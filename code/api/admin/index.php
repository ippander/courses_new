<?php

require_once "f_course.php";




/*********** PLACE ***********/

$app->get('/admin/place', function ($request, $response) {

	$stmt = pdo()->prepare("SELECT * FROM place");
	$stmt->execute();

	return $response->withJson($stmt->fetchAll(), 200, JSON_UNESCAPED_UNICODE);
});

$app->get('/admin/place/{id}', function ($request, $response) {

	$id = $request->getAttribute("id");

	$stmt = pdo()->prepare("SELECT * FROM place WHERE id = ?");
	$stmt->execute([$id]);
	
	return $response->withJson($stmt->fetch(), 200, JSON_UNESCAPED_UNICODE);
});

$app->post('/admin/place', function ($request, $response) {

	$added = addPlace(json_decode($request->getBody()));

	return $response->withJson($added, 201, JSON_UNESCAPED_UNICODE);
});

$app->put('/admin/place/{id}', function ($request, $response) {

	$id = $request->getAttribute("id");
	$obj = json_decode($request->getBody());
	
	$pdo = pdo();

	$stmt = $pdo->prepare("
		UPDATE place SET name = ?
		WHERE id = ?
	");

	$stmt->execute([$obj->name, $id]);

	return $response->withJson($obj, 200, JSON_UNESCAPED_UNICODE);
});

$app->delete('/admin/place/{id}', function ($request, $response) {

	$id = $request->getAttribute("id");
	$pdo = pdo();

	$stmt = $pdo->prepare("
		DELETE FROM place
		WHERE id = ?
	");

	$stmt->execute([$id]);

	return $response->withStatus(204);
});




/******* SEASON ***********/

$app->get('/admin/season', function ($request, $response) {

	$stmt = pdo()->prepare("SELECT * FROM season");
	$stmt->execute();

	return $response->withJson($stmt->fetchAll(), 200, JSON_UNESCAPED_UNICODE);
});

$app->get('/admin/season/{id}', function ($request, $response) {

	$id = $request->getAttribute("id");

	$stmt = pdo()->prepare("SELECT * FROM season WHERE id = ?");
	$stmt->execute([$id]);
	
	return $response->withJson($stmt->fetch(), 200, JSON_UNESCAPED_UNICODE);
});

$app->post('/admin/season', function ($request, $response) {

	$obj = json_decode($request->getBody());
	$pdo = pdo();
	
	$stmt = $pdo->prepare("
		INSERT INTO season (name, season_start, season_end)
		VALUES (?, ?, ?)
	");

	$stmt->execute([$obj->name, $obj->season_start, $obj->season_end]);

	$obj->id = $pdo->lastInsertId();

	return $response->withJson($obj, 201, JSON_UNESCAPED_UNICODE);
});

$app->put('/admin/season/{id}', function ($request, $response) {

	$id = $request->getAttribute("id");
	$obj = json_decode($request->getBody());
	
	$pdo = pdo();

	$stmt = $pdo->prepare("
		UPDATE season SET name = ?, season_start = ?, season_end = ?
		WHERE id = ?
	");

	$stmt->execute([$obj->name, $obj->season_start, $obj->season_end, $id]);

	return $response->withJson($obj, 200, JSON_UNESCAPED_UNICODE);
});

$app->delete('/admin/season/{id}', function ($request, $response) {

	$id = $request->getAttribute("id");
	$pdo = pdo();

	$stmt = $pdo->prepare("
		DELETE FROM season
		WHERE id = ?
	");

	$stmt->execute([$id]);

	return $response->withStatus(204);
});





/********* PERIOD **********/

$app->get('/admin/period', function ($request, $response) {


	return $response->withJson(getPeriods(), 200, JSON_UNESCAPED_UNICODE);
});

$app->post('/admin/period', function ($request, $response) {


	$added = addPeriod(json_decode($request->getBody()));

	return $response->withJson($added, 201, JSON_UNESCAPED_UNICODE);
});




/********** COURSE ************/

$app->get('/admin/course', function ($request, $response) {


	return $response->withJson(getCourses(), 200, JSON_UNESCAPED_UNICODE);
});

$app->get('/admin/course/{id}', function ($request, $response) {


	return $response->withJson(getCourse($request->getAttribute("id")), 200, JSON_UNESCAPED_UNICODE);
});

$app->post('/admin/course', function ($request, $response) {


	$added = addCourse(json_decode($request->getBody()));

	return $response->withJson($added, 201, JSON_UNESCAPED_UNICODE);
});

$app->put('/admin/course/{id}', function ($request, $response) {

	$updated = updateCourse(json_decode($request->getBody()), $request->getAttribute("id"));

	return $response->withJson($updated, 200, JSON_UNESCAPED_UNICODE);
});

$app->delete('/admin/course/{id}', function ($request, $response) {

	$added = deleteCourse($request->getAttribute("id"));

	return $response->withStatus(204);
});





/********* EVENT ************/

$app->get('/admin/event', function ($request, $response) {

	$stmt = pdo()->prepare("SELECT * FROM event");
	$stmt->execute();

	return $response->withJson($stmt->fetchAll(), 200, JSON_UNESCAPED_UNICODE);

});

$app->get('/admin/event/{id}', function ($request, $response) {

	$id = $request->getAttribute("id");

	$stmt = pdo()->prepare("SELECT * FROM event WHERE id = ?");
	$stmt->execute([$id]);
	
	return $response->withJson($stmt->fetch(), 200, JSON_UNESCAPED_UNICODE);
});

$app->post('/admin/event', function ($request, $response) {

	$obj = json_decode($request->getBody());
	$pdo = pdo();
	
	$stmt = $pdo->prepare("
		INSERT INTO event (product_id, season_id, place_id, start_time, end_time,
			regstartdate, start_date, max_participants, price, member_price)
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
	");

	$stmt->execute([
			$obj->product_id,
			$obj->season_id,
			$obj->place_id,
			$obj->start_time,
			$obj->end_time,
			$obj->regstartdate,
			$obj->start_date,
			$obj->max_participants,
			$obj->price,
			$obj->member_price
		]);

	$obj->id = $pdo->lastInsertId();

	return $response->withJson($obj, 201, JSON_UNESCAPED_UNICODE);
});

$app->put('/admin/event/{id}', function ($request, $response) {

	$id = $request->getAttribute("id");
	$obj = json_decode($request->getBody());
	
	$pdo = pdo();

	$stmt = $pdo->prepare("
		UPDATE event SET product_id = ?, season_id = ?, place_id = ?, start_time = ?, end_time = ?,
			regstartdate = ?, start_date = ?, max_participants = ?, price = ?, member_price = ?
		WHERE id = ?
	");

	$stmt->execute([
			$obj->product_id,
			$obj->season_id,
			$obj->place_id,
			$obj->start_time,
			$obj->end_time,
			$obj->regstartdate,
			$obj->start_date,
			$obj->max_participants,
			$obj->price,
			$obj->member_price,
			$id
		]);

	return $response->withJson($obj, 200, JSON_UNESCAPED_UNICODE);
});

$app->delete('/admin/event/{id}', function ($request, $response) {

	$id = $request->getAttribute("id");
	$pdo = pdo();

	$stmt = $pdo->prepare("
		DELETE FROM event
		WHERE id = ?
	");

	$stmt->execute([$id]);

	return $response->withStatus(204);
});

?>