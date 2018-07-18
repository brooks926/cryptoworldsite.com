<?php

// Database Configuration

$dbHost = ("DESKTOP-C1OSDA6");
$dbUser = ("admin");
$dbPW = ("saad1989");
$dbName = ("bitcofaucet.club/admin.php");

// Establish connection

$mysqli = mysqli_connect($dbHost, $dbUser, $dbPW, $dbName);

// Check connection
if(mysqli_connect_errno()){
 	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Website

$Website_Url =("https://bitcofaucet.club");

?>
