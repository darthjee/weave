<?php

require_once __DIR__ . '/lib/models/Response.php';
require_once __DIR__ . '/lib/models/MissingResponse.php';
require_once __DIR__ . '/lib/models/Request.php';
require_once __DIR__ . '/lib/models/ProxyRequest.php';

function send_response($response) {
    http_response_code($response->httpCode);
    foreach ($response->headerLines as $header) {
        header($header);
    }
    echo $response->body;
}

// Get the request URI and method
$request = new Request();
$requestUri = $request->request_url();
$requestMethod = $request->request_method();

// Check if request should be proxied to frontend
if ($requestMethod === 'GET' && 
    ($requestUri == '/' || strpos($requestUri, '/assets/js/') === 0 || strpos($requestUri, '/assets/css/') === 0 || strpos($requestUri, '/@vite/') === 0 || strpos($requestUri, '/node_modules/') === 0 || $requestUri == '/@react-refresh')) {
    
    // Proxy to frontend
    $proxyRequest = new ProxyRequest('http://frontend:8080');
    $response = $proxyRequest->proxy_request($request);
} else {
    $response = new MissingResponse();
}

send_response($response);
