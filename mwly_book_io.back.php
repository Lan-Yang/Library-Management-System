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

$nexturl = "mwly_librarian.php";
ini_set('display_errors', 'On');
session_start();
require "mwly_conn.inc";
if (isset($_POST['post-type']) && !empty($_POST['post-type'])) {
	$bookid = intval($_POST['bookid']);
	$lbrarian_id = $_SESSION['librarian_id'];
	// check bookid
	$sql = "select is_available from book 
		where book_id=$bookid and own_by_library=".
		$_SESSION['library_id'];
	$stmt = oci_parse($conn, $sql);
	oci_execute($stmt, OCI_DEFAULT);
	if ($res = oci_fetch_row($stmt))
		$available = $res[0];
	else
		error_return("No such book", $nexturl);
	// generate transid
	$stmt = oci_parse($conn, "select max(trans_id)+1 from trans");
	oci_execute($stmt, OCI_DEFAULT);
	if ($res = oci_fetch_row($stmt))
		$transid = intval($res[0]);
	else
		error_return("Cannot get new transaction ID", $nexturl);
	switch ($_POST['post-type']) {
	case "checkout":
		// check book status
		if ($available != 1)
			error_return("Book not available!", $nexturl);
		// check readerid
		$readerid = intval($_POST['readerid']);
		$stmt = oci_parse($conn, "select reader_quota from reader 
			where reader_id=$readerid");
		oci_execute($stmt, OCI_DEFAULT);
		if ($res = oci_fetch_row($stmt))
			$reader_quota = $res[0];
		else
			error_return("No such reader", $nexturl);
		// check reader holding books
		$sql = "SELECT count(b.book_id) FROM
			(SELECT ch.book_id as book_id,ch.out_time as out_time
			FROM
			  (SELECT c.book_id AS book_id,
				  max(t.trans_time) AS out_time
			   FROM check_out c, trans t
			   WHERE c.reader_id=$readerid
			     AND c.trans_id=t.trans_id
			   GROUP BY c.book_id ) ch
			LEFT OUTER JOIN
			  (SELECT r.book_id AS book_id,
				  max(t.trans_time) AS in_time
			   FROM return_back r, trans t
			   WHERE r.reader_id=$readerid
			     AND r.trans_id=t.trans_id
			   GROUP BY r.book_id ) re ON re.book_id=ch.book_id
			WHERE ch.out_time > re.in_time OR re.in_time is NULL)i, 
			book b
			WHERE b.book_id=i.book_id";
		$stmt = oci_parse($conn, $sql);
		oci_execute($stmt, OCI_DEFAULT);
		if ($res = oci_fetch_row($stmt)) {
			if ($res[0] >= $reader_quota)
				error_return("No enough quota", $nexturl);
		} else
			error_return("Cannot get quota", $nexturl);
		// insert transaction records & update book status
		$sql = "insert into trans
			values ($transid, SYSDATE, $lbrarian_id)";
		$stmt = oci_parse($conn, $sql);
		oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
		$sql = "insert into check_out
			values ($readerid, $bookid, $transid)";
		$stmt = oci_parse($conn, $sql);
		oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
		$sql = "update book
			set is_available = 0
			where book_id=$bookid";
		$stmt = oci_parse($conn, $sql);
		oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
		error_return("Check out success!", $nexturl);
		break;
	case "return":
		// check book status
		if ($available != 0) {
			error_return("Book not borrowed!", $nexturl);
		}
		$sql = "select v.reader_id, SYSDATE-v.trans_time as tl,
			(select loan_period from book 
			 where book_id=$bookid) lp
			from (select c.reader_id as reader_id, 
				     t.trans_time as trans_time,
				     max(t.trans_time) over() as l_trans_time
			      from check_out c
			      join trans t 
			      on c.trans_id=t.trans_id
			      where c.book_id=$bookid) v
			where v.trans_time = v.l_trans_time";
		$stmt = oci_parse($conn, $sql);
		oci_execute($stmt, OCI_DEFAULT);
		if ($res = oci_fetch_row($stmt)) {
			$readerid = intval($res[0]);
			$ckday = intval($res[1]); // days holding book
			$lplim = intval($res[2]); // limit of holding time
		} else
			error_return("Book not borrowed!", $nexturl);
		$sql = "insert into trans
			values ($transid, SYSDATE, $lbrarian_id)";
		$stmt = oci_parse($conn, $sql);
		oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
		$sql = "insert into return_back
			values ($bookid, $transid, $readerid)";
		$stmt = oci_parse($conn, $sql);
		oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
		$sql = "update book
			set is_available = 1
			where book_id=$bookid";
		$stmt = oci_parse($conn, $sql);
		oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
		$msg = "Return success!";
		if (($d = $ckday-$lplim) > 0 )
			$msg .= "\n But $d days later due date";
		error_return($msg, $nexturl);		
		break;
	}
}
//header("Location:$nexturl");
?>