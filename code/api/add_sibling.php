<?php

require('config.php');
require('post_json.php');

$query = "
INSERT INTO sibling (person_id, sibling_id)
VALUES (" . $json_data["person_id"] . ", " . $json_data["sibling_id"] . ")
";

insertDb($query);

$query = "
INSERT INTO sibling (person_id, sibling_id)
VALUES (" . $json_data["sibling_id"] . ", " . $json_data["person_id"] . ")
";

insertDb($query);

?>