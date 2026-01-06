<?php

class RequestMatcher {
    private $requestMethod;
    private $requestUri;
    private $matchType;

    public function __construct($requestMethod = NULL, $requestUri = NULL, $matchType = 'exact') {
        $this->requestMethod = $requestMethod;
        $this->requestUri = $requestUri;
        $this->matchType = $matchType;
    }

    public function matches($request) {
        if ($request->request_method() !== $this->requestMethod) {
            return false;
        }

        $requestUrl = $request->request_url();

        if ($this->matchType === 'exact') {
            return $requestUrl === $this->requestUri;
        } elseif ($this->matchType === 'begins_with') {
            return strpos($requestUrl, $this->requestUri) === 0;
        }

        return false;
    }
}
