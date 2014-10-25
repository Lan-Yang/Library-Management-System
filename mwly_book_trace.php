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
	<title>BookTrace</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="classes.css">
</head>

<body>
<div id = "banner">
	<h1><?php echo $_SESSION['library_name']?></h1>
</div>
<div id = "search">	
	<form name="tracebook" action="" method="post">
	Book id:
		<input type="text" name="book" />
		<input type="submit" value="submit" />
	</form>
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
		<a href="mwly_librarian.php">check/return book</a>
	<br>
		<a href="mwly_book_man.php">add/delete book</a>
	<br>
		<a href="mwly_reader_man.php">add/delete/find reader</a>
	<br>
		<a href="mwly_search.php">return</a>
	<br>
		<a href="mwly_login.php?action=logout">log out</a>
</div>
<div id="result">
<div id="displaytwo">
	<h>CHECK OUT HISTORY</h>
		<table class="gridtable">
			<tr>
				<td>trans id</td>
				<td>reader id</td>
				<td>time</td>
				<td>librarian id</td>
			<tr>
				<td>1</td>
				<td>1</td>
				<td>2014.9.30</td>
				<td>1</td>
			</tr>
		</table>
</div>
<div id="displaytwo">
	<h>RETURN HISTORY</h>
	<table class="gridtable">
			<tr>
				<td>trans id</td>
				<td>reader id</td>
				<td>time</td>
				<td>librarian id</td>
			<tr>
				<td>2</td>
				<td>1</td>
				<td>2014.10.3</td>
				<td>1</td>
			</tr>
		</table>
</div>
</div>
</body>
</html>