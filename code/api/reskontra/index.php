<?php

require '../../vendor/autoload.php';

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
$c = new \Slim\Container($configuration);

$app = new \Slim\App($c);

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:4200')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

require_once "f_reskontra.php";

$app->get('/asdf', function ($request, $response) {

	// return $response->withJson(sendInvoices(), 200, JSON_UNESCAPED_UNICODE);
    $body = $response->getBody();
    $body->write(sendInvoices());
    
    // return $response;
});


$app->run();