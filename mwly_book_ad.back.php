<?php
function error_return($msg, $next)
{
	echo '<script type="text/javascript">alert("'
			. $msg . '");
			location.href = "'
			. $next . '";
			</script>';
	exit();
}

$nexturl = "mwly_book_man.php";
ini_set('display_errors', 'On');
session_start();
require 'mwly_conn.inc';
if (isset($_POST['post-type'])) {
	switch ($_POST['post-type']) {
	case "add_book":
		$title = trim($_POST['title']);
		$author = trim($_POST['author']);
		$callno = trim($_POST['call_no']);
		$puby = intval($_POST['pub_year']);
		$puby = $puby == 0 ? "" : $puby ;
		$lang = trim($_POST['lang']);
		$lper = intval($_POST['loan_period']);
		if (empty($title))
			error_return("Must have a title!", $nexturl);
		if ($lper <= 0)
			error_return("Loan period cannot be 0!", $nexturl);
		if (empty($lang))
			error_return("Language cannot be empty!", $nexturl);
		$sql = "insert into book
			values (
			  (select max(book_id)+1
			   from book), 
			   '$title',
			   '$author',
			   '$callno',
			   $puby,
			   '$lang',
			   $lper,
			   {$_SESSION['library_id']},
			   1)";
		$stmt = oci_parse($conn, $sql);
		$ret = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
		if ($ret)
			error_return("Add book success", $nexturl);	
		else
			error_return("Add book fail!", $nexturl);
		break;
	case "del_book":
		$bookid = intval($_POST['bookid']);
		if ($bookid > 0) {
			$sql = "delete from book 
				where book_id=$bookid";
			$stmt = oci_parse($conn, $sql);
			oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
		}
		break;
	}
}
?>
