<?php

error_reporting( E_ALL );
ini_set('display_errors', 1);

require("../../phpUtils.php");

$link = makeSqlConnection_new('aurajoen_rusi2', 'anneJ6947');
mysqli_set_charset($link, 'latin1');

$query = "
	SELECT rid from registration where reference is null
";

$result = mysqli_query($link, $query) or die("Query failed : " . mysql_error());

while ($row = mysqli_fetch_object($result)) {
	
	$refcode = viitenumero($row->rid + 1000);

	$query = "update registration set reference='$refcode' where rid=$row->rid";
echo $query;
	mysqli_query($link, $query) or die("Query failed : " . mysql_error());
}


?>