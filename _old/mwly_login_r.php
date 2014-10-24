<!DOCTYPE html>
<html>
<head>
	<title>Reader Frame</title>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
	<style>
	table, th, td {
		border: 1px solid black;
	}
	</style>
</head>

<body>
<div id = "banner">
	<h1>Columbia University Library</h1>
</div>
<div id = "content">	
	<form name="searchbook" action="" method="post">
	SEARCH BOOK:
		<select>
  		<option value="title">title</option>
  		<option value="ISBN number">ISBN</option>
  		<option value="author">author</option>
		</select>
		<input type="text" name="book" />
		<input type="submit" value="Search" />
	</form>
</div>
<div id = "content2">
	<h2>PERSONAL INFO</h2>
	<table>
	<tr>
		<td>name:</td>
		<td>Meng Wang</td>
	</tr>
	<tr>
		<td>reader id:</td>
		<td>1</td>
	</tr>
	<tr>
		<td>gender:</td>
		<td>M</td>
	</tr>
	<tr>
		<td>reader quota:</td>
		<td>10</td>
	</tr>
	<tr>
		<td>remaning quota:</td>
		<td>10</td>
	</tr>
	</table>
</div>

<div>
<div id = "content3">
	<h2>BOOKS INFO</h2>
	<table>
		<tr>
			<th>book id</th>
			<th>book title</th>
			<th>due date</th>
		</tr>
		<tr>
			<td>1</td>
			<td>ABC</td>
			<td>9.30</td>
		</tr>
	</table>
</div>
</body>
</html>
