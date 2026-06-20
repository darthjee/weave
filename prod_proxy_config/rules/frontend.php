<?php

use Tent\Configuration;
use Tent\Models\Rule;
use Tent\Handlers\FixedFileHandler;
use Tent\Handlers\ProxyRequestHandler;
use Tent\Handlers\StaticFileHandler;
use Tent\Models\Server;
use Tent\Models\FolderLocation;
use Tent\Models\RequestMatcher;

Configuration::buildRule([
    'handler' => [
        'type' => 'static',
        'location' => './static'
    ],
    'matchers' => [
        ['method' => 'GET', 'uri' => '/index.html', 'type' => 'exact'],
        ['method' => 'GET', 'uri' => '/assets', 'type' => 'begins_with'],
    ]
]);
Configuration::buildRule([
    'handler' => [
        'type' => 'static',
        'location' => './static'
    ],
    'matchers' => [
        ['method' => 'GET', 'uri' => '/', 'type' => 'exact'],
    ],
    "middlewares" => [
        [
          'class' => 'Tent\Middlewares\SetPathMiddleware',
          'path' => '/index.html'
        ]
    ]
]);
