<?php

namespace Weave\Proxy;

class MissingRequestHandler
{
    public function handleRequest($request)
    {
        return new MissingResponse();
    }
}
