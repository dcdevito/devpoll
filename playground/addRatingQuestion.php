<?php
	session_start();

	$questionNumber = mysql_real_escape_string($_POST['questionNumber']);
	$questionType = mysql_real_escape_string($_POST['createType']);
	$questionText = mysql_real_escape_string($_POST['questionText']);
	$surveyId = mysql_real_escape_string($_POST['surveyId']);
	$surveyName = mysql_real_escape_string($_POST['surveyName']);
	$everyQuestion = mysql_real_escape_string($_POST['everyQuestion']);

	$value = mysql_real_escape_string($_POST['numberOfDescriptions']);
	$ratingValue = mysql_real_escape_string($_POST['ratingValue']);

	$lowValue = 1;
	$highValue= $ratingValue;

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

		// Start a transaction.
		$conn->autocommit(false);

		// Insert the values into the database.
		$conn->query("INSERT INTO questions(surveyId, questionNumber, questionText, questionType, lastmodified) 
							VALUES ($surveyId, $questionNumber, '$questionText', 'rating', now());");

		$conn->query("INSERT INTO answers(surveyId, questionNumber, answerNumber, lowValue, highvalue, lowdescription, highdescription) 
							VALUES($surveyId, $questionNumber, 0, $lowValue, $highValue, '$lowDescription', '$highDescription');");

		// Commit the transaction.
		$conn->commit();		

		if ($conn->errno != 0)
		{
			echo "error ".$conn->error."<br/>";
		}

		// Close the connetion.
		$conn->close();
	}
?>