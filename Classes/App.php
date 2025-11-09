<?php

namespace Classes;

class App
{
    public static $container;

    public static function setContainer(Container $container)
    {
        static::$container = $container;
    }

    public static function container()
    {
        return static::$container;
    }

    public static function resolve(string $key)
    {
        return static::$container->resolve($key);
    }

    public static function bind(string $key, callable $resolver)
    {
        static::$container->bind($key, $resolver);
    }
}