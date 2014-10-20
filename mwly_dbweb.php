<p>Hello world!</p>
<p>
<?php
	ini_set('display_errors', 'On');
	require 'mwly_conn.php';
	# $conn returned. Don't forget oci_close($conn) !!
	$stmt = oci_parse($conn, "select user from dual");
	oci_execute($stmt, OCI_DEFAULT);
	while ($res = oci_fetch_row($stmt))
	{
		echo "User Name: ". $res[0] ;
	}
	oci_close($conn);
?>
</p>
