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
	<title>ReaderManagement</title>
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
		<a href="mwly_book_io.php">check/return book</a>
	<br>
		<a href="mwly_book_man.php">add/delete book</a>
	<br>
		<a href="mwly_book_trace.php">trace book</a>
	<br>
		<a href="mwly_search.php">return</a>
	<br>
		<a href="mwly_login.php?action=logout">log out</a>
</div>
<div id="result">
<div id="displaytwo">
	<h>ADD READER</h>
	<form name="addreader" action="" method="post">
		<table>
			<tr>
				<td>name:</td>
				<td><input type="text" name="readername" /></td>
			</tr>
			<tr>
				<td>gender:</td>
				<td><input type="text" name="gender" /></td>
			</tr>
			<tr>
				<td>quota:</td>
				<td><input type="text" name="quota" /></td>
			</tr>
		</table>
		<input type="submit" value="submit" />
	</form>
</div>
<div id="displaytwo">
	<h>DELETE READER</h>
	<form name="deletereader" action="" method="post">
		<table>
			<tr>
				<td>reader id:</td>
				<td><input type="text" name="readerid" /></td>
			</tr>
		</table>
		<input type="submit" value="submit" />
	</form>
</div>
</div>
<div id = "search">	
	<form name="searchreader" action="" method="post">
	SEARCH READER:
		<select>
  		<option value="readerid">reader id</option>
  		<option value="readername">reader name</option>
		</select>
		<input type="text" name="reader" />
		<input type="submit" value="Search" />
	</form>
</div>
<div id="result2">
	<table class="gridtable">
		<tr>
			<th>id</th>
			<th>name</th>
			<th>gender</th>
			<th>reader quota</th>
		</tr>
		<tr>
			<td>1</td>
			<td>WM</td>
			<td>M</td>
			<td>10</td>
		</tr>
	</table>
</div>
</body>
</html>
