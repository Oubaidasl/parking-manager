<?php

use Classes\Consts;
use Classes\DB;
use Classes\Validators;
use Classes\App;





$id = '';
$error = ['body' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $error['body'] = $errors['body'];
} else {
    $id = $_GET['id'];
}

$query = App::resolve(DB::class);
$note = $query->executeQuery("select * from notes where id = :id", ['id' => $id])->fetch(PDO::FETCH_ASSOC);

if (!$note) {
    abort();
}

if ( $note['owner_id'] != Consts::OWNER_ID) {
    abort(403);
}



base_path("views/notes/edit.view.php", ['note' => $note, 'title' => "Note " . $id, 'id' => $id, 'errors' => $error]);
