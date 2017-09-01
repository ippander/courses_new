<?php

require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../model/validius_schema_classes.php');

function getLogin($production = false) {

	$CUSTOMER_ID = $SECURITY_KEY = $CLIENT_ID = null;

	if ($production) {
		$CUSTOMER_ID = "20140473";
		$SECURITY_KEY = "68c682a7858bd0e2e49c79815cd61ff5b11fd0ffe7bb7f65d54827cc65ba2ae5";
		$CLIENT_ID = null;		
	} else {
		$CUSTOMER_ID = "20140127";
		$SECURITY_KEY = "be006f335a0892ca3a4eb72bf1edeb0beabb81773cfaf2ea532549494e7d2d65";
		$CLIENT_ID = "149843";
	}

	return new Login($CUSTOMER_ID, $SECURITY_KEY);
}

function getClient($production = false) {

	$client = null;

	if ($production) {

		$client = new SoapClient("https://www.validius.net/ws/?wsdl",
			array(
				"login" => "aurajoen_uinti",
				"password" => "0bmOv1ieecpvu65qWBbB",
				"trace" => true
				)
			);

	} else {

		$client = new SoapClient("https://demo.validius.net/ws/?wsdl",
			array(
				"trace" => true
				)
			);

	}

	return $client;
}

function isProduction() {
	return false;
}

function weekday($wd) {
	switch ($wd) {
		case 0:
			return "maanantaisin";
		case 1:
			return "tiistaisin";
		case 2:
			return "keskiviikkoisin";
		case 3:
			return "torstaisin";
		case 4:
			return "perjantaisin";
		case 5:
			return "lauantaisin";
		default:
			return "sunnuntaisin";
	}
}

function toInvoiceRow($row) {
// var_dump($row);
	// $siblingDiscount = empty($row['discount_percent']) ? "" : ", sisaralennus";

	$siblingDiscount = array_map(function($r) {
		return $r['name'] . ' ' . $r['percent'] . "%";
	}, $row['discount_types']);

	$productname =
		$row['course_name'] . ', ' . $row['place'] . ', ' . weekday($row['weekday'])
		. ' ' . $row['start_time'] . '-' . $row['end_time'] . ' ' . implode(", ", $siblingDiscount);
	
	$quantity = 1;
	$price = $row['event_price'];
	$discount_percent = empty($row['discount_percent']) ? 0.0 : $row['discount_percent'];
	$vatpercent = 0.0;
	$vatamount = 0.0;
	$netamount = $row['price'];;
	$amount = $netamount;

	return new InvoiceRow(
		$productname,
		$quantity,
		$price,
		$discount_percent,
		$vatpercent,
		$vatamount,
		$netamount,
		$amount,
		$row['first_name'] . ' ' . $row['last_name'] 
	);

	// $invoice->addInvoiceRow($row);

}

function toMemberRow($member) {

	$productname = "Jäsenyys";
	$quantity = 1;
	$price = $member['price'];
	$discount_percent = 0;
	$vatpercent = 0;
	$vatamount = 0;
	$netamount = $member['price'];
	$amount = $member['price'];
	$member['first_name'] . ' ' . $member['last_name'];

	return new InvoiceRow(
		$productname,
		$quantity,
		$price,
		$discount_percent,
		$vatpercent,
		$vatamount,
		$netamount,
		$amount,
		$member['first_name'] . ' ' . $member['last_name'],
		"JASENYYS17/18"
		);

}

function toInvoice($rows, $memberships, $buyer) {

	$addr = new InvoiceBuyerAddress(
		$buyer['first_name'] . ' ' . $buyer['last_name'],
		$buyer['street_address'],
		$buyer['zipcode'],
		$buyer['post_office'],
		$buyer['email']
	);

// var_dump($invoiceRows);
	$invoiceRows = array_map("toMemberRow", $memberships);
	$invoiceRows = array_merge($invoiceRows, array_map("toInvoiceRow", $rows ? $rows : []));

	$now = new DateTime();

	$invoice = new Invoice(
		$now->format("Y-m-d"),
		$now->add(new DateInterval("P14D"))->format("Y-m-d"),
		0,
		0,
		0,
		7.0,
		true,
		'email',
		false,
		null,
		$addr,
		$invoiceRows
		);

	$sum = array_reduce($rows, function($carry, $item) {
		$carry += $item['price'];
		return $carry;
	}, 0);

	$sum = array_reduce($memberships, function($carry, $item) {
		$carry += $item['price'];
		return $carry;
	}, $sum);

	$invoice->netamount = (float)$sum;
	$invoice->amount = (float)$sum;

	// $invoice->invoiceRow = $invoiceRows;

	return $invoice;
}

function sendInvoices() {

	$ids = getParticipatingAccounts(pdo());
	
	foreach ($ids as $id) {
		sendInvoice($id);
	}
}

function sendInvoice($accountId) {

	$pdo = pdo();

	try {

		$pdo->beginTransaction();

		$participations = getUnBilledParticipations($pdo, $accountId);

		if (!sizeof($participations)) {
			return [];
		}

		updateBilled($pdo, $participations);

		$personIds = array_map(function($row) {
			return $row['person_id'];
		}, $participations);

		$memberships = getUnBilledMemberships($pdo, 1, array_unique($personIds));
		updateBilledMemberShips($pdo, $memberships);

		$invoice = [ toInvoice($participations, $memberships, getBuyerAddress($pdo, $accountId)) ];

		$saveInvoiceObj = new SaveInvoiceIn(
			getLogin(isProduction()),
			$invoice
		);

		$client = getClient(isProduction());
		$response = $client->saveInvoice($saveInvoiceObj);
		
		echo $client->__getLastRequest();

		$pdo->commit();

		return $invoice;

	} catch (Exception $e) {
		$pdo->rollback();
		// throw $e;
	}

}

function getBuyerAddress($pdo, $accountId) {

	$query = "
		SELECT *
		FROM account a
			INNER JOIN person p ON (a.id=p.account_id)
		    INNER JOIN address addr ON (p.id=addr.person_id)
		WHERE p.account_id = $accountId
	";

	$stmt = $pdo->prepare($query);
	$stmt->execute();
	
	return $stmt->fetch(PDO::FETCH_ASSOC);	
}

function getUnBilledMemberShips($pdo, $season, $personIds) {

	if (sizeof($personIds) < 1) {
		return [];
	}

	$query = "
		SELECT
			m.id membership_id, p.id person_id, p.first_name, p.last_name, ms.price
		FROM person p
		    LEFT JOIN member m ON (p.id=m.person_id)
		    LEFT JOIN membership ms ON (m.membership_id=ms.id)
		WHERE p.id IN (" . implode(",", $personIds) . ") AND m.id is not null AND m.reskontra is null
		ORDER BY p.last_name asc, p.first_name asc
	";

	$stmt = $pdo->prepare($query);
	$stmt->execute();
	
	return $stmt->fetchAll();	
}

function getUnBilledParticipations($pdo, $accountId) {

	$query = "
		SELECT p.account_id, p.id person_id, p.first_name, p.last_name, p.birthday,
				c.name course_name,
				e.weekday, pl.name place, e.id event_id, e.start_time, e.end_time, e.price event_price, e.max_participants,
				pa.id participation_id, pa.price, pa.discounted_amount, pa.reskontra
		FROM person p
			INNER JOIN participant pa ON (p.id=pa.person_id)
			INNER JOIN course_event e ON (pa.event_id=e.id)
			INNER JOIN product c ON (e.product_id=c.id)
			INNER JOIN place pl ON (e.place_id=pl.id)
		WHERE p.account_id = $accountId AND pa.reskontra IS NULL
		ORDER BY p.last_name asc, p.first_name asc
	";

	$stmt = $pdo->prepare($query);
	$stmt->execute();
	
	$allEnrollments = $stmt->fetchAll();

	$actual = array_filter($allEnrollments, function($row) use ($pdo, $accountId) {
		// filter out the reserved enrollments
		return sizeof(getActualParticipations($pdo, $accountId, $row['event_id']));
	});

	if (!sizeof($actual)) {
		return [];
	}

	setSiblingDiscount($pdo, $actual);
	$calculated = calculatePrices($pdo, $actual);

	return $calculated;
}

function updateBilledMemberShips($pdo, $actual) {

	$query = "
		UPDATE member
		SET reskontra = current_timestamp
		WHERE id = ?
	";

	$stmt = $pdo->prepare($query);

	foreach ($actual as $part) {
		$stmt->execute([ $part['membership_id'] ]);
	}

}

function updateBilled($pdo, $actual) {

	$query = "
		UPDATE participant
		SET price = ?, discounted_amount = ?, reskontra = current_timestamp
		WHERE id = ?
	";

	$stmt = $pdo->prepare($query);

	foreach ($actual as $part) {
		$stmt->execute([  $part['price'], $part['discounted_amount'], $part['participation_id'] ]);
	}

}

function sortByBirthday($rows) {
	// Sort it
	usort($rows, function($a, $b) {

		$aDate = new DateTime($a['birthday']);
		$bDate = new DateTime($b['birthday']);

		return ($aDate < $bDate) ? -1 : 1;
	});

	// return sorted array
	return $rows;
}

function setSiblingDiscount($pdo, $rows) {
// Find underaged members under this account
// Take all after oldest one
// Check their participations and add discount rows

	$query = "
		SELECT p.id person_id, birthday
		FROM person p INNER JOIN member m ON (p.id=m.person_id)
		WHERE account_id = ?
		";

	$stmt = $pdo->prepare($query);
var_dump($rows);
	$stmt->execute([ $rows[0]['account_id'] ]);
	$persons = $stmt->fetchAll();

	$now = new DateTime();
	$eighteen = $now->sub(new DateInterval("P18Y"))->format("Y-m-d");

	$underAgedCount = 0;

	$discountedSibling = array_filter(sortByBirthday($persons), function($row) use ($eighteen, &$underAgedCount) {

		$current = new DateTime($row['birthday']);
		
		if ($current > $eighteen) {
			$underAgedCount++;
		}

		return $underAgedCount > 1;
	});

	$discountedSibling = array_map(function($item) { return $item['person_id']; }, $discountedSibling);

	$discounted = array_filter($rows, function($row) use ($discountedSibling) {
		return in_array($row['person_id'], $discountedSibling);
	});

	$stmt = $pdo->prepare("
		INSERT INTO participant_discount_type (discount_type_id, participant_id)
		VALUES (1, ?)
		");

	foreach ($discounted as $dis) {
		$stmt->execute([$dis['participation_id']]);
	}
}

function calculatePrices($pdo, $participations) {

	$query = "
		SELECT *
		FROM participant_discount_type pdt
			INNER JOIN discount_type dt ON (pdt.discount_type_id=dt.id)
		WHERE pdt.participant_id = ?
	";

	$stmt = $pdo->prepare($query);

	$discounted = array_map(function($row) use ($stmt) {
		
		$row['discount_types'] = [];
		$stmt->execute([ $row['participation_id'] ]);	
		$dis = $stmt->fetchAll();

		// var_dump($dis);
		// var_dump($row);

		if (!sizeof($dis)) {
			$row['price'] = $row['event_price'];
			// $row = 
		} else {

			$price = $row['event_price'];
			$d_amount = 0.0;

			foreach ($dis as $discount) {
				$row['discount_types'][] = [ 'name' => $discount['name'], 'percent' => $discount['discount_percent'] ];
				
				// $row['discount_percent'] = $discount['discount_percent'];

				$row['discounted_amount'] = ($discount['discount_percent'] / 100) * $price;
				$price = $price - $row['discounted_amount'];
			}

			$row['price'] = $price;

		}

		return $row;

	}, $participations);

	return $discounted; 
}

function getActualParticipations($pdo, $accountId, $eventId) {
// Filter out enrollements on "varasijat"

	$query = "
		SELECT part.*
		FROM (
			SELECT @rownum := @rownum  + 1 as rownum, p.account_id, e.max_participants
		    FROM person p
			INNER JOIN participant pa ON (p.id=pa.person_id)
			INNER JOIN course_event e ON (pa.event_id=e.id)
			INNER JOIN product c ON (e.product_id=c.id)
			INNER JOIN place pl ON (e.place_id=pl.id),
			(select @rownum := 0) rn
			WHERE e.id = $eventId
		    ) part 
		WHERE part.rownum < max_participants AND part.account_id = $accountId
	";

	$stmt = $pdo->prepare($query);
	$stmt->execute();
	
	return $stmt->fetchAll();	

}

function getParticipatingAccounts($pdo) {
// Return ids for all accounts that have unpaid participations
	$stmt = $pdo->prepare("
		SELECT p.account_id
		FROM person p INNER JOIN participant pa ON (p.id=pa.person_id)
		WHERE pa.id IS NOT NULL AND pa.reskontra IS NULL
		GROUP BY p.account_id
		LIMIT 50
	");

	$stmt->execute();

	return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

?>