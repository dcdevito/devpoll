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
	session_start();

	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		echo "We are in the post<br/>";

		// Get the information submitted by the form.
		$username = mysql_real_escape_string($_POST['username']);
		$password = mysql_real_escape_string($_POST['password']);
		$firstName = mysql_real_escape_string($_POST['firstName']);
		$lastName = mysql_real_escape_string($_POST['lastName']);
		$email = mysql_real_escape_string($_POST['email']);
		$districtId = mysql_real_escape_string($_POST['districtId']);

		echo "
			Username = $username<br/>
			Password = $password<br/>
			First Name = $firstName<br/>
			Last Name = $lastName<br/>
			email = $email<br/>
			districtId = $districtId<br/>
		";

		echo "About to include code <br/>";

		// Connect to the database.
		require("connectToDB.php");

		echo "We included connectToDB<br/>";

		// Make sure the email address isn't already in the database.
		$emailQuery = "SELECT email FROM devpoll.users WHERE email = '$email'";

		echo "emailQuery = $emailQuery<br/>";

		if ($emailRS = $conn->query($emailQuery))
		{
			echo "No problem - Check if the email exists<br/>";

			$emailExists = $emailRS->num_rows;

			echo "How many emails are there = $emailExists<br/>";

			// If the number of rows is greater than 0 then the email exists in the database.
			if ($emailExists > 0)
			{
				echo "emailExists is greater than 0<br/>";

				echo '<script>alert("This email is already registered. If you have forgotten your username or password, please click the forgot login link");</script>';
				echo '<script>window.location.assign("login.php");</script>';	// Redirect to login page.
			}
			else
			{
				echo "There are no emails<br/>";

				// Make sure the username isn't already taken.
				if ($usernameRS = $conn->query("SELECT userid FROM devpoll.security WHERE userid = '$username'"))
				{
					echo "No problem - Check if username exists<br/>";

					$usernameExists = $usernameRS->num_rows;

					echo "usernameExists = $usernameExists<br/>";

					// If the number of rows is greater than 0 then the username exists in the database.
					// If this is the case allow the user to enter a different username.
					if ($usernameExists > 0)
					{
						echo "usernameExists is greater than zero - the username already exists<br/>";

						echo '<script>alert("This username is already taken. Please choose another username.");';
						echo '<script>document.getElementById("username").value = "";</script>';
					}
					else
					{
						echo "The username doesn't exist - write the username to the database<br/>";

						try
						{
							// Start a transaction.
							$conn->autocommit(false);

							// Insert the values into the database.
							$conn->query("INSERT INTO security(userid, accesscode) VALUES ('$username', '$password');") or die(mysql_error());

							echo "Add to security query: $conn->error<br/>";

							$conn->query("INSERT INTO users(userid, districtId, firstName, lastName, email) VALUES ('$username', $districtId, '$firstName', '$lastName', '$email');");

							echo "Add to user query: $conn->error<br/>";

							// Commit the transaction.
							$conn->commit();

							echo "We committed the code<br/>";

							$_SESSION['user'] = $username;

							// echo out some JavaScript code
							echo "Registration successful<br/>";

							echo '<script>alert("Successfully Registered");</script>';
							echo '<script>window.location.assign("welcome.php");</script>';
						}
						catch(Exception $e)
						{
							echo "We are in the catch exception<br/>";

							echo "Error ".$conn->error."<br/>";
							
							$conn->rollback();

							// echo out some JavaScript code
							echo '<script>alert("There was a problem registering, please try again`");</script>';
							echo '<script>window.location.assign("login.php");</script>';
						}
					}
				}
				else
				{
					echo "A problem has occurred<br/>";
					echo "Error = $conn->error<br/>";

					trigger_error('A problem has occurred registering: '.$conn->error, E_USER_ERROR);
				}
			}
		}
		else
		{
			echo "A problem has occurred<br/>";
			echo "Error = $conn->error<br/>";

			trigger_error('A problem has occurred registering: '.$conn->error, E_USER_ERROR);
		}

		// Close the database connection.
		$conn->close();
	}
?>
