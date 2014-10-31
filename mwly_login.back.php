<?php
ini_set('display_errors', 'On');

session_start();
if (isset($_GET['action']) && $_GET['action'] == 'logout')
{
	//session_destroy();
	$library_id = $_SESSION['library_id'];
	$_SESSION = array();
	$_SESSION['library_id'] = $library_id;
	header("Location:mwly_search.php");
}

require_once  "mwly_conn.inc";
$nexturl = "mwly_search.php";
if (!isset($_SESSION['library_id']))
	header("Location:$nexturl");
if (!isset($_POST['login_as']) || empty($_POST['login_as']))
	header("Location:$nexturl");
if (!isset($_POST['login_id']) || empty($_POST['login_id']))
	header("Location:$nexturl");

$as = $_POST['login_as'];
$as_id = $as . "_id";
$as_name = $as . "_name";
$login_id = intval($_POST['login_id']);
$sql = "select $as_name from $as
	where $as_id=$login_id";
if ($as == "librarian")
	$sql = $sql." and library_id=".$_SESSION['library_id'];
$stmt = oci_parse($conn, $sql);
oci_execute($stmt, OCI_DEFAULT);
if ($res = oci_fetch_row($stmt)) {
	$name = $res[0];
	$_SESSION[$as_id] = $login_id;
	$_SESSION[$as_name] = $name;
	header("Location:$nexturl");
} else {
	echo "<script type=\"text/javascript\">
	alert(\"Login Fail: invalid id\");
	location.href=\"$nexturl\";
	</script>";
}
?>
