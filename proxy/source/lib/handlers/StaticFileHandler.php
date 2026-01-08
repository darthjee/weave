<?php

namespace Tent;

class StaticFileHandler implements RequestHandler
{
    private $folderLocation;

    public function __construct($folderLocation)
    {
        $this->folderLocation = $folderLocation;
    }

    public function handleRequest($request)
    {
        $filePath = $this->folderLocation->basePath() . $request->requestUrl();

        if (!file_exists($filePath) || !is_file($filePath)) {
            return new MissingResponse();
        }

        $content = file_get_contents($filePath);
        $contentType = $this->getContentType($filePath);

        return new Response(
            $content,
            200,
            ["Content-Type: $contentType"]
        );
    }

    private function getContentType($filePath)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        
        $mimeTypes = [
            'html' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'txt' => 'text/plain'
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
}
