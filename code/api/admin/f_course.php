<?php

require_once "config.php";

// function getPlaces() {

// 	$stmt = pdo()->prepare("SELECT * FROM place");
// 	$stmt->execute();
	
// 	return $stmt->fetchAll();
// }

function addPlace($place) {

	$pdo = pdo();

	$stmt = $pdo->prepare("
		INSERT INTO place (name)
		VALUES (?)
	");

	$stmt->execute([$place->name]);

	$place->id = $pdo->lastInsertId();

	return $place;

}

// function getSeasons() {

// 	$stmt = pdo()->prepare("SELECT * FROM season");
// 	$stmt->execute();
	
// 	return $stmt->fetchAll();
// }

// function addSeason($season) {

// 	$pdo = pdo();

// 	$stmt = $pdo->prepare("
// 		INSERT INTO season (name, season_start, season_end)
// 		VALUES (?, ?, ?)
// 	");

// 	$stmt->execute([$season->name, $season->startYear, $season->endYear]);

// 	$season->id = $pdo->lastInsertId();

// 	return $season;

// }

function getPeriods() {

	$stmt = pdo()->prepare("SELECT * FROM period");
	$stmt->execute();
	
	return $stmt->fetchAll();
}

function addPeriod($obj) {

	$pdo = pdo();

	$stmt = $pdo->prepare("
		INSERT INTO period (season_id, quarter)
		VALUES (?, ?)
	");

	$stmt->execute([$obj->seasonId, $obj->quarter]);

	$obj->id = $pdo->lastInsertId();

	return $obj;

}

function addCourse($obj) {

	$pdo = pdo();

	$stmt = $pdo->prepare("
		INSERT INTO product (name, description)
		VALUES (?, ?)
	");

	$stmt->execute([ $obj->name, $obj->description ]);

	$obj->id = $pdo->lastInsertId();

	return $obj;

}

function updateCourse($obj, $id) {

	$pdo = pdo();

	$stmt = $pdo->prepare("
		UPDATE product SET name = ?, description = ?
		WHERE id = ?
	");

	$stmt->execute([$obj->name, $obj->description, $id]);

	return $obj;

}

function getCourses() {

	$stmt = pdo()->prepare("SELECT * FROM product");
	$stmt->execute();
	
	return $stmt->fetchAll();
}

function getCourse($id) {

	$stmt = pdo()->prepare("SELECT * FROM product WHERE id = ?");
	$stmt->execute([$id]);
	
	return $stmt->fetch();
}

// function getPerson($id) {

// 	$stmt = pdo()->prepare("SELECT * FROM person WHERE id = ?");
// 	$stmt->execute([$id]);
	
// 	return $stmt->fetch();
// }

function deleteCourse($id) {

	$pdo = pdo();

	$stmt = $pdo->prepare("
		DELETE FROM product
		WHERE id = ?
	");

	$stmt->execute([$id]);

}

?>