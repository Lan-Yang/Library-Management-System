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
if ($res = oci_fetch_row($stmt))
	$linfo_v = $res;
else
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
		<a href="mwly_librarian.php">check/return book</a>
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
<?php
if (isset($_POST['post-type'])) {
	switch ($_POST['post-type']) {
	case "add_reader":
		$name = trim($_POST['reader_name']);
		$gender = ($_POST['gender']=="M") ? "M" : "F";
		$quota = intval($_POST['reader_quota']);
		$sql = "insert into reader
			values (
			  (select max(reader_id)+1
			   from reader), '$name',
			   '$gender', $quota)";
		$stmt = oci_parse($conn, $sql);
		oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
		break;
	case "del_reader":
		$readerid = intval($_POST['reader_id']);
		$sql = "delete from reader 
			where reader_id=$readerid";
		$stmt = oci_parse($conn, $sql);
		oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
		break;
	case "search_reader":
		$sql = "select * from reader 
			where ".$_POST['search_by'];
		if ($_POST['search_by']=='reader_id')
			$sql .= "=".intval($_POST['search_val']);
		else
			$sql .= " like '%".trim($_POST['search_val'])."%'";
		$stmt = oci_parse($conn, $sql);
		oci_execute($stmt, OCI_DEFAULT);
		$seach_flag = 1;
		break;
	}
}
?>
<div id="displaytwo">
	<h2>ADD READER</h2>
	<form name="add_reader" action="" method="post">
		<table>
			<tr>
				<td>name:</td>
				<td><input type="text" name="reader_name" /></td>
			</tr>
			<tr>
				<td>gender:</td>
				<td><input type="text" name="gender" /></td>
			</tr>
			<tr>
				<td>quota:</td>
				<td><input type="text" name="reader_quota" /></td>
			</tr>
		</table>
		<button type="submit" name="post-type" value="add_reader">
		Submit</button>
	</form>
</div>
<div id="displaytwo">
	<h2>DELETE READER</h2>
	<form name="del_reader" action="" method="post">
		<table>
			<tr>
				<td>reader id:</td>
				<td><input type="text" name="reader_id" /></td>
			</tr>
		</table>
		<button type="submit" name="post-type" value="del_reader">
		Submit</button>
	</form>
</div>
</div>
<div id = "search">
	<form name="search_reader" action="" method="post">
	SEARCH READER:
		<select name="search_by">
  		<option value="reader_id">reader id</option>
  		<option value="reader_name">reader name</option>
		</select>
		<input type="text" name="search_val" />
		<button type="submit" name="post-type" value="search_reader">
		Search</button>
	</form>
</div>
<div id="result2">
	<table class="gridtable">
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Gender</th>
			<th>Quota</th>
		</tr>
		<?php
		if (isset($seach_flag)) {
			while ($res=oci_fetch_row($stmt)) {
				echo "<tr>";
				foreach ($res as $v)
					echo "<td>".$v."</td>";
				echo "</tr>";
			}
		}
		?>
	</table>
</div>
</body>
</html>
