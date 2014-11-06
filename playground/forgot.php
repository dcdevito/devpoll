<html>
	<head>
		<title>DevPoll Forgot Login</title>
	</head>
	<body>
		<h2>Forgot Login</h2>
		<a href="login.php">Back</a>

		<form action="forgot.php" method="POST">
			<p>Username:		<input type="text" name="username" required="required"/></p>
			<p>email address:	<input type="text" name="email" required="required"/></p>	
			<p><input type="submit" value="Send email"/></p>
		</form>
	</body>
</html>

<?php
	// Start the sessions that hold the page information for the user.
	session_start();

	// If the page has come from a Post submission.
	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		// Get the information submitted by the form.
		$username = mysql_real_escape_string($_POST['username']);
		$email = mysql_real_escape_string($_POST['email']);

		// Connect to the database.
		require("connectToDB.php");

		// Make sure the email address is in the database.
		$sql = "SELECT s.userid, u.email, s.password FROM devpoll.security s join devpoll.users u ON s.userid = u.userid WHERE s.userid = '$username' AND u.email='$email'";

		// Obtain a resultset from the query.
		if ($rs = $conn->query($sql))
		{
			// If there are any number of rows returned then the username and email exist in the database.
			$emailExists = $rs->num_rows;

			// If the number of rows is greater than 0 then the email exists in the database.
			if ($emailExists > 0)
			{
				echo '<script>alert("This username/email combination is not registered, please try again");</script>';
				echo '<script>window.location.assign("login.php");</script>';	// Redirect to login page.
			}
			else
			{
				// Create the email to send to the user with the login information.
				require("class.phpmailer.php");
				$mail = new PHPMailer();

				$mail->IsSMTP();
				$mail->Mailer = 'smtp';
				$mail->SMTPAuth = true;
				$mail->Host = 'smtp.gmail.com';
				$mail->Port = 465;
				$mail->SMTPSecure = 'ssl';

				$mail->Username = "fatsquirrel2013@gmail.com";
				$mail->Password = "ihatehimsomuch";

				$mail->IsHTML(false);
				$mail->SingleTo = false;
				$mail->From = "devpoll@devpoll.net";
				$mail->FromName = "DevPoll";

				$mail->addAddress($email, $username);
				$mail->Subject = "Forgotten Login";

				$mail->Body = "Your password is <br/> password";

				// Send the mail.
				if (!$mail->Send())
				{
					// The send was not successful.
					echo "Message was not sent <br/>The error was ".$mail->ErrorInfo;
				}
				else
				{
					// The mail was sent successfully.
					echo "Message sent successfully";
				}

				// Link back the login page.
				echo '<a href="login.php">Return to Login page"</a>';
			}
		}		
		else
		{
			// Trigger a predefined constant E_USER_ERROR.
			trigger_error($conn->error, E_USER_ERROR);
		}
		
		// Close the database connection.
		$conn->close();
	}
?>