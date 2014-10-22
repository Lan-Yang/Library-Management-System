<!DOCTYPE html>
<html>
<head>
<title>DBWEB</title>
</head>
<body>

<h1>My First Heading</h1>

<p>My first paragraph.</p>
<p>
<?php
	ini_set('display_errors', 'On');
	require 'mwly_conn.php';
	# $conn returned. Don't forget oci_close($conn) !!
	$stmt = oci_parse($conn, "select * from library");
	oci_execute($stmt, OCI_DEFAULT);
	while ($res = oci_fetch_row($stmt))
	{
		echo "Library ". $res[0] . ": " . $res[1] .  "<br>";
	}
	oci_close($conn);
?>
</p>
</body>
</html>

