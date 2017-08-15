<?php

require("../phpUtils.php");
require("validius_schema_classes.php");

function getDbLink() {

	$link = makeSqlConnection_new('aurajoen_rusi2', 'anneJ6947');
	mysqli_set_charset($link, 'latin1');

	return $link;
}

function getRegistrationsFrom($dateString) {

	$link = getDbLink();

	$query = "
		SELECT r.rid, r.reference, p.email, p.member, p.name as customer_name, p.parent_name as parent_name,
			p.information,
			p.address, p.zipcode, p.hometown, c.name as course_name, c.period,
			IF(p.member, c.price_member, c.price) as price, p.sibling_discount
		FROM person p, registration r, course c
		WHERE p.id=r.person_id AND r.course_id=c.id AND reskontra is NULL and r.course_payed != 'y'
			AND (
					(c.regstartdate > '$dateString')
					OR
					(c.regstartdate = '2016-05-06 21:15:00' and c.name like '%senyys%')
				)
				
		ORDER BY p.email, p.name, c.name
		LIMIT 50
	";

	$result = mysqli_query($link, $query) or die("Query failed : " . mysqli_error($link));

	$arr = array();

	while ($row = mysqli_fetch_object($result)) {
		$arr[] = $row;
	}

	return $arr;	
}


// ============ SOAP STUFF ========== //

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

$VATAMOUNT = 0;
$NETAMOUNT = 0;
$AMOUNT = 0;
$OVERDUE_INTEREST = 7;
$SEND_TYPE = "email";
$AUTOCONFIRM = false;

function generateInvoiceObjectFrom($reg) {

	global $VATAMOUNT, $NETAMOUNT, $AMOUNT, $OVERDUE_INTEREST, $SEND_TYPE, $AUTOCONFIRM;
	
	// If address is not given then no payment control could be set
	$paymentControl = $reg->address != "" && $reg->zipcode != "";

	$now = new DateTime();
	$course_name = $reg->course_name . ' ' . $reg->period;

	$address = $reg->address;
	$zipcode = $reg->zipcode;

	if (empty(trim($address))) {
		$address = "Ei tiedossa";
	}

	if (empty(trim($zipcode))) {
		$zipcode = "Ei tiedossa";
	}

	// $freetext = (empty($reg->information) ? null : $reg->information);
	$freetext = $reg->course_name . ' ' . $reg->period . ' ' . $reg->customer_name;

	if ($reg->member) {
		$freetext .= " Jäsenhinta.";
	}

	$productName = "Uimakoulu";

	if (trim($reg->course_name) == "JÄSENYYS") {
		$productName = "Jäsenyys";
		$productCode = "JASENYYS";
	} else {
		$productCode = "UK1";
	}

	$reference = trim($reg->reference);

	if (empty($reference)) {
		$reference = getReferenceFor($reg->rid); 
	}

	$price = explode(",", $reg->price)[0];

	$customer_name = empty($reg->parent_name) ? $reg->customer_name : $reg->parent_name;

	$invoice = 	new Invoice(
			$now->format("Y-m-d"),
			$now->add(new DateInterval("P14D"))->format("Y-m-d"),
			$VATAMOUNT,
			$NETAMOUNT,
			$AMOUNT,
			$OVERDUE_INTEREST,
			$paymentControl,
			$SEND_TYPE,
			$AUTOCONFIRM,
			$reference,
			new InvoiceBuyerAddress($customer_name, $address, $zipcode, $reg->hometown, $reg->email)
			);

	$invoice->addInvoiceRow(new InvoiceRow($productName, 1, $price, 0, 0, 0, $price, $price, $freetext, $productCode));
	$discount = 0;

	if ($productName != "Jäsenyys" && $reg->sibling_discount) {
		$discount = -ceil((0.1 * ($price * 100)) / 100);
		$invoice->addInvoiceRow(new InvoiceRow("Sisaralennus", 1, $discount, 0, 0, 0, $discount, $discount, null, "SIS1"));
	}

	$invoice->netamount = $price + $discount;
	$invoice->amount = $price + $discount;

	return $invoice;
}

// date format YYYY-MM-DD
function sendInvoicesFrom($dateString, $batchSize = 50) {

	$registrations = getRegistrationsFrom($dateString);
	$chunked = array_chunk($registrations, $batchSize);

	foreach ($chunked as $registrations) {

		$invoices = array();

		foreach ($registrations as $reg) {
			$invoices[] = generateInvoiceObjectFrom($reg);
			$sentRegistrations = $reg;
		}

		sendInvoice($invoices);

		foreach ($registrations as $sent) {
			saveReskontraStatusFor($sent->rid);
		}
	}
}

function getReferenceFor($registrationId) {
	return viitenumero($registrationId + 1000);
}

function sendInvoiceForRegistration($registrationId) {

	$link = getDbLink();

	$query = "
		SELECT r.rid, r.reference, p.email, p.member, p.name as customer_name, p.information,
			p.address, p.zipcode, p.hometown, c.name as course_name, c.period,
			IF(p.member, c.price_member, c.price) as price, p.sibling_discount
		FROM person p, registration r, course c
		WHERE p.id=r.person_id AND r.course_id=c.id 
			AND r.rid = $registrationId
	";

	$result = mysqli_query($link, $query) or die("Query failed : " . mysqli_error($link));

	sendInvoice(generateInvoiceObjectFrom(mysqli_fetch_object($result)));
	saveReskontraStatusFor($registrationId);
}

function saveReskontraStatusFor($registrationId) {

	$reference = getReferenceFor($registrationId);

	$query = "UPDATE registration SET course_payed = 'y', reference = '$reference', reskontra = now() WHERE rid = $registrationId";
	mysqli_query(getDbLink(), $query) or die("Query failed : " . mysqli_error($link));
}

function sendInvoice($invoices) {

	$saveInvoiceObj = new SaveInvoiceIn(
		getLogin(isProduction()),
		$invoices
		);


	// $client = new DummySoapClient("http://localhost/reskontra/validius_schema/validius.wsdl",
	// $client = new DummySoapClient("https://demo.validius.net/ws/?wsdl",
	// 	array(
	// 		"trace" => true
	// 		));
	$client = getClient(isProduction());

	$response = null;

	// try {
		$response = $client->saveInvoice($saveInvoiceObj);
	// } catch (SoapFault $e) {
	// 	echo "Exception: " + $e->getMessage();
	// }

	echo "Response: " . print_r($response) . "\n";
	var_dump($client->__getLastRequest());
}

function isProduction() {
	return true;
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

?>