<?php
function error_return($msg, $next)
{
	echo '<script type="text/javascript">alert("'
			. $msg . '");
			location.href = "'
			. $next . '";
			</script>';
	exit();
}

$nexturl = "mwly_patron_man.php";
ini_set('display_errors', 'On');
session_start();
require 'mwly_conn.inc';
if (!isset($_POST['post-type'])) 
	header("Location:$nexturl");
	
switch ($_POST['post-type']) {
case "add_patron":
	$name = trim($_POST['patron_name']);
	$info = trim($_POST['patron_info']);
	if (empty($name))
		error_return("Must have a name!", $nexturl);
	$sql = "select max(patron_id)+1
			 from patron";
	$stmt = oci_parse($conn, $sql);
	oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
	$res = oci_fetch_row($stmt);
	$pid = intval($res[0]);
	$sql = "insert into patron
		values ($pid, '$name', '$info')";
	$stmt = oci_parse($conn, $sql);
	$ret = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
	if (!$ret)
		error_return("Add patron fail!", $nexturl);
	$sql = "insert into sponsor_of
		values ($pid, {$_SESSION['library_id']})";
	$stmt = oci_parse($conn, $sql);
	$ret = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
	if (!$ret)
		error_return("Add patron fail!", $nexturl);	
	else
		error_return("Add patron success", $nexturl);	
	break;
case "del_patron":
	$pid = intval($_POST['patron_id']);
	if ($pid <= 0)
		error_return("Invalid patron id!", $nexturl);
	$sql = "select * from sponsor_of s,patron p
		where p.patron_id=$pid
		and s.patron_id=p.patron_id
		and s.library_id={$_SESSION['library_id']}";
	$stmt = oci_parse($conn, $sql);
	oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
	if (!oci_fetch_row($stmt))
		error_return("No such patron!", $nexturl);		
	$sql = "delete from patron 
		where patron_id=$pid";
	$stmt = oci_parse($conn, $sql);
	$ret = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
	if ($ret)
		error_return("Delete patron success", $nexturl);	
	else
		error_return("Delete patron fail!", $nexturl);		
	break;
}

?>
