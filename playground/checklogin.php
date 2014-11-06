<?php	
	// Check the username and password entered by the user against the database

	// Start the sessions that hold the user information for the pages.
	session_start();

	// Get the username and password submitted by the form.
	$username = mysql_real_escape_string($_POST['username']);
	$password = mysql_real_escape_string($_POST['password']);

	// Import the database connection code. 
	require("connectToDB.php");
	
	// If the username and password exist in the database.
	if ($rs = $conn->query("SELECT userid, password FROM devpoll.security WHERE userid = '$username'"))
	{
		$exists = $rs->num_rows;		// Check if name exists

		// If the name exists - the number of rows will be greater than 0.
		if ($exists > 0)
		{
			// Fetch the rows into an associative array.
			$arr = $rs->fetch_array(MYSQLI_ASSOC);

			// Get the userid and password from the table.
			$tableUsers = $arr['userid'];
			$tablePassword = $arr['password'];

			// Compare the entered username and password to make sure they are allowed in.
			if (($username == $tableUsers) && ($password == $tablePassword))
			{
				if ($password = $tablePassword)
				{
					$_SESSION['user'] = $username;
					header("location: welcome.php");	// Redirects user to authenticated home page
				}
			}
			else
			{
				// Display the incorrect login error message.
				echo '<script>alert("Incorrect Login");</script>';
				echo '<script>window.location.assign("login.php");</script>';	// Redirect to login page
			}
		}
		else
		{
			// Display the incorrect login error message.
			echo '<script>alert("Incorrect Login");</script>';
			echo '<script>window.location.assign("login.php");</script>';	// Redirect to login page
		}
	}
	else
	{
		// Database error checking the username and password.
		trigger_error('A problem has occurred checking the login: '.$conn->error, E_USER_ERROR);
	}

	// Close the connection.
	$conn->close();
?>
