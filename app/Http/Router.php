<?php

namespace App\Http;

use \Exception;
use \Closure;

class Router {
    private $routes = [];
    private $url = '';
    private $prefix = '';
    private $request;
    private $response;

    public function __construct($url)
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->url = $url;
        $this->setPrefix();
    }

    private function setPrefix()
    {
        $url = parse_url($this->url);
        $this->prefix = $url['path'] ?? '';
    }

    private function addRoute($method, $route, $callback, $params = [])
    {
        $params['variables'] = [];
        $patternVariable = '/{(.*?)}/';
        if(preg_match_all($patternVariable, $route, $matches)) {
           $route = preg_replace($patternVariable, '(.*?)', $route);
           $params['variables'] = $matches[1];
        }

        $patternRoute = '/^'.str_replace('/', '\/', $route).'$/';
        $this->routes[$patternRoute][$method] = [
            $callback,
            $params
        ];
    }

    private function getUri()
    {
        $uri = $this->request->uri();
        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];

        return end($xUri);
    }

    private function getRoute()
    {
        $uri = $this->getUri();
        $method = $this->request->method();

        // Valida a rota e o método
        foreach($this->routes as $patternRoute => $methods) {
            if(preg_match($patternRoute, $uri, $matches)) {
                if(isset($methods[$method])) {
                    unset($matches[0]);

                    $keys = $methods[$method][1]['variables'];
                    $methods[$method][1]['variables'] = array_combine($keys, $matches);

                    return $methods[$method];
                }

                throw new Exception('Método não permitido.', 405);
            }
        }

        throw new Exception('URL não encontrada.', 404);
    }

    public function run()
    {
        try {
            $route = $this->getRoute();

            if(!isset($route[0]) || !$route[0] instanceof Closure) {
                throw new Exception('A URL não pôde ser processada.', 500);
            }

            $args = [
                $this->request,
                $this->response,
                $route[1]['variables'] ?? [],
            ];

            return call_user_func_array($route[0], $args);

        } catch(Exception $e) {
            $this->response->status($e->getCode());
            $this->response->send($e->getMessage());
        }
    }

    public function get($uri, $callback, $params = [])
    {
        $this->addRoute('GET', $uri, $callback, $params);
    }

    public function post($uri, $callback, $params = [])
    {
        $this->addRoute('POST', $uri, $callback, $params);
    }

    public function put($uri, $callback, $params = [])
    {
        $this->addRoute('PUT', $uri, $callback, $params);
    }

    public function delete($uri, $callback, $params = [])
    {
        $this->addRoute('DELETE', $uri, $callback, $params);
    }
}