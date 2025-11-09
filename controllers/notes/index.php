<?php

use Classes\Consts;
use Classes\DB;
use Classes\Validators;
use Classes\App;


$query = App::resolve(DB::class);


base_path("views/notes/index.view.php", ['posts' => $query->executeQuery("select * from notes where owner_id = " . Consts::OWNER_ID . ";")->fetchAll(PDO::FETCH_ASSOC)]);