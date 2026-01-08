<?php

namespace Tent;

class ProxyTarget
{
    private $handler;
    private $matchers;

    public function __construct($handler, $matchers = [])
    {
        $this->handler = $handler;
        $this->matchers = $matchers;
    }

    public function match($request)
    {
        foreach ($this->matchers as $matcher) {
            if ($matcher->matches($request)) {
                return true;
            }
        }

        return false;
    }

    public function handleRequest($request)
    {
        return $this->handler->handleRequest($request);
    }
}
