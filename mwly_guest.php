<!DOCTYPE html>
<html>
<?php
session_start();
if (!isset($_SESSION['library_id']) || empty($_SESSION['library_id'])) {
	if (!isset($_POST['library_id']) || empty($_POST['library_id'])) {
		header("Location: mwly_dbweb.php");
	} else {
		$lib_id = $_POST['library_id'];
		$_SESSION['library_id'] = $lib_id;
	}
} else {
	$lib_id = $_SESSION['library_id'];
}
?>

<head>
	<title>Guest Frame</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
</head>

<body>
<div id = "banner">
<?php
require 'mwly_conn.inc';
$stmt = oci_parse($conn, "select library_name from library where 
			library_id=$lib_id");
oci_execute($stmt, OCI_DEFAULT);
while ($res = oci_fetch_row($stmt))
{
	$library_name = $res[0] ;
}
echo "<h1>$library_name</h1>"
?>
</div>
<div id = "content2">
	<form name="login" action="mwly_login_r" method="post">
	log in:
		<select name="login_as">
  		<option value="reader">reader</option>
  		<option value="librarian">librarian</option>
		</select>
		<input type="text" name="login_val" />
		<input type="submit" value="Login" />
	</form>
</div>
<div id = "content">
	<form name="searchbook" action="mwly_guest.php" method="post">
	search book:
		<select name="search_by">
  		<option value="title">title</option>
  		<option value="call_no">call no.</option>
  		<option value="author">author</option>
		</select>
		<input type="text" name="search_val" />
		<input type="submit" value="Search" />
	</form>
</div>
<div id = "content3">
<?php
if ((isset($_POST['search_by']) && !empty($_POST['search_by']))
&& (isset($_POST['search_val']) && !empty($_POST['search_val']))) {
	echo "<h2>BOOKS INFO</h2>
        <table>
                <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Call No.</th>
			<th>Year</th>
                </tr>";
	$search_by = $_POST['search_by'];
	$search_val = $_POST['search_val'];
	$sql = "select * from book where $search_by like '%$search_val%'";
	$stmt = oci_parse($conn, $sql);
	oci_execute($stmt, OCI_DEFAULT);
	while ($res = oci_fetch_row($stmt))
	{
		echo "<tr>" ;
		for ($i=1; $i<5; $i++)
			echo "<td>$res[$i]</td>";			
		echo "</tr>" ;
	}
	echo "</table>";
}
?>
</div>
</body>
</html>

