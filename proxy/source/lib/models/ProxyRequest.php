<?php

class ProxyRequest {
    private $targetHost;

    public function __construct($targetHost) {
        $this->targetHost = $targetHost;
    }

    public function proxy_request($request) {
        // Build full URL from target host and request path
        $url = $this->targetHost . $request->request_url();
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
}
