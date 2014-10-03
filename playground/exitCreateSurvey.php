<?php
	/****************************************************************
		Clear out the session values needed to create the surveys
	****************************************************************/
?>

<?php
	session_start();
	
	// Clear out the session values.
	$_SESSION['surveyInProgress'] = "";
	$_SESSION['surveyId'] = "";
	$_SESSION['surveyName'] = "";
	$_SESSION['questionNumber'] = "";
	$_SESSION['everyQuestion'] = "";

	// Redirect to the welcome screen.
	header("location: welcome.php");
?>
