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
			<p>First Name:		<input type="text" name="firstName"/></p>
			<p>Last Name:		<input type="text" name="lastName"/></p>
			<p>email address:	<input type="text" name="email" required="required"/></p>	
			<p>District ID:		<input type="text" name="districtId" required="required"/></p>
			<p><input type="submit" value="Register"/></p>
		</form>
	</body>
</html>

<?php
	// Register a username and password for the User

	// Start the sessions that hold the user information for the pages.
	session_start();

	// If the page has come from a Post submission.
	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		// Get the information submitted by the form.
		$username = mysql_real_escape_string($_POST['username']);
		$password = mysql_real_escape_string($_POST['password']);
		$firstName = mysql_real_escape_string($_POST['firstName']);
		$lastName = mysql_real_escape_string($_POST['lastName']);
		$email = mysql_real_escape_string($_POST['email']);
		$districtId = mysql_real_escape_string($_POST['districtId']);

		// Connect to the database.
		require("connectToDB.php");

		// Make sure the email address isn't already in the database.
		$emailQuery = "SELECT email FROM devpoll.users WHERE email = ?";

		$estatement = $conn->prepare($emailQuery);
		$estatement->bind_param('s', $email);

		$estatement->execute();

		$estatement->bind_result($returned_email);

		$estatement->fetch();

		if (!is_null($returned_email) && !empty($returned_email))
		{
			echo '<script>alert("This email is already registered. If you have forgotten your username or password, please click the forgot login link");</script>';
			echo '<script>window.location.assign("login.php");</script>';	// Redirect to login page.
		}
		else
		{
			// There are no emails.

			// Make sure the username isn't already taken.
			$userNameQuery = "SELECT userid FROM devpoll.security WHERE userid = ?";

			$ustatement = $conn->prepare($userNameQuery);
			$ustatement->bind_param('s', $username);

			$ustatement->execute();

			$ustatement->bind_result($returned_username);

			$ustatement->fatch();

			if (!is_null($returned_username) && !empty($returned_username))
			{
				// If the username exists in the database allow the user to enter a different username.
				echo '<script>alert("This username is already taken. Please choose another username.");';
				echo '<script>document.getElementById("username").value = "";</script>';				
			}
			else
			{
				// The username doesn't exist - write the username to the database.
				try
				{
					// Start a transaction.
					$conn->autocommit(false);

					// Insert into the security table.
					$securityQuery = "INSERT INTO security(userid, password) VALUES (?, ?);";

					$sstatement = $conn->prepare($securityQuery);
					$sstatement->bind_param('ss', $username, $password);

					$sstatement->execute();

					// Insert into the users table.
					$usersQuery = "INSERT INTO users(userid, districtId, firstName, lastName, email) VALUES (?, ?, ?, ?, ?);";

					$usstatement = $conn->prepare($usersQuery);
					$usstatement->bind_param('sisss', $username, $districtId, $firstName, $lastName, $email);

					$usstatement->execute();

					$conn->commit();

					$_SESSION['user'] = $username;

					// Close the prepared statement.
					$sstatement->free_result();

					// Registration successful.
					echo '<script>alert("Successfully Registered");</script>';
					echo '<script>window.location.assign("welcome.php");</script>';
				}
				catch(Exception $e)
				{
					echo "Error ".$conn->error."<br/>";
						
					$conn->rollback();

					// echo out some JavaScript code
					echo '<script>alert("There was a problem registering, please try again`");</script>';
					echo '<script>window.location.assign("login.php");</script>';
				}
			}

			// Close the prepared statement.
			$usstatement->free_result();
		}

		// Close the prepared statement.
		$estatement->free_result();

		// Close the database connection.
		$conn->close();
	}
?>
