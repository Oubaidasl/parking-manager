<?php

namespace Classes\Middleware;
use Exception;

class Middleware {
    public const MAP = [
        'Auth' => Auth::class,
        'Guest' => Guest::class
    ];

    public static function resolve($key)
    {
        if (!$key) {
            return null;
        }

        if (!isset(self::MAP[$key])) {
            throw new Exception("No matching middleware found for key '{$key}'.");
        }

        $class = self::MAP[$key];
        return (new $class())->handle();
    }
}