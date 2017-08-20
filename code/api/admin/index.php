<?php

require_once "f_course.php";

function getLimitString($request) {
	// 	$filters = json_decode($request->getQueryParams()['_filters']);

	// $id = null;
	// $searchField = "pa.event_id";

	// if (property_exists($filters, 'event_id')) {
	// 	$id = json_decode($request->getQueryParams()['_filters'])->event_id;
	// } else {
	// 	$id = json_decode($request->getQueryParams()['_filters'])->person_id;
	// 	$searchField = "pa.person_id";
	// }

	$params = $request->getQueryParams();

	$query = '';

	if (array_key_exists('_filters', $params)) {

		$query .= ' WHERE';
		$filters = json_decode($params['_filters'], true);

		foreach ($filters as $key => $value) {
			$query .= " " . $key . " LIKE '" . $value . "%'";
		}

		// var_dump($filters);
	}

	$currentPage = $params['_page'];
	$perPage = $params['_perPage'];
	$sortDir = $params['_sortDir'];
	$sortField = $params['_sortField'];

	return  $query . " ORDER BY " . $sortField . " " . $sortDir . " LIMIT " . $perPage . " OFFSET " . $perPage * ($currentPage - 1);
}

/********** ACCOUNT *********/

$app->get('/admin/account', function ($request, $response) {

	$stmt = pdo()->prepare("SELECT * FROM account ORDER BY email");
	$stmt->execute();

	return $response->withJson($stmt->fetchAll(), 200, JSON_UNESCAPED_UNICODE);
});

$app->get('/admin/account/{id}', function ($request, $response) {

	$id = $request->getAttribute("id");

	$stmt = pdo()->prepare("SELECT * FROM account WHERE id = ?");
	$stmt->execute([ $id ]);

	return $response->withJson($stmt->fetch(), 200, JSON_UNESCAPED_UNICODE);
});

$app->put('/admin/account/{id}', function ($request, $response) {

	$id = $request->getAttribute("id");
	$obj = json_decode($request->getBody());
	
	$pdo = pdo();

	$stmt = $pdo->prepare("
		UPDATE account SET email = ?, password = ?
		WHERE id = ?
	");

	$stmt->execute([
		$obj->email, password_hash($obj->password, PASSWORD_DEFAULT), $id
		]);

	return $response->withJson($obj, 200, JSON_UNESCAPED_UNICODE);
});

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

	$pdo = pdo();
	$res = $pdo->query("SELECT count(*) FROM product");

	$count = $res->fetchColumn();
	$response = $response->withHeader('X-Total-Count', $count);

	$query = "SELECT * FROM product" . getLimitString($request);

	$stmt = pdo()->prepare($query);
	$stmt->execute();
	
	return $response->withJson($stmt->fetchAll(), 200, JSON_UNESCAPED_UNICODE);
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
		ORDER BY course_name, season_name, weekday
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

$app->get('/admin/stats/current', function ($request, $response) {

	$pdo = pdo();

	$stmt = pdo()->prepare("
select kurssit.*, (kurssit.PaikkojaYht - Osallistujat) PaikkojaVapaana, (Osallistujat / PaikkojaYht) ratio
from (
    select c.name Kurssi, pl.name Paikka, e.weekday, count(*) Osallistujat, e.max_participants PaikkojaYht, sum(e.price) Brutto
    from product c, course_event e, place pl, participant pa
    where c.id=e.product_id and e.place_id=pl.id and pa.event_id=e.id
    group by e.id
) kurssit
order by PaikkojaVapaana desc
		");

	$stmt->execute();
	$res['data'] = $stmt->fetchAll();

	// var_dump($res);
	
	return $response->withJson($res, 200, JSON_UNESCAPED_UNICODE);
});

$app->get('/admin/stats/participant_list', function ($request, $response) {

	$pdo = pdo();

	$stmt = pdo()->prepare("
		select c.name course,
			start_date,
			case e.weekday
		    	when 0 then 'Maanantaisin'
		    	when 1 then 'Tiistaisin'
		    	when 2 then 'Keskiviikkoisin'
		    	when 3 then 'Torstaisin'
		    	when 4 then 'Perjantaisin'
		    	when 5 then 'Lauantaisin'
		    	when 6 then 'Sunnuntaisin'
		    end as paiva
		    ,
		pl.name place, e.start_time, e.end_time, p.first_name, p.last_name, p.birthday, p.notes, a.email, pa.enrolled_at
		from product c, course_event e, person p, participant pa, account a, place pl
		where c.id=e.product_id and e.id=pa.event_id and pa.person_id=p.id and p.account_id=a.id and e.place_id=pl.id
		order by e.start_date, e.start_time, pa.enrolled_at
	");

	$stmt->execute();
	$res['data'] = $stmt->fetchAll();

	// var_dump($res);
	
	return $response->withJson($res, 200, JSON_UNESCAPED_UNICODE);
});

?>