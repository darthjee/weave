<?php

class Request
{
    public function request_method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function body()
    {
        return file_get_contents('php://input');
    }

    public function headers()
    {
        return getallheaders();
    }

    public function request_url()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $parts = parse_url($uri);
        return $parts['path'] ?? '/';
    }

    public function query()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $parts = parse_url($uri);
        return $parts['query'] ?? '';
    }
}
