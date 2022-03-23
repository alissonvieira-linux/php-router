<?php

require __DIR__.'/vendor/autoload.php';

use \App\Http\Request;
use \App\Http\Response;
use \App\Http\Router;

$router = new Router(DEFAULT_URL);

$router->get('/', function(Request $request, Response $response){
    $response->send('Test');
});

$router->run();
