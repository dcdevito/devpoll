<?php
	$questionNumber = mysql_real_escape_string($_POST['questionNumber']);
	$questionType = mysql_real_escape_string($_POST['questionType']);
	$questionText = mysql_real_escape_string($_POST['questionText']);
	$sid = mysql_real_escape_string($_POST['surveyId']);

	//$is1LowValue = mysql_real_escape_string($_POST['rating1Low']);
	$value = mysql_real_escape_string($_POST['values']);

	//if ($is1LowValue == 'no')
	//{
	//	$lowValue = $value;
	//	$highValue = 1;
	//}
	//else
	//{
		$lowValue = 1;
		$highValue= $value;
	//}

	$lowDescription = mysql_real_escape_string($_POST['ratingLowValue']);
	$highDescription = mysql_real_escape_string($_POST['ratingHighValue']);

	addRating($sid, $questionNumber, $questionType, $questionText, $lowValue, $highValue, $lowDescription, $highDescription);
	header('Location: createsurvey.php');

	// ----------------------------------------------------------------------
	// Add the rating question to the database.
	// ----------------------------------------------------------------------
	function addRating($sid, $questionNumber, $questionType, $questionText, $lowValue, $highValue, $lowDescription, $highDescription)
	{
		// Connect to the database.
		include("connectToDB.php");

		// Get the surveyId.
		$surveyId = $_SESSION['surveyId'];

		// Start a transaction.
		$conn->autocommit(false);

		// Insert the values into the database.
		$conn->query("INSERT INTO questions(surveyId, questionNumber, questionText, questionType, lastmodified) 
							VALUES ($sid, $questionNumber, '$questionText', 'rating', now());");

		$conn->query("INSERT INTO answers(surveyId, questionNumber, answerNumber, lowValue, highvalue, lowdescription, highdescription) 
							VALUES($sid, $questionNumber, 0, $lowValue, $highValue, '$lowDescription', '$highDescription');");

		// Commit the transaction.
		$conn->commit();		

		// Close the connetion.
		$conn->close();
	}
?>