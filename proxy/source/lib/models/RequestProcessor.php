<?php

class RequestProcessor {
    public static function handleRequest($request) {
        $requestUri = $request->request_url();
        $requestMethod = $request->request_method();

        // Check if request should be proxied to frontend
        if ($requestMethod === 'GET' && 
            ($requestUri == '/' || strpos($requestUri, '/assets/js/') === 0 || strpos($requestUri, '/assets/css/') === 0 || strpos($requestUri, '/@vite/') === 0 || strpos($requestUri, '/node_modules/') === 0 || $requestUri == '/@react-refresh')) {
            
            // Proxy to frontend
            $proxyRequest = new ProxyRequest('http://frontend:8080');
            return $proxyRequest->proxy_request($request);
        } else {
            return new MissingResponse();
        }
    }
}
