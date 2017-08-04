<?php
// use \Psr\Http\Message\ServerRequestInterface as Request;
// use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

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



require 'admin/index.php';

$app->post('/login', function ($request, $response, $args) {

    require_once "f_account.php";

    $account = login(json_decode($request->getBody()));

    return $response->withJson($account);
});

$app->post('/account', function ($request, $response, $args) {

	require_once "f_account.php";

	$account = addAccount(json_decode($request->getBody()));

	return $response->withJson($account);
});

$app->put('/account/{id}', function ($request, $response, $args) {

    require_once "f_account.php";

    $account = updateAccount(json_decode($request->getBody()));

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

$app->post('/person/{account_id}', function ($request, $response) {

    require_once "f_account.php";

    $accountId = $request->getAttribute("account_id");

// var_dump(json_decode($request->getBody()));
    return $response->withJson(addSwimmer($accountId, json_decode($request->getBody())));
});

$app->put('/person/{account_id}/{person_id}', function ($request, $response) {

    require_once "f_account.php";

    $accountId = $request->getAttribute("account_id");
    $personId = $request->getAttribute("person_id");
    $swimmer = json_decode($request->getBody());

// var_dump(json_decode($request->getBody()));
    return $response->withJson(updateSwimmer($swimmer, $accountId));
});

$app->get('/course/current', function($request, $response, $args){

    require_once 'f_account.php';

    $res = getCurrentCourses();

    $courses = [];
    $events = [];

    while ($current = current($res)) {

        $event['id'] = $current['event_id'];
        $event['start_date'] = $current['start_date'];
        $event['end_date'] = $current['end_date'];
        $event['weekday'] = $current['weekday'];
        $event['start_time'] = $current['start_time'];
        $event['end_time'] = $current['end_time'];
        $event['place'] = $current['place'];
        $event['address'] = $current['address'];
        $event['price'] = $current['price'];
        $event['notes'] = $current['notes'];
        $event['max_participants'] = $current['max_participants'];
        $event['current_participants'] = $current['current_participants'];


        $events[] = $event;

        $next = next($res);
        
        if (false === $next || $next['course_id'] !== $current['course_id']) {
            //do something with $current
            $course['id'] = $current['course_id'];
            $course['name'] = $current['course_name'];
            $course['description'] = $current['course_description'];
            $course['events'] = $events;

            $courses[] = $course;

            $events = [];
        }
    }

    return $response->withJson($courses);
});

$app->post('/enroll/{account_id}', function($request, $response, $args) {

    require_once "f_account.php";

    $accountId = $request->getAttribute("account_id");
    $enrollments = json_decode($request->getBody());

// var_dump($enrollments);
    // return $response->withJson(updateSwimmer($swimmer, $accountId));
    return $response->withJson(saveEnrollments($accountId, $enrollments));
});

$app->get('/enroll/{account_id}', function($request, $response, $args){
    
    require_once "f_account.php";

    $accountId = $request->getAttribute("account_id");

    $er = [];
    $enrollments = getCurrentEnrollments($accountId);

// e.id event_id, e.weekday, e.start_time, e.end_time, e.price, pl.name place, pl.address,
// pe.id person_id, pe.account_id, pe.first_name, pe.last_name, pe.birthday, true isMember, pe.notes


    foreach ($enrollments as $e) {

        $obj = [];

        $course = [];
        $course['id'] = $e['course_id'];
        $course['name'] = $e['course_name'];
        $course['description'] = $e['course_description'];
        
        $obj['course'] = $course;

        $event = [];
        $event['id'] = $e['event_id'];
        $event['weekday'] = $e['weekday'];
        $event['start_time'] = $e['start_time'];
        $event['end_time'] = $e['end_time'];
        $event['start_date'] = $e['start_date'];
        $event['end_date'] = $e['end_date'];
        $event['price'] = $e['price'];
        $event['place'] = $e['place'];
        $event['address'] = $e['address'];

        $obj['event'] = $event;

        $swimmer = [];

        $swimmer['id'] = $e['person_id'];
        $swimmer['first_name'] = $e['first_name'];
        $swimmer['last_name'] = $e['last_name'];
        $swimmer['birthday'] = $e['birthday'];
        $swimmer['isMember'] = $e['isMember'];
        $swimmer['notes'] = $e['notes'];

        $obj['swimmer'] = $swimmer;

        $er[] = $obj;

    }
    
    return $response->withJson($er);    
});









$app->run();