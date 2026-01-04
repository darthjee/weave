<?php

// Get the request URI
$requestUri = $_SERVER['REQUEST_URI'];

// Return the path as plain text
header('Content-Type: text/plain');
echo $requestUri;
