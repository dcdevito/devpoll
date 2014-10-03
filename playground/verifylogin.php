<?php
	/****************************************
		Check that the user session exists
	****************************************/
?>

<?php
	// Start the sessions that hold the user information for the pages.
	session_start();

	// If the user session does not exist redirect to the index page.
	if (! $_SESSION['user'])
	{
		header("location: index.php");
	}

	// Set a variable to the user session.
	$user = $_SESSION['user'];
?>
