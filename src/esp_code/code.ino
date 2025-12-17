#include <WiFi.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <ESP32Servo.h>
#include <ArduinoJson.h>

// ==================== WiFi Configuration ====================
const char* ssid     = "iPhone de Omar";
const char* password = "123456789";

// ==================== Server Configuration ====================
const char* serverURL = "http://172.20.10.2";
const char* apiKey    = "ESP32_RFID_2025_KEY123";

// ==================== RFID Pins (Your Setup) ====================
#define SCK_PIN     14
#define MISO_PIN    12
#define MOSI_PIN    13
#define SS_PIN      15
#define RST_PIN     4

// ==================== Gate & Sensors ====================
#define GATE_SERVO      10
#define ENTRANCE_IR     5

// ==================== Parking Spots (3 spots) ====================
#define SPOT1_IR    20
#define SPOT2_IR    21
#define SPOT3_IR    35

// ==================== LEDs ====================
#define LED_GREEN   19
#define LED_RED     18

// ==================== Objects ====================
MFRC522 rfid(SS_PIN, RST_PIN);
Servo gate;

// ==================== Variables ====================
const int TOTAL_SPOTS = 3;
bool spotStatus[TOTAL_SPOTS] = {true, true, true};   // true = EMPTY
int  spotIRPins[TOTAL_SPOTS] = {SPOT1_IR, SPOT2_IR, SPOT3_IR};

String validRFIDs[100];
String validNIDs[100];
int    rfidCount = 0;

// --------- gate & IR helpers ----------
int currentGateAngle = 90;  // 90 = closed, 270 = open

void setGate(int angle) {
  gate.write(angle);
  currentGateAngle = angle;
}

bool gateIsOpen()   { return currentGateAngle == 270; }
bool gateIsClosed() { return currentGateAngle == 90;  }

// IR sensor: LOW = vehicle present
bool isCarPresent() {
  return digitalRead(ENTRANCE_IR) == LOW;
}

void waitForCarToPass() {
  // Wait until car leaves IR beam (max 15 s to avoid blocking forever)
  unsigned long t0 = millis();
  while (isCarPresent() && (millis() - t0 < 15000)) {
    yield();
    delay(50);
  }
}

// Count free spots (EMPTY = true)
int getFreeSpots() {
  int freeCount = 0;
  for (int i = 0; i < TOTAL_SPOTS; i++) {
    if (spotStatus[i]) freeCount++;
  }
  return freeCount;
}
// -------------------------------------------

// ==================== SETUP ====================
void setup() {
  Serial.begin(115200);
  delay(2000);  // Important for ESP32-S3
  
  Serial.println("\n========================================");
  Serial.println("   SMART PARKING SYSTEM - ESP32-S3");
  Serial.println("========================================\n");

  // Initialize pins
  pinMode(ENTRANCE_IR, INPUT);
  pinMode(SPOT1_IR, INPUT);
  pinMode(SPOT2_IR, INPUT);
  pinMode(SPOT3_IR, INPUT);
  pinMode(LED_GREEN, OUTPUT);
  pinMode(LED_RED, OUTPUT);
  Serial.println("[OK] Pins initialized");

  // Initialize SPI for RFID
  SPI.begin(SCK_PIN, MISO_PIN, MOSI_PIN, SS_PIN);
  pinMode(SS_PIN, OUTPUT);
  digitalWrite(SS_PIN, HIGH);
  
  rfid.PCD_Init();
  delay(100);

  // Check RFID
  byte version = rfid.PCD_ReadRegister(MFRC522::VersionReg);
  if (version == 0x00 || version == 0xFF) {
    Serial.println("[WARNING] RFID not detected - check wiring");
  } else {
    Serial.print("[OK] RFID Reader - Firmware: 0x");
    Serial.println(version, HEX);
  }

  // Initialize Servo
  gate.attach(GATE_SERVO);
  setGate(90);   // 90 = CLOSED
  Serial.println("[OK] Gate servo (CLOSED @ 90)");

  // Connect WiFi
  connectWiFi();

  // BOOT:  Fetch valid RFID cards
  Serial.println("\n[BOOT] Fetching allowed clients...");
  fetchValidRFIDs();

  // BOOT:  Read and send spot status
  Serial.println("[BOOT] Reading spot status...");
  readAndSendSpotStatus();

  Serial.print("[BOOT] Free spots: ");
  Serial.println(getFreeSpots());

  Serial.println("\n========================================");
  Serial.println("   SYSTEM READY");
  Serial.println("========================================\n");
}

// ==================== MAIN LOOP ====================
void loop() {
  yield();  // Feed watchdog
  
  // Check for RFID card (no need for IR sensor trigger)
  checkRFID();

  // Exit logic using IR only
  if (gateIsClosed() && isCarPresent()) {
    Serial.println("[EXIT] Car detected at closed gate -> opening");
    setGate(270);                       // open

    Serial.println("[EXIT] Waiting for car to pass...");
    waitForCarToPass();                 // wait until IR is clear

    Serial.println("[EXIT] Car passed -> closing");
    setGate(90);                        // close
  }

  // Check spots for changes
  checkSpots();

  delay(100);
}

// ==================== WiFi ====================
void connectWiFi() {
  Serial.print("[WIFI] Connecting");
  WiFi.begin(ssid, password);

  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED && attempts < 30) {
    delay(500);
    Serial.print(".");
    yield();
    attempts++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println(" OK!");
    Serial.print("[WIFI] IP:  ");
    Serial.println(WiFi.localIP());
  } else {
    Serial.println(" FAILED!");
  }
}

// ==================== RFID ====================
void fetchValidRFIDs() {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("[ERROR] No WiFi");
    return;
  }

  HTTPClient http;
  String url = String(serverURL) + "/esp-read?key=" + apiKey;

  http.begin(url);
  http.setTimeout(10000);
  int httpCode = http.GET();

  if (httpCode == 200) {
    String payload = http.getString();
    
    DynamicJsonDocument doc(4096);
    DeserializationError error = deserializeJson(doc, payload);

    if (!error && doc["ok"] == true) {
      JsonArray rfids = doc["rfids"];
      rfidCount = 0;

      for (JsonObject item : rfids) {
        if (rfidCount < 100) {
          validNIDs[rfidCount]  = item["NID"].as<String>();
          validRFIDs[rfidCount] = item["RFID"].as<String>();
          rfidCount++;
        }
      }

      Serial.print("[OK] Loaded ");
      Serial.print(rfidCount);
      Serial.println(" clients:");
      
      for (int i = 0; i < rfidCount; i++) {
        Serial.print("     ");
        Serial.print(validNIDs[i]);
        Serial.print(" :  ");
        Serial.println(validRFIDs[i]);
      }
    }
  } else {
    Serial.print("[ERROR] HTTP:  ");
    Serial.println(httpCode);
  }

  http.end();
}

void checkRFID() {
  if (!rfid.PICC_IsNewCardPresent()) {
    return;
  }

  if (!rfid.PICC_ReadCardSerial()) {
    return;
  }

  // Get card UID
  String cardUID = "";
  for (byte i = 0; i < rfid.uid.size; i++) {
    if (rfid.uid.uidByte[i] < 0x10) cardUID += "0";
    cardUID += String(rfid.uid.uidByte[i], HEX);
  }
  cardUID.toUpperCase();

  Serial.println("\n----------------------------------------");
  Serial.print("[RFID] Card:  ");
  Serial.println(cardUID);

  // Validate card
  String nid = "";
  for (int i = 0; i < rfidCount; i++) {
    if (validRFIDs[i].equalsIgnoreCase(cardUID)) {
      nid = validNIDs[i];
      break;
    }
  }

  if (nid != "") {
    // ALLOWED (by card) – now check capacity
    int freeSpots = getFreeSpots();
    Serial.print("[PARKING] Free spots: ");
    Serial.println(freeSpots);

    if (freeSpots <= 0) {
      Serial.println("[PARKING] FULL -> ACCESS BLOCKED");

      digitalWrite(LED_GREEN, LOW);
      digitalWrite(LED_RED, HIGH);
      delay(2000);
      digitalWrite(LED_RED, LOW);

      Serial.println("----------------------------------------\n");
      rfid.PICC_HaltA();
      rfid.PCD_StopCrypto1();
      return; // Do NOT open gate
    }

    Serial.println("[ACCESS] *** ALLOWED ***");
    Serial.print("[ACCESS] NID: ");
    Serial.println(nid);

    digitalWrite(LED_GREEN, HIGH);
    digitalWrite(LED_RED, LOW);

    // ENTRY SERVO + IR LOGIC
    Serial.println("[GATE] Opening (ENTRY)...");
    setGate(270);

    // Log entry to server
    sendEntryLog(nid);

    // Wait for car to arrive at IR (optional safety)
    Serial.println("[GATE] Waiting for car to reach IR...");
    unsigned long t0 = millis();
    while (!isCarPresent() && (millis() - t0 < 15000)) {  // max 15 s to arrive
      yield();
      delay(50);
    }

    // Now wait for it to pass and clear IR
    Serial.println("[GATE] Waiting for car to pass...");
    waitForCarToPass();

    // Close gate (90 = CLOSED)
    Serial.println("[GATE] Closing (ENTRY)...");
    setGate(90);

    digitalWrite(LED_GREEN, LOW);

  } else {
    // DENIED
    Serial.println("[ACCESS] *** DENIED ***");
    
    digitalWrite(LED_RED, HIGH);
    digitalWrite(LED_GREEN, LOW);
    
    delay(2000);
    
    digitalWrite(LED_RED, LOW);
  }

  Serial.println("----------------------------------------\n");

  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();
}

void sendEntryLog(String nid) {
  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;
  String url = String(serverURL) + "/esp-create";

  http.begin(url);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  http.setTimeout(5000);

  String postData = "key=" + String(apiKey) + "&nid=" + nid;
  int httpCode = http.POST(postData);

  if (httpCode == 200) {
    Serial.println("[LOG] Entry saved to server");
  } else {
    Serial.print("[ERROR] Log failed: ");
    Serial.println(httpCode);
  }

  http.end();
}

// ==================== PARKING SPOTS ====================
void checkSpots() {
  bool changed = false;

  for (int i = 0; i < TOTAL_SPOTS; i++) {
    bool isEmpty = digitalRead(spotIRPins[i]) == HIGH;

    if (spotStatus[i] != isEmpty) {
      spotStatus[i] = isEmpty;
      changed = true;

      Serial.print("[SPOT] Spot ");
      Serial.print(i + 1);
      Serial.println(isEmpty ? " EMPTY" : " OCCUPIED");
    }
  }

  if (changed) {
    sendSpotUpdate();
  }
}

void readAndSendSpotStatus() {
  for (int i = 0; i < TOTAL_SPOTS; i++) {
    spotStatus[i] = digitalRead(spotIRPins[i]) == HIGH;
    Serial.print("     Spot ");
    Serial.print(i + 1);
    Serial.println(spotStatus[i] ?  ":  EMPTY" : ":  OCCUPIED");
  }

  Serial.print("[BOOT] Free spots: ");
  Serial.println(getFreeSpots());

  sendSpotUpdate();
}

void sendSpotUpdate() {
  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;
  String url = String(serverURL) + "/esp-update";

  http.begin(url);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  http.setTimeout(5000);

  String postData = "key=" + String(apiKey);
  for (int i = 0; i < TOTAL_SPOTS; i++) {
    postData += "&spot" + String(i + 1) + "=" + String(spotStatus[i] ? 1 : 0);
  }

  Serial.print("[SPOTS] Sending:  ");
  Serial.println(postData);

  int httpCode = http.POST(postData);

  if (httpCode == 200) {
    Serial.println("[SPOTS] Server updated");
  } else {
    Serial.print("[ERROR] Update failed: ");
    Serial.println(httpCode);
  }

  http.end();
}
