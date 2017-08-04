<?php

require_once "f_course.php";

/********** ENROLLMENTS *******/

$app->get('/admin/enrollment', function ($request, $response) {

	$filters = json_decode($request->getQueryParams()['_filters']);

	$id = null;
	$searchField = "pa.event_id";

	if (property_exists($filters, 'event_id')) {
		$id = json_decode($request->getQueryParams()['_filters'])->event_id;
	} else {
		$id = json_decode($request->getQueryParams()['_filters'])->person_id;
		$searchField = "pa.person_id";
	}


	$stmt = pdo()->prepare("
		SELECT
			pa.*,
			pe.first_name, pe.last_name, pe.birthday,
			p.name as course_name, e.weekday, pl.name as place, e.start_time, e.end_time
		FROM participant pa, person pe, course_event e, product p, place pl
		WHERE p.id=e.product_id AND e.id = pa.event_id AND pa.person_id = pe.id AND e.place_id=pl.id
			AND " . $searchField . " = ?
		");

	$stmt->execute([ $id ]);

	return $response->withJson($stmt->fetchAll(), 200, JSON_UNESCAPED_UNICODE);
});

$app->get('/admin/enrollment/{id}', function ($request, $response) {

	$id = $request->getAttribute("id");

	$stmt = pdo()->prepare("SELECT * FROM participant WHERE id = ?");
	$stmt->execute([$id]);
	
	return $response->withJson($stmt->fetch(), 200, JSON_UNESCAPED_UNICODE);
});

$app->post('/admin/enrollment', function ($request, $response) {

	$added = addPlace(json_decode($request->getBody()));

	return $response->withJson($added, 201, JSON_UNESCAPED_UNICODE);
});

$app->put('/admin/enrollment/{id}', function ($request, $response) {

	$id = $request->getAttribute("id");
	$obj = json_decode($request->getBody());
	
	$pdo = pdo();

	$stmt = $pdo->prepare("
		UPDATE participant SET name = ?, address = ?
		WHERE id = ?
	");

	$stmt->execute([$obj->name, $obj->address, $id]);

	return $response->withJson($obj, 200, JSON_UNESCAPED_UNICODE);
});

$app->delete('/admin/enrollment/{id}', function ($request, $response) {

	$id = $request->getAttribute("id");
	$pdo = pdo();

	$stmt = $pdo->prepare("
		DELETE FROM participant
		WHERE id = ?
	");

	$stmt->execute([$id]);

	return $response->withStatus(204);
});

/*********** PERSON **********/

$app->get('/admin/person', function ($request, $response) {

	$stmt = pdo()->prepare("SELECT * FROM person ORDER BY last_name, first_name");
	$stmt->execute();

	return $response->withJson($stmt->fetchAll(), 200, JSON_UNESCAPED_UNICODE);
});

$app->get('/admin/person/{id}', function ($request, $response) {

	$id = $request->getAttribute("id");

	$stmt = pdo()->prepare("SELECT * FROM person WHERE id = ?");
	$stmt->execute([$id]);
	
	return $response->withJson($stmt->fetch(), 200, JSON_UNESCAPED_UNICODE);
});

$app->post('/admin/person', function ($request, $response) {

	$added = addPlace(json_decode($request->getBody()));

	return $response->withJson($added, 201, JSON_UNESCAPED_UNICODE);
});

$app->put('/admin/person/{id}', function ($request, $response) {

	$id = $request->getAttribute("id");
	$obj = json_decode($request->getBody());
	
	$pdo = pdo();

	$stmt = $pdo->prepare("
		UPDATE person SET first_name = ?, last_name = ?, birthday = ?, notes = ?
		WHERE id = ?
	");

	$stmt->execute([
		$obj->first_name, $obj->last_name, $obj->birthday, $obj->notes, $obj->id
		]);

	return $response->withJson($obj, 200, JSON_UNESCAPED_UNICODE);
});

$app->delete('/admin/person/{id}', function ($request, $response) {

	$id = $request->getAttribute("id");
	$pdo = pdo();

	$stmt = $pdo->prepare("
		DELETE FROM person
		WHERE id = ?
	");

	$stmt->execute([$id]);

	return $response->withStatus(204);
});


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
		UPDATE place SET name = ?, address = ?
		WHERE id = ?
	");

	$stmt->execute([$obj->name, $obj->address, $id]);

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

	$stmt = pdo()->prepare("
		SELECT p.name as course_name, s.name as season_name, e.*, pl.name as place_name
		FROM product p, course_event e, place pl, season s
		WHERE p.id=e.product_id and e.place_id=pl.id and e.season_id=s.id
		");
	$stmt->execute();

	return $response->withJson($stmt->fetchAll(), 200, JSON_UNESCAPED_UNICODE);

});

$app->get('/admin/event/{id}', function ($request, $response) {

	$id = $request->getAttribute("id");

	$stmt = pdo()->prepare("SELECT * FROM course_event WHERE id = ?");
	$stmt->execute([$id]);

	$res = $stmt->fetch();

	$res['price'] = (float)$res['price'];
	$res['member_price'] = (float)$res['member_price'];

	// var_dump($res);
	
	return $response->withJson($res, 200, JSON_UNESCAPED_UNICODE);
});

$app->post('/admin/event', function ($request, $response) {

	$obj = json_decode($request->getBody());
	$pdo = pdo();
	
	$stmt = $pdo->prepare("
		INSERT INTO course_event (product_id, season_id, period, place_id, weekday, start_time, end_time,
			regstartdate, start_date, end_date, notes, max_participants, price, member_price)
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
	");

	$stmt->execute([
			$obj->product_id,
			$obj->season_id,
			$obj->period,
			$obj->place_id,
			$obj->weekday,
			$obj->start_time,
			$obj->end_time,
			$obj->regstartdate,
			$obj->start_date,
			$obj->end_date,
			$obj->notes,
			$obj->max_participants,
			$obj->price,
			$obj->member_price,
		]);

	$obj->id = $pdo->lastInsertId();

	return $response->withJson($obj, 201, JSON_UNESCAPED_UNICODE);
});

$app->put('/admin/event/{id}', function ($request, $response) {

	$id = $request->getAttribute("id");
	$obj = json_decode($request->getBody());
	
	$pdo = pdo();

	$stmt = $pdo->prepare("
		UPDATE course_event SET product_id = ?, season_id = ?, period = ?, place_id = ?, weekday = ?, start_time = ?, end_time = ?,
			regstartdate = ?, start_date = ?, end_date = ?, notes = ?, max_participants = ?, price = ?, member_price = ?
		WHERE id = ?
	");

	$stmt->execute([
			$obj->product_id,
			$obj->season_id,
			$obj->period,
			$obj->place_id,
			$obj->weekday,
			$obj->start_time,
			$obj->end_time,
			$obj->regstartdate,
			$obj->start_date,
			$obj->end_date,
			$obj->notes,
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
		DELETE FROM course_event
		WHERE id = ?
	");

	$stmt->execute([$id]);

	return $response->withStatus(204);
});

?>