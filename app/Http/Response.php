<?php

namespace App\Http;

class Response {
    private $status_code = 200;
    private $headers = [];
    private $contentType = 'text/html';

    public function status($code)
    {
        $this->status_code = $code;
    }

    private function sendHeaders()
    {
        http_response_code($this->status_code);

        foreach($this->headers as $key => $value) {
            header($key .':'. $value);
        }
    }

    public function json($data)
    {
        $this->content_type('application/json');
        $this->sendHeaders();
        echo json_encode($data);
    }

    public function send($content)
    {
        $this->sendHeaders();
        echo $content;
    }

    public function content_type($contentType)
    {
        $this->contentType = $contentType;
        $this->addHeader('Content-Type', $contentType);
    }

    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }
}