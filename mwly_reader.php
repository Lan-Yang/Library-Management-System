<!DOCTYPE html>
<html>
<?php
ini_set('display_errors', 'On');
session_start();
if (!isset($_SESSION['library_id']))
	header("Location: mwly_dbweb.php");
// reader info
require 'mwly_conn.inc';
if (!isset($_SESSION['reader_id']))
	header("Location:mwly_search.php");
$rinfo_k = array("id:", "name:", "gender:", "quota:");
$stmt = oci_parse($conn, "select * from reader 
	where reader_id=" . $_SESSION['reader_id']);
oci_execute($stmt, OCI_DEFAULT);
while ($res = oci_fetch_row($stmt)) // user info
	$rinfo_v = $res;
$rinfo_v[3] = "x" . "/" . $rinfo_v[3];
// reader holding books
$sql = "";
$stmt = oci_parse($conn, $sql);
oci_execute($stmt, OCI_DEFAULT);
while ($res = oci_fetch_row($stmt)) // user info
	$rinfo_v = $res;
?>
<head>
	<title>ReaderInfo</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="classes.css">
</head>

<body>
<div id = "banner">
	<h1><?php echo $_SESSION['library_name']?></h1>
</div>
<div id = "navlist">
	<h2>PERSONAL INFO</h2>
	<table>
	<?php 
	for ($i=0; $i<4; $i++) {
		echo "<tr>";
		echo "<td>".$rinfo_k[$i]."</td>";
		echo "<td>".$rinfo_v[$i]."</td>";
		echo "</tr>";
	}
	?>
	</table>
		return to <a href="mwly_search.php">search</a>
	<br>
		<a href="mwly_login.php?action=logout">log out</a>
</div>
<div id="result">
	<table class="gridtable">
		<tr>
			<th>book id</th>
			<th>title</th>
			<th>due date</th>
		</tr>
		<tr>
			<td>1</td>
			<td>ABC</td>
			<td>9.30</td>
		</tr>
	</table>
</div>
</body>
</html>
