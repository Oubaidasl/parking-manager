<?php 

function base_path ($path = '') {
    return BASE_PATH . $path;
}

function dd($var) {
    var_dump($var);
    die();
}

function abort ($statusCode = 404) {
    http_response_code($statusCode);
    require base_path("views/{$statusCode}.html");
} 

// Parse 'Y-m-d' to DateTimeImmutable (date-only)
function parseYmd(string $s): DateTimeImmutable {
  $dt = DateTimeImmutable::createFromFormat('!Y-m-d', $s); // '!' zeroes time [web:135]
  if (!$dt) {
    throw new InvalidArgumentException("Invalid date: $s");
  }
  return $dt;
}

// Format to 'Y-m-d'
function formatYmd(DateTimeImmutable $d): string {
  return $d->format('Y-m-d'); // [web:135]
}

// Add months with clamping to avoid skipping over last day of month
function addMonthsClamped(DateTimeImmutable $dt, int $months): DateTimeImmutable {
  $day = (int)$dt->format('j');
  // Move to first day of target month: first day of this month, then +N months
  $firstTarget = $dt->modify('first day of this month')->modify("+$months month"); // [web:135]
  $daysInTarget = (int)$firstTarget->format('t');
  $clampedDay = min($day, $daysInTarget);
  return $firstTarget->setDate(
    (int)$firstTarget->format('Y'),
    (int)$firstTarget->format('n'),
    $clampedDay
  );
}

// Validate RFID (basic check, adjust pattern according to your format)
function validate_rfid($rfid) {
    // Typical RFID: numeric/hex blocks (e.g., "12:FD:34:56")
    return preg_match('/^([A-Fa-f0-9]{2}:){1,}[A-Fa-f0-9]{2}$/', $rfid) === 1;
}

// Validate Full Name: at least two words, each starting with a capital letter
function validate_full_name($name) {
    // At least two words, each starts with uppercase followed by lowercase (supports accents)
    return preg_match('/^([A-Z][a-z]+)(\s+[A-Z][a-z]+)+$/u', trim($name)) === 1;
}

// Validate Moroccan phone number: starts with 05, 06, or 07, 10 digits in total
function validate_phone_number($phone) {
    return preg_match('/^(05|06|07)[0-9]{8}$/', $phone) === 1;
}

// Validate Email (uses PHP's built-in validator)
function validate_email($email) {
    if (!$email) return true;
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Validate Matricula: format "X-12-34567" (one capital, 2 digits, 5 digits)
function validate_matricula($matricula) {
    if (!$matricula) return true;
    return preg_match('/^[A-Z]-\d{2}-\d{5}$/', $matricula) === 1;
}

// Validate NID: one or two uppercase letters, followed by 6 digits (e.g. 'L160997' or 'AB123456')
function validate_nid($nid) {
    return preg_match('/^[A-Z]{1,2}\d{6}$/', $nid) === 1;
}
