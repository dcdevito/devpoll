<?php	
	// Check the username and password entered by the user against the database

	// Start the sessions that hold the user information for the pages.
	session_start();

	// Get the username and password submitted by the form.
	$username = mysql_real_escape_string($_POST['username']);
	$password = mysql_real_escape_string($_POST['password']);

	// Import the database connection code. 
	require("connectToDB.php");
	
	$sql = "SELECT userid, password FROM devpoll.security WHERE userid = ?";

	$statement = $conn->prepare($sql);
	$statement->bind_param('s', $username);

	$statement->execute();

	$statement->bind_result($returned_userid, $returned_password);

	$statement->fetch();

	// Compare the entered username and password to make sure they are allowed in.
	if (($username == $returned_userid) && ($password == $returned_password))
	{
		$_SESSION['user'] = $username;
		header("location: welcome.php");	// Redirects user to authenticated home page
	}
	else
	{
		// Display the incorrect login error message.
		echo '<script>alert("Incorrect Login");</script>';
		echo '<script>window.location.assign("login.php");</script>';	// Redirect to login page
	}

	// Close the connection.
	$statement->free_result();
	$conn->close();
?>
