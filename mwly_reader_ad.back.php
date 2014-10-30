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

$nexturl = "mwly_reader_man.php";
ini_set('display_errors', 'On');
session_start();
require 'mwly_conn.inc';
if (!isset($_POST['post-type'])) 
	header("Location:$nexturl");
	
switch ($_POST['post-type']) {
case "add_reader":
	$name = trim($_POST['reader_name']);
	$gender = ($_POST['gender']=="M") ? "M" : "F";
	$quota = intval($_POST['reader_quota']);
	if (empty($name))
		error_return("Must have a name!", $nexturl);
	if ($quota <= 0)
		error_return("Quato cannot be lower than 1!", $nexturl);
	$sql = "insert into reader
		values (
			(select max(reader_id)+1
			 from reader), '$name',
			 '$gender', $quota)";
	$stmt = oci_parse($conn, $sql);
	$ret = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
	if ($ret)
		error_return("Add reader success", $nexturl);	
	else
		error_return("Add reader fail!", $nexturl);
	break;
case "del_reader":
	$readerid = intval($_POST['reader_id']);
	if ($readerid <= 0) {
		error_return("Invalid reader id!", $nexturl);
	}	
	$sql = "select * from reader 
		where reader_id=$readerid";
	$stmt = oci_parse($conn, $sql);
	oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
	if (!oci_fetch_row($stmt))
		error_return("No such reader!", $nexturl);		
	$sql = "delete from reader 
		where reader_id=$readerid";
	$stmt = oci_parse($conn, $sql);
	$ret = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
	if ($ret)
		error_return("Delete reader success", $nexturl);	
	else
		error_return("Delete reader fail!", $nexturl);		
break;
}

?>
