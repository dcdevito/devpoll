<?php
	session_start();

	$username = mysql_real_escape_string($_POST['username']);
	$password = mysql_real_escape_string($_POST['password']);

	require("connectToDB.php");
	
	if ($rs = $conn->query("SELECT userid, accesscode FROM security WHERE userid = '$username'"))
	{
		$exists = $rs->num_rows;		// Check if name exists

		if ($exists > 0)
		{
			// Fetch the rows into an associative array.
			$arr = $rs->fetch_array(MYSQLI_ASSOC);

			// Get the userid and accesscode from the table.
			$tableUsers = $arr['userid'];
			$tablePassword = $arr['accesscode'];

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
				echo '<script>alert("Incorrect Login");</script>';
				echo '<script>window.location.assign("login.php");</script>';	// Redirect to login page
			}
		}
		else
		{
			echo '<script>alert("Incorrect Login");</script>';
			echo '<script>window.location.assign("login.php");</script>';	// Redirect to login page
		}
	}
	else
	{
		trigger_error('A problem has occurred checking the login: '.$conn->error, E_USER_ERROR);
	}

	// Close the connection.
	$conn->close();
?>