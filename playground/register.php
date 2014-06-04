<html>
	<head>
		<title>DevPoll Registration</title>
	</head>
	<body>
		<h2>Register</h2>
		<a href="login.php">Back</a>

		<form action="register.php" method="POST">
			<p>Username:		<input type="text" name="username" required="required"/></p>
			<p>Password:		<input type="password" name="password" required="required"/></p>
			<p>First Name:		<input type="text" name="firstname"/></p>
			<p>Last Name:		<input type="text" name="lastname"/></p>
			<p>email address:	<input type="text" name="email" required="required"/></p>	
			<p>District ID:		<input type="text" name="districtid" required="required"/></p>
			<p><input type="submit" value="Register"/></p>
		</form>
	</body>
</html>

<?php
	session_start();

	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		// Get the information submitted by the form.
		$username = mysql_real_escape_string($_POST['username']);
		$password = mysql_real_escape_string($_POST['password']);
		$firstname = mysql_real_escape_string($_POST['firstname']);
		$lastname = mysql_real_escape_string($_POST['lastname']);
		$email = mysql_real_escape_string($_POST['email']);
		$districtid = mysql_real_escape_string($_POST['districtid']);

		// Connect to the database.
		require_once("connectToDB.php");

		// Make sure the email address isn't already in the database.
		$emailRS = $conn->query("SELECT email FROM users WHERE email = '$email'");

		if ($emailRS === false)
		{
			trigger_error('A problem has occurred registering: '.$conn->error, E_USER_ERROR);
		}
		else
		{
			$emailExists = $emailRS->num_rows;

			// If the number of rows is greater than 0 then the email exists in the database.
			if ($emailExists > 0)
			{
				echo '<script>alert("This email is already registered. If you have forgotten your username or password, please click the forgot login link");</script>';
				echo '<script>window.location.assign("login.php");</script>';	// Redirect to login page.
			}
			else
			{
				// Make sure the username isn't already taken.
				$usernameQuery = $conn->query("SELECT username FROM security WHERE userid = '$username'");

				if ($rs === false)
				{
					trigger_error('A problem has occurred registering: '.$conn->error, E_USER_ERROR);
				}
				else
				{
					$usernameExists = $usernameQuery->num_rows;

					// If the number of rows is greater than 0 then the username exists in the database.
					// If this is the case allow the user to enter a different username.
					if ($usernameExists > 0)
					{
						echo '<script>alert("This username is already taken. Please choose another username.");';
						echo '<script>document.getElementById("username").value = "";</script>';
					}
					else
					{
						try
						{
							// Start a transaction.
							$conn->autocommit(false);

							// Insert the values into the database.
							$conn->query("INSERT INTO security(userid, accesscode) VALUES ('$username', '$password');") or die(mysql_error());

							$conn->query("INSERT INTO users(userid, districtid, firstname, lastname, email) VALUES ('$username', $districtid, '$firstname', '$lastname', '$email');");

							// Commit the transaction.
							$conn->commit();

							$_SESSION['user'] = $username;

							// echo out some JavaScript code
							echo '<script>alert("Successfully Registered");</script>';
							echo '<script>window.location.assign("welcome.php");</script>';
						}
						catch(Exception $e)
						{
							$conn->rollback();

							// echo out some JavaScript code
							echo '<script>alert("There was a problem registering, please try again`");</script>';
							echo '<script>window.location.assign("login.php");</script>';
						}
					}
				}
			}
		}

		// Close the database connection.
		$conn->close();
	}
?>
