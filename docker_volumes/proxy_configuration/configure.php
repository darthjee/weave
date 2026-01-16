<?php

use Tent\Configuration;
use Tent\Models\Rule;
use Tent\Handlers\FixedFileHandler;
use Tent\Handlers\ProxyRequestHandler;
use Tent\Handlers\StaticFileHandler;
use Tent\Models\Server;
use Tent\Models\FolderLocation;
use Tent\Models\RequestMatcher;

Configuration::addRule(
    new Rule(
        new StaticFileHandler(new FolderLocation('./static')),
        [
            new RequestMatcher('GET', '/index.html', 'exact'),
            new RequestMatcher('GET', '/assets', 'begins_with'),
        ]
    )
);
Configuration::addRule(
    new Rule(
        new FixedFileHandler('./static/index.html'),
        [
            new RequestMatcher('GET', '/', 'exact'),
        ]
    )
);

Configuration::addRule(
    new Rule(
        new ProxyRequestHandler(new Server('https://weave.tamanduati.tech/')),
        [
            new RequestMatcher('GET', '/api/', 'begins_with')
        ]
    )
);
