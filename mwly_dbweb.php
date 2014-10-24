<!DOCTYPE html>
<?php
require "mwly_conn.inc";
?>
<html>
<head>
	<title>Book Management System Main Frame</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="classes.css">
</head>

<body>
<div id = "banner">
	<h1>Welcome to Book Management System</h1>
</div>
<div id = "content">
	<form name="input" action="mwly_guest.php" method="post">
	SELECT LIBRARY:
		<select name="library_id">
		<?php
		$stmt = oci_parse($conn, "select * from library");
		oci_execute($stmt, OCI_DEFAULT);
		while ($res = oci_fetch_row($stmt))
			echo "<option value=\"$res[0]\">$res[1]</option>";
		?>
		</select>
		<input type="submit" value="Submit"/>
	</form>
</div>
<?php oci_close($conn); ?>
</body>
</html>
