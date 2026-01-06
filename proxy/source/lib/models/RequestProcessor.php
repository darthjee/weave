<?php

class RequestProcessor {
    private $request;

    public function __construct($request) {
        $this->request = $request;
    }

    public static function handleRequest($request) {
        return (new RequestProcessor($request))->handle();
    }

    public function handle() {
        // Check if request should be proxied to frontend
        if ($this->matchesFrontendRoute()) {
            // Proxy to frontend
            $handler = new ProxyRequest('http://frontend:8080');
        } else {
            $handler = new MissingRequestHandler();
        }

        return $handler->handle_request($this->request);
    }

    private function matchesFrontendRoute() {
        $requestUri = $this->request->request_url();
        $requestMethod = $this->request->request_method();

        return $requestMethod === 'GET' && 
            ($requestUri == '/' || strpos($requestUri, '/assets/js/') === 0 || strpos($requestUri, '/assets/css/') === 0 || strpos($requestUri, '/@vite/') === 0 || strpos($requestUri, '/node_modules/') === 0 || $requestUri == '/@react-refresh');
    }
}
