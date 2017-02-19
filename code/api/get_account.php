<?php

require("config.php");

$id = $_GET["id"];

$query = "
SELECT a.id as acc_id, a.email, p.*
FROM account a LEFT JOIN person p ON (a.id=p.account_id)
WHERE a.id = $id
";

$result = queryDb($query);

if (mysqli_num_rows($result) < 1) {
	http_response_code(404);
	die("Not found");
}

$account = null;

while ($row = mysqli_fetch_object($result)) {

	if ($account == null) {
		
		$account = new stdClass();
		
		$account->id = $row->acc_id;
		$account->email = $row->email;
		$account->persons = array();
	}

	if ($row->id != null) {
		$person = new stdClass();

		$person->first_name = $row->first_name;
		$person->last_name = $row->last_name;
		$person->birthday = $row->birthday;
		$person->street_address = $row->street_address;
		$person->zipcode = $row->zipcode;
		$person->post_office = $row->post_office;

		$account->persons[] = $person;
	}
}

print(json_encode($account, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));

?>