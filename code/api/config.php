<?php

// header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors',1);

function pdo() {
	$host = '127.0.0.1';
	$db   = 'aurajoen_courses_new';
	$user = 'aurajoen_new_usr';
	$pass = '4ur4j0k1';
	$charset = 'utf8';

	$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
	$opt = [
	    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
	    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	    PDO::ATTR_EMULATE_PREPARES   => false,
	];
	
	return new PDO($dsn, $user, $pass, $opt);
}

function connectDb() {

exit('not gonna happen');
	$userName = 'course_admin';
	$password = 'aurajoki';

	$link = mysqli_connect( "localhost", $userName, $password, "aurajoen_courses_new" )
		or die("Could not connect : " . mysqli_error( $link ));
	
	return $link;
}

function queryDb( $query ) {

exit('not gonna happen');
	$link = connectDb();	

	$result = mysqli_query( $link, $query )
		or die("Query failed : " . mysqli_error( $link ));
	
	return $result;
}

function insertDb( $query ) {

exit('not gonna happen');
	$link = connectDb();

	$result = mysqli_query( $link, $query )
		or die("Query failed : " . mysqli_error( $link ));
	
	return mysqli_insert_id($link);	
}
?>