<?php

namespace Weave\Proxy;

class CurlUtils
{
    public static function buildHeaderLines($headers)
    {
        $headerLines = [];
        foreach ($headers as $name => $value) {
            $headerLines[] = "$name: $value";
        }
        return $headerLines;
    }
}
