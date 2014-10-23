<?php

if (!isset($_POST['name']))
	header("mwly_dbweb.php");

session_start();
$lib_name = $_POST["name"];
echo "Welcome to $lib_name";

?>
