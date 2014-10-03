<?php
	/*****************************
		Connect to the database
	*****************************/
?>

<?php
	// Connect to the database.
	define("HOSTNAME", "localhost");
	define("USERNAME", "devpoll");
	define("PASSWORD", "devpoll");
	define("DB_NAME", "devpoll");

	// Connection to the database - using MySQLi.
	$conn = new mysqli(HOSTNAME, USERNAME, PASSWORD, DB_NAME);

	// If the connection cannot connect - show an error.
	if ($conn->connect_errno)
	{
		echo "<br/>Failed to connect to database.";
		echo $conn->connect_errno." ".$conn->connect_error;
	}
?>
