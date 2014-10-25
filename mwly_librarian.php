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
		<a href="mwly_book_man.php">add/delete book</a>
	<br>
		<a href="mwly_reader_man.php">add/delete/find reader</a>
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
	$bookid = $_POST['bookid'];
	$lbrarian_id = $_SESSION['librarian_id'];
	$stmt = oci_parse($conn, "select max(trans_id)+1 from trans");
	oci_execute($stmt, OCI_DEFAULT);
	while ($res = oci_fetch_row($stmt))
		$transid = $res[0];
	switch ($_POST['post-type']) {
	case "checkout":
		$readerid = $_POST['readerid'];
		$sql = "insert into trans
			values ($transid, SYSDATE, $lbrarian_id)";
		$stmt = oci_parse($conn, $sql);
		oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
		$sql = "insert into check_out
			values ($readerid, $bookid, $transid)";
		$stmt = oci_parse($conn, $sql);
		oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
		break;
	case "return":
		$sql = "select v.reader_id, v.trans_time
			from (select c.reader_id as reader_id, 
				     t.trans_time as trans_time,
				     max(t.trans_time) over() as l_trans_time
			      from check_out c
			      join trans t 
			      on c.trans_id=t.trans_id
			      where c.book_id=$bookid) v
			where v.trans_time = v.l_trans_time";
		echo $sql.'<br>';
		$stmt = oci_parse($conn, $sql);
		oci_execute($stmt, OCI_DEFAULT);
		while ($res = oci_fetch_row($stmt)) {
			$readerid = $res[0];
			$cktime = $res[1];
		}
		$sql = "insert into trans
			values ($transid, SYSDATE, $lbrarian_id)";
		echo $sql.'<br>';
		$stmt = oci_parse($conn, $sql);
		oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
		$sql = "insert into return_back
			values ($bookid, $transid, $readerid)";
		echo $sql.'<br>';
		$stmt = oci_parse($conn, $sql);
		oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
		break;
	}
}
?>
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
		<button type="submit" name="post-type" value="checkout">
		Submit</button>
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
		<button type="submit" name="post-type" value="return">
		Submit</button>
	</form>
</div>
</div>
</body>
</html>
