<!DOCTYPE html>
<html>
<?php
ini_set('display_errors', 'On');
session_start();
if (!isset($_SESSION['library_id']))
	header("Location: mwly_dbweb.php");
// librarian info
require 'mwly_conn.inc';
if (!isset($_SESSION['librarian_id']))
	header("Location:mwly_search.php");
$linfo_k = array("id:", "name:");
$sql = "select * from librarian 
	where librarian_id=" . $_SESSION['librarian_id'] .
	" and library_id=" . $_SESSION['library_id'];
//echo $sql;
$stmt = oci_parse($conn, $sql);
oci_execute($stmt, OCI_DEFAULT);
while ($res = oci_fetch_row($stmt))
	$linfo_v = $res;
if (!isset($linfo_v))
	header("Location:mwly_search.php");
?>
<head>
	<title>Management</title>
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
	for ($i=0; $i<2; $i++) {
		echo "<tr>";
		echo "<td>".$linfo_k[$i]."</td>";
		echo "<td>".$linfo_v[$i]."</td>";
		echo "</tr>";
	}
	?>
	</table>
		<a href="mwly_bookmanagement.html">add/delete book</a>
	<br>
		<a href="mwly_readermanagement.html">add/delete reader</a>
	<br>
		<a href="mwly_tracebook.html">trace book</a>
	<br>
		<a href="mwly_search.php">return</a>
	<br>
		<a href="mwly_login.php?action=logout">log out</a>
</div>
<div id="result">
<div id="displaytwo">
	<h2>CHECK OUT</h2>
	<form name="checkout" action="" method="post">
		<table>
			<tr>
				<td>book id:</td>
				<td><input type="text" name="bookid" /></td>
			</tr>
			<tr>
				<td>reader id:</td>
				<td><input type="text" name="readerid" /></td>
			</tr>
		</table>
		<input type="submit" value="submit" />
	</form>
</div>
<div id="displaytwo">
	<h2>RETURN</h2>
	<form name="return" action="" method="post">
		<table>
			<tr>
				<td>book id:</td>
				<td><input type="text" name="bookid" /></td>
			</tr>
		</table>
		<input type="submit" value="submit" />
	</form>
</div>
</div>
</body>
</html>
