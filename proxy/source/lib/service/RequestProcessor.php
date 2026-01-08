<?php

namespace Tent;

class RequestProcessor
{
    private $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public static function handleRequest($request)
    {
        return (new RequestProcessor($request))->handle();
    }

    public function handle()
    {
        $targets = [
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
            ),
            new ProxyTarget(new MissingRequestHandler())
        ];

        foreach ($targets as $target) {
            if ($target->match($this->request)) {
                return $target->handleRequest($this->request);
            }
        }
    }
}
