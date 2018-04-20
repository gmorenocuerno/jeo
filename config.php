<?php

	$servername = "localhost";
	$username = "root";
	$password = "root";
//*/


	//$servername = "104.197.60.114";
	//$servername = "sql313.byethost4.com";
	
	//$username = "geekoffers";
	//$username = "b4_16916234";
	
	//$password = "chewbacca";
	//$password = "profedesa2015";
//*/
	
	// Create connection
	$conn = new mysqli($servername, $username, $password);
	
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 
	//echo "Connected successfully";
?>