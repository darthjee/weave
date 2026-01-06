<?php

namespace Weave\Proxy;

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
        // Check if request should be proxied to frontend
        if ($this->matchesFrontendRoute()) {
            // Proxy to frontend
            $handler = new ProxyRequest('http://frontend:8080');
        } else {
            $handler = new MissingRequestHandler();
        }

        return $handler->handle_request($this->request);
    }

    private function matchesFrontendRoute()
    {
        $matchers = [
            new RequestMatcher('GET', '/', 'exact'),
            new RequestMatcher('GET', '/assets/js/', 'begins_with'),
            new RequestMatcher('GET', '/assets/css/', 'begins_with'),
            new RequestMatcher('GET', '/@vite/', 'begins_with'),
            new RequestMatcher('GET', '/node_modules/', 'begins_with'),
            new RequestMatcher('GET', '/@react-refresh', 'exact')
        ];

        foreach ($matchers as $matcher) {
            if ($matcher->matches($this->request)) {
                return true;
            }
        }

        return false;
    }
}
