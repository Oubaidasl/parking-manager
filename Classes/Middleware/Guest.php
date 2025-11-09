<?php

namespace Classes\Middleware;

class Guest {
    public function handle () {
        if (isset($_SESSION['user'])) {
            header('Location: /');
            exit;
        }
    }
}