<?php

error_reporting( 'E_ERRORS' );
ini_set('display_errors', 1);

include('phpUtils.php');

mysql_set_charset('utf8');

$link = makeSqlConnection('aurajoen_rusi2', 'anneJ6947');

$query = "
SELECT c.regstartdate, c.name, c.period, c.place,
	COUNT(*) AS osallistuja_lkm, sum(IF(p.member, c.price_member, c.price)) AS summa,
    (COUNT(*) / c.pmax) AS tayttoaste, c.reserve AS varalla
FROM person p, registration r, course c
WHERE p.id=r.person_id AND r.course_id=c.id
	AND regstartdate > '2017-03-12'
GROUP BY c.regstartdate, c.name, c.period
ORDER BY c.regstartdate, summa DESC
";


$result = mysql_query($query, $link) or die("Query failed : " . mysql_error());

$arr['data'] = array();

while ($row = mysql_fetch_object($result)) {
	$arr['data'][] = $row;
}

header('Content-type: application/json; charset=utf-8');
// header('Content-type: application/json; charset=latin1');
// print_r($arr);
print(json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));
// print(json_last_error())
// var_dump($arr);
// var_dump(json_encode($arr, JSON_PRETTY_PRINT));
// var_dump(json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT), json_last_error());
// print(json_last_error())

// print(json_encode(array('data' => mysql_fetch_array($result, MYSQL_ASSOC))));
?>