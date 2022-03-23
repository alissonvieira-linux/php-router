<?php

namespace App\Http;

class Request {
    private $request_uri;
    private $request_method;
    private $request_params = [];
    private $request_post = [];
    private $request_headers = [];

    public function __construct()
    {
       $this->request_uri = $_SERVER['REQUEST_URI'];
       $this->request_method = $_SERVER['REQUEST_METHOD'];
       $this->request_params = $_GET;
       $this->request_post = $_POST;
       $this->request_headers = getallheaders();
    }

    public function uri()
    {
        return $this->request_uri;
    }

    public function method()
    {
        return $this->request_method;
    }

    public function params()
    {
        return $this->request_params;
    }

    public function post()
    {
        return $this->request_post;
    }

    public function headers()
    {
        return $this->request_headers;
    }
}