<?php

require_once __DIR__ . '/lib/models/Response.php';
require_once __DIR__ . '/lib/models/MissingResponse.php';
require_once __DIR__ . '/lib/models/Request.php';

function proxy_request($request, $targetHost) {
    // Build full URL from target host and request path
    $url = $targetHost . $request->request_url();
    if ($request->query()) {
        $url .= '?' . $request->query();
    }
    
    // Get all request headers
    $requestHeaders = $request->headers();
    $headers = [];
    foreach ($requestHeaders as $name => $value) {
        $headers[] = "$name: $value";
    }
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    
    curl_close($ch);
    
    // Parse headers into array
    $headerLines = explode("\n", $headers);
    $headerLines = array_map('trim', $headerLines);
    $headerLines = array_filter($headerLines, function($header) {
        return !empty($header) && strpos($header, 'HTTP/') !== 0;
    });
    
    return new Response($body, $httpCode, $headerLines);
}

// Get the request URI and method
$request = new Request();
$requestUri = $request->request_url();
$requestMethod = $request->request_method();

// Check if request should be proxied to frontend
if ($requestMethod === 'GET' && 
    ($requestUri == '/' || strpos($requestUri, '/assets/js/') === 0 || strpos($requestUri, '/assets/css/') === 0 || strpos($requestUri, '/@vite/') === 0 || strpos($requestUri, '/node_modules/') === 0 || $requestUri == '/@react-refresh')) {
    
    // Proxy to frontend
    $response = proxy_request($request, 'http://frontend:8080');
    
    // Forward response
    http_response_code($response->httpCode);
    foreach ($response->headerLines as $header) {
        header($header);
    }
    echo $response->body;
} else {
    $response = new MissingResponse();
    http_response_code($response->httpCode);
    foreach ($response->headerLines as $header) {
        header($header);
    }
    echo $response->body;
}
