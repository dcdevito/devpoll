<?php
	session_start();

	$questionNumber = mysql_real_escape_string($_POST['questionNumber']);
	$questionType = mysql_real_escape_string($_POST['questionType']);
	$questionText = mysql_real_escape_string($_POST['questionText']);
	$surveyId = mysql_real_escape_string($_POST['surveyId']);
	$surveyName = mysql_real_escape_string($_POST['surveyName']);
	$everyQuestion = mysql_real_escape_string($_POST['everyQuestion']);

	//$is1LowValue = mysql_real_escape_string($_POST['rating1Low']);
	$value = mysql_real_escape_string($_POST['values']);

	$lowValue = 1;
	$highValue= $value;

	$lowDescription = mysql_real_escape_string($_POST['ratingLowValue']);
	$highDescription = mysql_real_escape_string($_POST['ratingHighValue']);

	addRating($surveyId, $questionNumber, $questionType, $questionText, $lowValue, $highValue, $lowDescription, $highDescription);

	$_SESSION['surveyInProgress'] = 'YES';
	$_SESSION['surveyId'] = $surveyId;
	$_SESSION['surveyName'] = $surveyName;
	$_SESSION['questionNumber'] = $questionNumber;
	$_SESSION['everyQuestion'] = $everyQuestion;
	
	header('Location: createsurvey.php');

	// ----------------------------------------------------------------------
	// Add the rating question to the database.
	// ----------------------------------------------------------------------
	function addRating($surveyId, $questionNumber, $questionType, $questionText, $lowValue, $highValue, $lowDescription, $highDescription)
	{
		// Connect to the database.
		include("connectToDB.php");

		// Get the surveyId.
		$surveyId = $_SESSION['surveyId'];

		// Start a transaction.
		$conn->autocommit(false);

		// Insert the values into the database.
		$conn->query("INSERT INTO questions(surveyId, questionNumber, questionText, questionType, lastmodified) 
							VALUES ($surveyId, $questionNumber, '$questionText', 'rating', now());");

		$conn->query("INSERT INTO answers(surveyId, questionNumber, answerNumber, lowValue, highvalue, lowdescription, highdescription) 
							VALUES($surveyId, $questionNumber, 0, $lowValue, $highValue, '$lowDescription', '$highDescription');");

		// Commit the transaction.
		$conn->commit();		

		// Close the connetion.
		$conn->close();
	}
?>