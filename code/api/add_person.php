<?php

require('config.php');
require('post_json.php');

$query = "
INSERT INTO person (account_id, first_name, last_name, birthday, street_address, zipcode, post_office)
VALUES (
	" . $json_data["account_id"] . ",
	'" . $json_data["first_name"] . "',
	'" . $json_data["last_name"] . "',
	'" . $json_data["birthday"] . "',
	'" . $json_data["street_address"] . "',
	'" . $json_data["zipcode"] . "',
	'" . $json_data["city"] . "'
);
";

$id = insertDb($query);

$json_data["id"] = $id;
unset($json_data["password"]);

print(json_encode($json_data));


?>