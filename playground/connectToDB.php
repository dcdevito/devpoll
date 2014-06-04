<?php
	// Connect to the database.
	define("HOSTNAME", "localhost");
	define("USERNAME", "devpoll");
	define("PASSWORD", "devpoll");
	define("DB_NAME", "devpoll");

	$conn = new mysqli(HOSTNAME, USERNAME, PASSWORD, DB_NAME);

	if ($conn->connect_errno)
	{
		echo "<br/>Failed to connect to database.";
		echo $conn->connect_errno." ".$conn->connect_error;
	}
	//else
	//{
	//	echo "Connection successful ".$conn->host_info;
	//}

	// These are depreciated methods of connecting.
	//mysql_connect("localhost:8888", "devpoll", "devpoll") or die(mysql_error());
	//mysql_select_db("devpoll") or die("Cannot connect to Database");
?>
