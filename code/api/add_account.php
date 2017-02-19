<?php

require('config.php');
require('post_json.php');

$query = "
INSERT INTO account (email, password)
VALUES ('" . $json_data["email"] . "', '" . md5($json_data["password"]) . "')
";

$id = insertDb($query);

$json_data["id"] = $id;
unset($json_data["password"]);

print(json_encode($json_data));


?>