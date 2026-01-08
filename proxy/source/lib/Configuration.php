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

Configuration::addTarget(
    new ProxyTarget(
        new ProxyRequestHandler(new Server('http://frontend:8080')),
        [
            new RequestMatcher('GET', '/', 'exact'),
            new RequestMatcher('GET', '/assets/images/', 'begins_with'),
            new RequestMatcher('GET', '/assets/js/', 'begins_with'),
            new RequestMatcher('GET', '/assets/css/', 'begins_with'),
            new RequestMatcher('GET', '/@vite/', 'begins_with'),
            new RequestMatcher('GET', '/node_modules/', 'begins_with'),
            new RequestMatcher('GET', '/@react-refresh', 'exact')
        ]
    )
);
