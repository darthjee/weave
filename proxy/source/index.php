<?php

use Weave\Proxy\Request;
use Weave\Proxy\RequestProcessor;

require_once __DIR__ . '/lib/models/Response.php';
require_once __DIR__ . '/lib/models/MissingResponse.php';
require_once __DIR__ . '/lib/models/Request.php';
require_once __DIR__ . '/lib/models/ProxyRequest.php';
require_once __DIR__ . '/lib/models/MissingRequestHandler.php';
require_once __DIR__ . '/lib/models/RequestMatcher.php';
require_once __DIR__ . '/lib/models/RequestProcessor.php';

function send_response($response)
{
    http_response_code($response->httpCode);
    foreach ($response->headerLines as $header) {
        header($header);
    }
    echo $response->body;
}

$request = new Request();
$response = RequestProcessor::handleRequest($request);

send_response($response);
