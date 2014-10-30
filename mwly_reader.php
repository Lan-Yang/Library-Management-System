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
$rid = $_SESSION['reader_id'];
$rinfo_k = array("id:", "name:", "gender:", "quota:");
$stmt = oci_parse($conn, "select * from reader 
	where reader_id=$rid");
oci_execute($stmt, OCI_DEFAULT);
while ($res = oci_fetch_row($stmt)) // user info
	$rinfo_v = $res;
// reader holding books
$sql = "SELECT b.book_id, b.title, (i.out_time+b.loan_period) as due_time
	FROM
	(SELECT ch.book_id as book_id,ch.out_time as out_time
	FROM
	  (SELECT c.book_id AS book_id,
		  max(t.trans_time) AS out_time
	   FROM check_out c, trans t
	   WHERE c.reader_id=$rid
	     AND c.trans_id=t.trans_id
	   GROUP BY c.book_id ) ch
	LEFT OUTER JOIN
	  (SELECT r.book_id AS book_id,
		  max(t.trans_time) AS in_time
	   FROM return_back r, trans t
	   WHERE r.reader_id=$rid
	     AND r.trans_id=t.trans_id
	   GROUP BY r.book_id ) re ON re.book_id=ch.book_id
	WHERE ch.out_time > re.in_time OR re.in_time is NULL)i, book b
	WHERE b.book_id=i.book_id";
$stmt = oci_parse($conn, $sql);
oci_execute($stmt, OCI_DEFAULT);
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
<div id="result">
	<h2>Holding Books</h2>
	<table class="gridtable">
		<tr>
			<th>book id</th>
			<th>title</th>
			<th>due date</th>
		</tr>
		<?php
		for ($j=0; $res = oci_fetch_row($stmt); $j++) {// book info
			echo "<tr>";
			for ($i=0; $i<3; $i++) 
				echo "<td>".$res[$i]."</td>";
			echo "</tr>";
		}
		?>
	</table>
</div>
<div id = "navlist">
	<h2>PERSONAL INFO</h2>
	<table>
	<?php
	$rinfo_v[3] = strval($j)."/".$rinfo_v[3]."used";
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
		<a href="mwly_login.back.php?action=logout">log out</a>
</div>
</body>
</html>
