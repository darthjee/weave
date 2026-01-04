<?php

// Get the request URI and method
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Check if request should be proxied to frontend
if ($requestMethod === 'GET' && 
    (strpos($requestUri, '/') === 0 || strpos($requestUri, '/assets/js/') === 0 || strpos($requestUri, '/assets/css/') === 0)) {
    
    // Proxy to frontend
    $frontendUrl = 'http://frontend:8080' . $requestUri;
    
    $ch = curl_init($frontendUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Host: localhost:3000',
        'Accept: */*'
    ]);
    
    $response = curl_exec($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    
    curl_close($ch);
    
    // Forward response headers
    http_response_code($httpCode);
    $headerLines = explode("\n", $headers);
    foreach ($headerLines as $header) {
        $header = trim($header);
        if (!empty($header) && strpos($header, 'HTTP/') !== 0) {
            header($header);
        }
    }
    
    // Return response body
    echo $body;
} else {
    // Return the path as plain text
    header('Content-Type: text/plain');
    echo $requestUri;
}
