<?php

namespace Weave\Proxy;

interface HttpClientInterface
{
    public function request($url, $headers);
}
