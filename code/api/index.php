<?php
// use \Psr\Http\Message\ServerRequestInterface as Request;
// use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$app = new \Slim\App;

require 'admin/index.php';

$app->get('/hello/{name}', function ($request, $response) {

    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, dfdfd $name");

    return $response;
});

$app->post('/account', function ($request, $response, $args) {

	require_once "f_account.php";

	$account = addAccount(json_decode($request->getBody()));

	return $response->withJson($account);
});

$app->get('/account', function ($request, $response, $args) {

	require_once "f_account.php";

	$accounts = getAccounts();

	return $response->withJson($accounts);
});

$app->get('/account/{id}', function ($request, $response, $args) {

	require_once "f_account.php";

	$account = getAccount($request->getAttribute('id'));

	return $response->withJson($account);
});

$app->get('/account/{account_id}/customer/{customer_id}', function ($request, $response) {

	require_once "f_account.php";

	$customers = getCustomers($request->getAttribute('customer_id'));

	return $response->withJson($customers);
});

$app->run();