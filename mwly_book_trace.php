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
	<form name="tracebook" action="mwly_book_trace.php" method="post">
	Book id:
		<input type="text" name="search_val" />
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
<?php

?>
<div id="displaytwo">
	<h2>CHECK OUT HISTORY</h2>
		<table class="gridtable">
		<?php
if (isset($_POST['search_val']) && !empty($_POST['search_val'])) {
	echo "<tr>
				<td>trans id</td>
				<td>reader id</td>
				<td>time</td>
				<td>librarian id</td>
		</tr>";
	$search_val = $_POST['search_val'];
	$sql = "select c.trans_id, c.reader_id, t.trans_time, t.librarian_id
					from check_out c, trans t
					where c.book_id=$search_val and c.trans_id=t.trans_id";
	//echo $sql;
	$stmt = oci_parse($conn, $sql);
	oci_execute($stmt, OCI_DEFAULT);
	while ($res = oci_fetch_row($stmt))
	{
		echo "<tr>" ;
		for ($i=0; $i<4; $i++)
			echo "<td>$res[$i]</td>";
		echo "</tr>";
	}
}
?>
		</table>
</div>
<div id="displaytwo">
	<h2>RETURN HISTORY</h2>
	<table class="gridtable">
	<?php
	if (isset($_POST['search_val']) && !empty($_POST['search_val'])) {
	echo "<tr>
				<td>trans id</td>
				<td>reader id</td>
				<td>time</td>
				<td>librarian id</td>
		</tr>";
	$sql = "select r.trans_id, r.reader_id, t.trans_time, t.librarian_id
					from return_back r, trans t
					where r.book_id=$search_val and r.trans_id=t.trans_id";
	//echo $sql;
	$stmt = oci_parse($conn, $sql);
	oci_execute($stmt, OCI_DEFAULT);
	while ($res = oci_fetch_row($stmt))
	{
		echo "<tr>" ;
		for ($i=0; $i<4; $i++)
			echo "<td>$res[$i]</td>";
		echo "</tr>";
	}
	}
?>
		</table>
</div>
</div>
</body>
</html>
