<?php

namespace Tent;

class Configuration
{
    private static $targets = [];

    public static function addTarget($target)
    {
        self::$targets[] = $target;
    }

    public static function getTargets()
    {
        return array_merge(
            self::$targets,
            [new ProxyTarget(new MissingRequestHandler())]
        );
    }
}
