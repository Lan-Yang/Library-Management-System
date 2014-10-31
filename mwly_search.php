<!DOCTYPE html>
<html>
<?php
ini_set('display_errors', 'On');
session_start();
if (isset($_POST['library_id']) && !empty($_POST['library_id'])) {
	$lib_id = intval($_POST['library_id']);
	$_SESSION['library_id'] = $lib_id;
}

if (!isset($_SESSION['library_id']))
	header("Location: mwly_dbweb.php");

$lib_id = $_SESSION['library_id'];

require 'mwly_conn.inc';
$stmt = oci_parse($conn, "select library_name from library where 
			library_id=$lib_id");
oci_execute($stmt, OCI_DEFAULT);
while ($res = oci_fetch_row($stmt))
	$library_name = $res[0] ;
$_SESSION['library_name'] = $library_name;
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
		<a href="mwly_login.back.php?action=logout">log out</a>
	<?php
	} else if (isset($_SESSION['librarian_id'])) {
		echo "<h2>Welcome,<br>".$_SESSION['librarian_name']."</h2>";
	?>
		<a href="mwly_librarian.php">user info</a><br>
		<a href="mwly_login.back.php?action=logout">log out</a>
	<?php
	} else {
	?>
	<h2>log in</h2>
	<form name="login" action="mwly_login.back.php" method="post">
		<input type="radio" name="login_as" value="reader" /> 
		reader
		<input type="radio" name="login_as" value="librarian" /> 
		librarian
		<br>
		<input type="text" name="login_id" />
		<br>
		<input type="submit" value="Login" />
	</form>
	<br><a href="mwly_dbweb.php">Change library</a>
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
		<th>Status</th>";
	if (isset($_SESSION['librarian_id'])) {
		echo "
			<th>Sponser</th>
			<th>Sponsed_Date</th>";
	}
	echo "</tr>";
	$search_by = $_POST['search_by'];
	$search_val = $_POST['search_val'];
	if (isset($_SESSION['librarian_id'])) {
		$sql = "SELECT b.book_id, b.title, b.author, 
		b.call_no, b.pub_year, b.lang, 
		case when b.is_available=1 then 'Available'
		else 'Unavailable' end, 
		pp.patron_name, pp.pay_for_date
		FROM book b
    		LEFT OUTER JOIN
    		(select pf.book_id,p.patron_name,pf.PAY_FOR_DATE  
    		from pay_for pf, patron p
    		where pf.patron_id = p.patron_id) pp  
    		ON b.book_id = pp.book_id
		where b.$search_by like '%$search_val%'
		  and b.own_by_library=$lib_id
		order by b.$search_by";
		$num_col = 9;
	} else {
		$sql = "SELECT book_id, title, author, call_no, pub_year, lang, 
		case when is_available=1 then 'Available' 
		     else 'Unavailable' end
		FROM book
		where $search_by like '%$search_val%'
		  and own_by_library=$lib_id
		order by $search_by";
		$num_col = 7;
	}
	$stmt = oci_parse($conn, $sql);
	oci_execute($stmt, OCI_DEFAULT);
	while ($res = oci_fetch_row($stmt))
	{
		echo "<tr>" ;
		for ($i=0; $i<$num_col; $i++)
			echo "<td>{$res[$i]}</td>";
		echo "</tr>";
	}
}
?>
	</table>
</div>
</body>
</html>
