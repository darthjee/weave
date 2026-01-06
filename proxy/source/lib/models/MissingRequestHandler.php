<?php

class MissingRequestHandler
{
    public function handle_request($request)
    {
        return new MissingResponse();
    }
}
