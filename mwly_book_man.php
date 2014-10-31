<!DOCTYPE html>
<html>
<?php
ini_set('display_errors', 'On');
session_start();
$libraryid = $_SESSION['library_id'];
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
if ($res = oci_fetch_row($stmt))
	$linfo_v = $res;
else
	header("Location:mwly_search.php");
?>
<head>
	<title>Book Management</title>
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
		<a href="mwly_librarian.php">check/return book</a>
	<br>
		<a href="mwly_reader_man.php">add/delete/find reader</a>
	<br>
		<a href="mwly_book_trace.php">trace book</a>
	<br>
		<a href="mwly_search.php">return</a>
	<br>
		<a href="mwly_login.back.php?action=logout">log out</a>
</div>
<div id="result">
<div id="displaytwo">
	<h2>ADD BOOK</h2>
	<form name="addbook" action="mwly_book_ad.back.php" method="post">
		<table>		
			<tr>
				<td>title:</td>
				<td><input type="text" name="title" /></td>
			</tr>
			<tr>
				<td>author:</td>
				<td><input type="text" name="author" /></td>
			</tr>
			<tr>
				<td>call number:</td>
				<td><input type="text" name="call_no" /></td>
			</tr>
			<tr>
				<td>publish year:</td>
				<td><input type="text" name="pub_year" /></td>
			</tr>
			<tr>
				<td>language:</td>
				<td><input type="text" name="lang" /></td>
			</tr>
			<tr>
				<td>loan period:</td>
				<td><input type="text" name="loan_period" /></td>
			</tr>
			<tr>
				<td>patron id:</td>
				<td><input type="text" name="patron_id" /></td>
			</tr>
		</table>
		<button type="submit" name="post-type" value="add_book">
		Submit</button>
	</form>
</div>
<div id="displaytwo">
	<h2>DELETE BOOK</h2>
	<form name="deletebook" action="mwly_book_ad.back.php" method="post">
		<table>
			<tr>
				<td>book id:</td>
				<td><input type="text" name="bookid" /></td>
			</tr>
		</table>
		<button type="submit" name="post-type" value="del_book">
		Submit</button>
	</form>
</div>
</div>
</body>
</html>
