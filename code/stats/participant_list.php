<?php

error_reporting( 'E_ERRORS' );
ini_set('display_errors', 1);

include('phpUtils.php');

mysql_set_charset('utf8');

$link = makeSqlConnection('aurajoen_rusi2', 'anneJ6947');

$query = "
	select c.name course,
		case e.weekday
	    	when 0 then 'Maanantaisin'
	    	when 1 then 'Tiistaisin'
	    	when 2 then 'Keskiviikkoisin'
	    	when 3 then 'Torstaisin'
	    	when 4 then 'Perjantaisin'
	    	when 5 then 'Lauantaisin'
	    	when 6 then 'Sunnuntaisin'
	    end as paiva
	    ,
	pl.name place, e.start_time, e.end_time, p.first_name, p.last_name, p.birthday, p.notes, a.email, pa.enrolled_at
	from product c, course_event e, person p, participant pa, account a, place pl
	where c.id=e.product_id and e.id=pa.event_id and pa.person_id=p.id and p.account_id=a.id and e.place_id=pl.id
	order by e.start_date, e.start_time, pa.enrolled_at
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