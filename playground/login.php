<?php
	/****************************
		Display the login page
	****************************/
?>

<html>
	<head>
		<title>DevPoll Sign In</title>
	</head>
	<body>
		<h2>Sign In</h2>

		<form action="checklogin.php" method="POST">
			<p>Username:	<input type="text" name="username" required="required"/></p>
			<p>Password:	<input type="password" name="password" required="required"/></p>
			<p><input type="submit" value="Login"/></p>
			<br/>
			<p><a href="forgot.php">Forgot login</a></p>
			<br/>
			<a href="register.php">Register for an account</a>
		</form>
	</body>
</html>

