<?php
ini_set('display_errors', 'On');

session_start();
if (isset($_GET['action']) && $_GET['action'] == 'logout')
{
	session_destroy();
	header("Location:mwly_dbweb.php");
}

require_once  "mwly_conn.inc";
if (isset($_SESSION['library_id']) 
 && isset($_POST['login_as']) && isset($_POST['login_id']))
{
	$as = $_POST['login_as'];
	$as_id = $as . "_id";
	$as_name = $as . "_name";
	$sql = "select $as_name
		from $as 
		where $as_id=".$_POST['login_id'];
	if ($as == "librarian")
		$sql = $sql." and library_id=".$_SESSION['library_id'];
	$stmt = oci_parse($conn, $sql);
	oci_execute($stmt, OCI_DEFAULT);
	while ($res = oci_fetch_row($stmt))
		$name = $res[0];
	if (isset($name) && !empty($name)) {
		$_SESSION[$as_id] = $_POST['login_id'];
		$_SESSION[$as_name] = $name;
	}
}
header("Location:mwly_search.php");
?>