<?php

require_once('config.php');

function login($login) {

	$pdo = pdo();
	$user = [];


	$stmt = $pdo->prepare("
			SELECT a.id as account_id, a.email, a.password,
				p.id, p.first_name, p.last_name,
				addr.street_address, addr.zipcode, addr.post_office
			FROM account a
				LEFT JOIN person p on (a.id=p.account_id)
				LEFT JOIN address addr on (p.id=addr.person_id)
			WHERE a.email = ? and addr.id IS NOT NULL
		");
	
	$stmt->execute([$login->username]);

	$res = $stmt->fetch();

	if (password_verify($login->password, $res['password'])) {

		unset($res['password']);
		$user = $res;

		$stmt = $pdo->prepare("
				SELECT p.id, p.first_name, p.last_name, p.birthday, p.notes
				FROM person p
					-- LEFT JOIN address addr on (p.id=addr.person_id)
				WHERE p.account_id = ?
			");

		$stmt->execute([$res['account_id']]);

		$swimmers = $stmt->fetchAll();

		for ($i = 0; $i < count($swimmers); $i++) {
			$swimmers[$i]['isMember'] = isMember($pdo, $swimmers[$i]['id']);
		}

		$user['swimmers'] = $swimmers;

		return $user;
	} 

	return false;
}

function updateSwimmer($swimmer, $account_id) {

	$pdo = pdo();
	$swimmerStmt = $pdo->prepare("
		UPDATE person
		SET first_name = ?, last_name = ?, birthday = ?, notes = ?
		WHERE id = ?
	");

	try {

		$pdo->beginTransaction();

		$swimmerStmt->execute([
			$swimmer->first_name,
			$swimmer->last_name,
			$swimmer->birthday,
			$swimmer->notes,
			$swimmer->id
			]);

		$pdo->commit();

	} catch (Exception $e) {
		$pdo->rollback();
		echo $e->getTraceAsString();
		throw $e;
	}

	return $swimmer;
}

function addAccount($person) {

	$pdo = pdo();

	$addressStmt = $pdo->prepare("
		INSERT INTO address (person_id, street_address, zipcode, post_office)
		VALUES (?, ?, ?, ?)
		");

	$accountStmt = $pdo->prepare("
		INSERT INTO account (email, password)
		VALUES (?, ?)
	");

	try {

		$pdo->beginTransaction();

		$accountStmt->execute([$person->email, password_hash($person->password, PASSWORD_DEFAULT)]);
		$person->account_id = $pdo->lastInsertId();

		$person = addPerson($pdo, $person);

		$addressStmt->execute([$person->id, $person->street_address, $person->zipcode, $person->post_office]);


		$pdo->commit();

	} catch (Exception $e) {
		$pdo->rollback();
		echo $e->getTraceAsString();
		throw $e;
	}

	unset($person->password);

	return $person;
}

function updateAccount($person) {

	$pdo = pdo();

	$stmt = $pdo->prepare("
		UPDATE account, person, address
		SET account.email = ?,
		-- account.password = ?,
			person.first_name = ?, person.last_name = ?,
		--	, person.birthday = ?,
			address.street_address = ?, address.zipcode = ?, address.post_office = ?
		WHERE account.id = person.account_id
			AND address.person_id=person.id
			AND account.id = ?
	");

	try {

		$pdo->beginTransaction();

		$stmt->execute([
			$person->email,
			// password_hash($person->password, PASSWORD_DEFAULT),
			$person->first_name, $person->last_name, 
			// $person->birthday,
			$person->street_address, $person->zipcode, $person->post_office,
			$person->account_id
			]);

		$pdo->commit();

	} catch (Exception $e) {
		echo $e->getTraceAsString();
		$pdo->rollback();
		throw $e;
	}

	return $person;
}

function getAccounts() {

	$stmt = pdo()->prepare("SELECT * FROM account");
	$stmt->execute();
	
	return $stmt->fetchAll();
}

function addPerson($pdo, $person) {

	$stmt = $pdo->prepare("
		INSERT INTO person (account_id, first_name, last_name, birthday, notes)
		VALUES (?, ?, ?, ?, ?)
		");

	$stmt->execute([ $person->account_id, $person->first_name, $person->last_name, $person->birthday, $person->notes ]);

	$person->id = $pdo->lastInsertId();
	
	return $person;
}

function addSwimmer($accountId, $swimmer) {

	$pdo = pdo();

	$swimmer->account_id = $accountId;
	$swimmer = addPerson($pdo, $swimmer);

	return $swimmer; 
}

function getAccount($id) {

	$stmt = pdo()->prepare("
		SELECT *
		FROM account a
			LEFT JOIN person p ON (a.id = p.account_id)
			LEFT JOIN address ad ON (p.id = ad.person_id)
		WHERE a.id = ?
		");

	$stmt->execute([$id]);

	return $stmt->fetch();
}

function getCustomers($accountId) {

	$stmt = pdo()->prepare("SELECT * FROM person WHERE account_id = ?");
	$stmt->execute([$accountId]);

	return $stmt->fetchAll();	
}

function getCurrentCourses() {

	$stmt = pdo()->prepare("
SELECT p.id as course_id, p.name as course_name, p.description as course_description,
	e.id as event_id, e.start_date, e.end_date, e.weekday, e.start_time, e.end_time,
    e.price, e.member_price, e.notes, pl.name as place, pl.address, e.max_participants,
    count(*) as current_participants
FROM product p
	INNER JOIN course_event e ON (p.id=e.product_id)
    INNER JOIN place pl ON (e.place_id=pl.id)
    LEFT JOIN participant pa on (pa.event_id=e.id)
GROUP BY p.id, e.id
ORDER BY p.name, e.id
");

	$stmt->execute();

	return $stmt->fetchAll();	

}

function isMember($pdo, $person_id) {

	$memberQuery = $pdo->prepare("
			SELECT * FROM member WHERE membership_id = 1 and person_id = ?
		");

	$memberQuery->execute([ $person_id ]);

	return 	count($memberQuery->fetchAll()) > 0;
}

function saveEnrollments($account_id, $enrollments) {

	$pdo = pdo();
var_dump($enrollments);
	foreach ($enrollments as $e) {

		$enrollStmt = $pdo->prepare("
			INSERT INTO participant (person_id, event_id)
			VALUES (?, ?)
			");

		try {

			$pdo->beginTransaction();
			
			$enrollStmt->execute([ $e->swimmer->id, $e->event->id ]);

			if (!isMember($pdo, $e->swimmer->id)) {

				$memberStmt = $pdo->prepare("
					INSERT INTO member (membership_id, person_id)
					VALUES (1, ?)
					");

				$memberStmt->execute([ $e->swimmer->id ]);
			}

			checkOut($account_id);
			$pdo->commit();

		} catch (Exception $e) {
			$pdo->rollback();
			// echo $e->getTraceAsString();
			throw $e;
		}
	}

	return $enrollments;
}

function checkOut($accountId) {

	require_once(__DIR__ . "/reskontra/f_reskontra.php");

	sendInvoice($accountId);
}

function getCurrentEnrollments($accountId) {

	$pdo = pdo();

	$enrollQuery = $pdo->prepare("
			SELECT
				p.id course_id, p.name course_name, p.description course_description,
				e.id event_id, e.weekday, e.start_time, e.end_time, e.start_date, e.end_date, e.price, pl.name place, pl.address,
				pe.id person_id, pe.account_id, pe.first_name, pe.last_name, pe.birthday, true isMember, pe.notes
			FROM product p, course_event e, participant pa, person pe, place pl
			WHERE p.id=e.product_id and e.id=pa.event_id and pa.person_id=pe.id and e.place_id=pl.id
				AND account_id = ?
		");
		
	$enrollQuery->execute([ $accountId ]);
	

	return $enrollQuery->fetchAll();
}











?>