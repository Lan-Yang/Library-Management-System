<?php

if (!isset($_POST['library_id']))
	header("mwly_dbweb.php");

session_start();
$lib_id = $_POST['library_id'];
echo "Welcome to $lib_id";

?>
