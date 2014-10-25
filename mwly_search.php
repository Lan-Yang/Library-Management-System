<!DOCTYPE html>
<html>
<?php
ini_set('display_errors', 'On');
session_start();
if (!isset($_SESSION['library_id']) || empty($_SESSION['library_id'])) {
	if (!isset($_POST['library_id']) || empty($_POST['library_id'])) {
		header("Location: mwly_dbweb.php");
	} else {
		$lib_id = $_POST['library_id'];
		$_SESSION['library_id'] = $lib_id;
	}
} else {
	$lib_id = $_SESSION['library_id'];
}
require 'mwly_conn.inc';
if (!isset($_SESSION['library_name'])) {
	$stmt = oci_parse($conn, "select library_name from library where 
				library_id=$lib_id");
	oci_execute($stmt, OCI_DEFAULT);
	while ($res = oci_fetch_row($stmt))
	{
		$library_name = $res[0] ;
	}
	$_SESSION['library_name'] = $library_name;
} else {
	$library_name = $_SESSION['library_name'];
}
?>
<head>
	<title>Search</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="classes.css">
</head>

<body>
<div id = "banner">
<h1><?php echo "$library_name"?></h1>
</div>
<div id = "search">
	<form name="searchbook" action="mwly_search.php" method="post">
	SEARCH BOOK:
		<select name="search_by">
  		<option value="title">title</option>
  		<option value="call_no">Call No.</option>
  		<option value="author">author</option>
		</select>
		<input type="text" name="search_val" />
		<input type="submit" value="Search" />
	</form>
</div>
<div id = "navlist">
	<?php
	if (isset($_SESSION['reader_id'])) {
		echo "<h2>Welcome,<br>".$_SESSION['reader_name']."</h2>";
	?>
		<a href="mwly_reader.php">user info</a><br>
		<a href="mwly_login.php?action=logout">log out</a>
	<?php
	} else if (isset($_SESSION['librarian_id'])) {
		echo "<h2>Welcome,<br>".$_SESSION['librarian_name']."</h2>";
	?>
		<a href="mwly_librarian.php">user info</a><br>
		<a href="mwly_login.php?action=logout">log out</a>
	<?php
	} else {
	?>
	<h2>log in</h2>
	<form name="login" action="mwly_login.php" method="post">
		<select name = "login_as">
  		<option value="reader">reader</option>
  		<br>
  		<option value="librarian">librarian</option>
  		<br>
		</select>
		<input type="text" name="login_id" />
		<br>
		<input type="submit" value="Login" />
	</form>
	<?php	
	}
	?>
</div>
<div id="result">
	<table class="gridtable">
<?php
if ((isset($_POST['search_by']) && !empty($_POST['search_by']))
&& (isset($_POST['search_val']) && !empty($_POST['search_val']))) {
	echo "<tr>
		<th>ID</th>
		<th>Title</th>
		<th>Author</th>
		<th>Call No.</th>
		<th>Year</th>
		<th>Language</th>
		<th>Status</th>
		</tr>";
	$search_by = $_POST['search_by'];
	$search_val = $_POST['search_val'];
	$sql = "SELECT book_id, title, author, call_no, pub_year, lang,
		case when ctime<rtime then 'Available' 
		    when ctime is null then 'Available'
		    when ctime>rtime then 'Unavailable'
		    when rtime is null then 'Unavailable'
		    else 'N/A'
		end as status
		FROM ( select B.*,
		       (select max(t.trans_time)
			from check_out c, trans t
			where c.book_id=B.book_id
			  and c.trans_id=t.trans_id) as ctime, 
		       (select max(t.trans_time)
			from return_back r, trans t
			where r.book_id=B.book_id
			  and r.trans_id=t.trans_id) as rtime
			FROM book B) B0
		where $search_by like '%$search_val%'";
	//echo $sql;
	$stmt = oci_parse($conn, $sql);
	oci_execute($stmt, OCI_DEFAULT);
	while ($res = oci_fetch_row($stmt))
	{
		echo "<tr>" ;
		for ($i=0; $i<7; $i++)
			echo "<td>$res[$i]</td>";
		echo "</tr>";
	}
	echo "</table>";
}
?>
	</table>
</div>
</body>
</html>
