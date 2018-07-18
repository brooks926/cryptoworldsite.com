<?php
session_start();
error_reporting(0);
include("config.php");
include("session.php");
include("template.class.php");
include("template.settings.php");
include("functions.php");
include("faucethub.php");

// CSRF PROTECTION

if($_SESSION['token'] == ""){
	$_SESSION['token'] = md5(md5(uniqid().uniqid().mt_rand()));
}
?>
