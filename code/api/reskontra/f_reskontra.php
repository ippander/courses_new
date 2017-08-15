<?php

require_once('../config.php');
require_once('../model/validius_schema_classes.php');

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

function sendInvoices() {

	$saveInvoiceObj = new SaveInvoiceIn(
		getLogin(isProduction()),
		getInvoiceRows()
	);

	$client = getClient(isProduction());

	// $client = new DummySoapClient("http://localhost/reskontra/validius_schema/validius.wsdl",
	// $client = new DummySoapClient("https://demo.validius.net/ws/?wsdl",
	// 	array(
	// 		"trace" => true
	// 		));
	// $client = getClient(isProduction());

	// $response = null;

	// try {
	$response = $client->saveInvoice($saveInvoiceObj);
	// } catch (SoapFault $e) {
	// 	echo "Exception: " + $e->getMessage();
	// }
	// echo "Response: " . print_r($response) . "\n";
	// var_dump($client->__getLastRequest());
// echo $client->__getLastRequest();
	// return $saveInvoiceObj;
	return $client->__getLastRequest();
}

function getInvoiceRows() {

	$pdo = pdo();

	$invoices = [];

	$accountIds = getParticipatingAccounts($pdo);
	$participants = getParticipants($pdo, $accountIds);
	$members = getUnpaidMemberShips($pdo, $accountIds);
// var_dump($members);
	$invoice = null;

	while ($current = current($participants)) {

		if (!empty($current['street_address'])) {

			$addr = new InvoiceBuyerAddress(
				$current['first_name'] . ' ' . $current['last_name'],
				$current['street_address'],
				$current['zipcode'],
				$current['post_office'],
				$current['email']
				);

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
				'our',
				$addr
				);
		}

		if (!empty($current['course_name'])) {

			$siblingDiscount = empty($current['discount_percent']) ? "" : ", sisaralennus";

			$productname =
				$current['course_name'] . ', ' . $current['place'] . ', ' . weekday($current['weekday'])
				. ' ' . $current['start_time'] . '-' . $current['end_time'] . $siblingDiscount;
			
			$quantity = 1;
			$price = $current['event_price'];
			$discount_percent = empty($current['discount_percent']) ? 0.0 : $current['discount_percent'];
			$vatpercent = 0.0;
			$vatamount = 0.0;
			$netamount = $current['price'];;
			$amount = $netamount;

			$row = new InvoiceRow(
				$productname,
				$quantity,
				$price,
				$discount_percent,
				$vatpercent,
				$vatamount,
				$netamount,
				$amount,
				$current['first_name'] . ' ' . $current['last_name'] 
				);

			$invoice->addInvoiceRow($row);
			// $rows[] = $row;
		}

        $next = next($participants);

        if (false === $next || $next['account_id'] !== $current['account_id']) {

			while ($member = current($members)) {
				
				if ($member['account_id'] == $current['account_id'] && $member['price']) {

					$productname = "Jäsenyys";
					$quantity = 1;
					$price = $member['price'];
					$discount_percent = 0;
					$vatpercent = 0;
					$vatamount = 0;
					$netamount = $member['price'];
					$amount = $member['price'];
					$member['first_name'] . ' ' . $member['last_name'];

					$row = new InvoiceRow(
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

					$invoice->addInvoiceRow($row);
				}

				$next = next($members);		
			}

			reset($members);

        	foreach ($invoice->invoicerow as $row) {
        		$invoice->netamount += $row->amount;
        		$invoice->amount += $row->amount;
			}

        	$invoices[] = $invoice;
		}

	}

	return $invoices;
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

function getUnpaidMemberShips($pdo, $accountIds) {

	$query = "
			SELECT
				a.id account_id, a.email,
				p.id person_id, p.first_name, p.last_name,
				addr.street_address, addr.zipcode, addr.post_office, ms.price
			FROM account a
				LEFT JOIN person p ON (a.id=p.account_id)
				LEFT JOIN address addr ON (p.id=addr.person_id)
			    LEFT JOIN member m ON (p.id=m.person_id)
			    LEFT JOIN membership ms ON (m.membership_id=ms.id)
			WHERE a.id IN (" . implode(",", $accountIds) . ") AND m.id is not null AND m.reskontra is null
			ORDER BY a.id asc, addr.id desc, p.last_name asc, p.first_name asc
			";

	$stmt = $pdo->prepare($query);
	$stmt->execute();
	
	return $stmt->fetchAll();	
}

function getParticipants($pdo, $accountIds) {

	$query = "
		SELECT
			a.id account_id, a.email,
			p.id person_id, p.first_name, p.last_name,
			addr.street_address, addr.zipcode, addr.post_office,
			c.name course_name, e.weekday, pl.name place, e.start_time, e.end_time, e.price event_price,
			pa.price, pa.discounted_amount, dt.name, dt.discount_percent
		FROM account a
			LEFT JOIN person p ON (a.id=p.account_id)
			LEFT JOIN address addr ON (p.id=addr.person_id)
			LEFT JOIN participant pa ON (p.id=pa.person_id)
			LEFT JOIN course_event e ON (pa.event_id=e.id)
			LEFT JOIN product c ON (e.product_id=c.id)
			LEFT JOIN place pl ON (e.place_id=pl.id)
			LEFT JOIN discount_type dt ON (pa.discount_type=dt.id)
		WHERE a.id IN (" . implode(",", $accountIds) . ")
		ORDER BY a.id asc, addr.id desc, p.last_name asc, p.first_name asc
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
		LIMIT 5
	");

	$stmt->execute();

	return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

?>