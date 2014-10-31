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
$stmt = oci_parse($conn, $sql);
oci_execute($stmt, OCI_DEFAULT);
if ($res = oci_fetch_row($stmt))
	$linfo_v = $res;
else
	header("Location:mwly_search.php");
?>
<head>
	<title>PatronManagement</title>
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
		<a href="mwly_reader_man.php">add/delete reader</a>
	<br>
		<a href="mwly_book_trace.php">trace book</a>
	<br>
		<a href="mwly_search.php">return</a>
	<br>
		<a href="mwly_login.back.php?action=logout">log out</a>
</div>
<div id="result">
<?php
if (isset($_POST['post-type'])) {
	switch ($_POST['post-type']) {
	case "search_patron":
		$sql = "select p.patron_id, p.patron_name, p.patron_intro
			from patron p 
			left join sponsor_of s
			on s.library_id={$_SESSION['library_id']}
			    and p.patron_id=s.patron_id 
			where p.{$_POST['search_by']}";
		
		if ($_POST['search_by']=='patron_id') {
			$patron_id = intval($_POST['search_val']);
			$sql .= "=$patron_id";
		} else {
			$patron_name = trim($_POST['search_val']);
			$sql .= " like '%$patron_name%'";
		}
		echo $sql;
		$stmt = oci_parse($conn, $sql);
		oci_execute($stmt, OCI_DEFAULT);
		$seach_flag = 1;
		break;
	}
}
?>
<div id="displaytwo">
	<h2>ADD PATRON</h2>
	<form name="add_patron" action="mwly_patron_ad.back.php" method="post">
		<table>
			<tr>
			<td>name:</td>
			<td><input type="text" name="patron_name" /></td>
			</tr>
			<tr>
			<td>info:</td>
			<td><input type="text" name="patron_info" /></td>
			</tr>
			<tr>
		</table>
		<button type="submit" name="post-type" value="add_patron">
		Submit</button>
	</form>
</div>
<div id="displaytwo">
	<h2>DELETE PATRON</h2>
	<form name="del_patron" action="mwly_patron_ad.back.php" method="post">
		<table>
			<tr>
			<td>patron id:</td>
			<td><input type="text" name="patron_id" /></td>
			</tr>
		</table>
		<button type="submit" name="post-type" value="del_patron">
		Submit</button>
	</form>
</div>
</div>
<div id = "search">
	<form name="search_patron" action="" method="post">
	Search Patron:
		<select name="search_by">
  		<option value="patron_id">patron id</option>
  		<option value="patron_name">patron name</option>
		</select>
		<input type="text" name="search_val" />
		<button type="submit" name="post-type" value="search_patron">
		Search</button>
	</form>
</div>
<div id="result2">
	<table class="gridtable">
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Info</th>
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
