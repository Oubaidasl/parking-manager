<?php

use Classes\Consts;
use Classes\DB;
use Classes\Validators;
use Classes\App;



$query = App::resolve(DB::class);
$note = $query->executeQuery("select * from notes where id = :id", ['id' => $_POST['id']])->fetch(PDO::FETCH_ASSOC);



if (!$note) {
    abort();
}

if ( $note['owner_id'] != Consts::OWNER_ID) {
    abort(403);
}

$query->executeQuery("delete from notes where id = :id", ['id' => $_POST['id']]);
header('location: /notes');
exit();