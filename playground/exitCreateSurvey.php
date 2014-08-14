<?php
	session_start();
	
	$_SESSION['surveyInProgress'] = "";
	$_SESSION['surveyId'] = "";
	$_SESSION['surveyName'] = "";
	$_SESSION['questionNumber'] = "";
	$_SESSION['everyQuestion'] = "";

	header("location: welcome.php");
?>
