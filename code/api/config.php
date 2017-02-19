<?php

header('Content-Type: application/json; charset=utf-8');

function connectDb() {

	$userName = 'course_admin';
	$password = 'aurajoki';

	$link = mysqli_connect( "localhost", $userName, $password, "aurajoen_courses_new" )
		or die("Could not connect : " . mysqli_error( $link ));
	
	return $link;
}

function queryDb( $query ) {

	$link = connectDb();
	
	$result = mysqli_query( $link, $query )
		or die("Query failed : " . mysqli_error( $link ));
	
	return $result;
}

function insertDb( $query ) {

	$link = connectDb();

	$result = mysqli_query( $link, $query )
		or die("Query failed : " . mysqli_error( $link ));
	
	return mysqli_insert_id($link);	
}
?>