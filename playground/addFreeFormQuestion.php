<?php
	session_start();

	$questionNumber = mysql_real_escape_string($_POST['questionNumber']);
	$questionType = mysql_real_escape_string($_POST['createType']);
	$questionText = mysql_real_escape_string($_POST['questionText']);
	$surveyId = mysql_real_escape_string($_POST['surveyId']);
	$surveyName = mysql_real_escape_string($_POST['surveyName']);
	$everyQuestion = mysql_real_escape_string($_POST['everyQuestion']);

	addFreeForm($surveyId, $questionNumber, $questionType, $questionText);

	$_SESSION['surveyInProgress'] = 'YES';
	$_SESSION['surveyId'] = $surveyId;
	$_SESSION['surveyName'] = $surveyName;
	$_SESSION['questionNumber'] = $questionNumber;
	$_SESSION['everyQuestion'] = $everyQuestion;

	header('Location: createsurvey.php');
	
	// ----------------------------------------------------------------------
	// Add the free form question to the database.
	// ----------------------------------------------------------------------
	function addFreeForm($surveyId, $questionNumber, $questionType, $questionText)
	{
		// Connect to the database.
		include("connectToDB.php");

		// Start a transaction.
		$conn->autocommit(false);

		// Insert the values into the database.
		$conn->query("INSERT INTO questions(surveyId, questionNumber, questionText, questionType, lastmodified) 
							VALUES ($surveyId, $questionNumber, '$questionText', 'freeForm', now());");

		$conn->query("INSERT INTO answers(surveyId, questionNumber, answerNumber, answerText) 
							VALUES($surveyId, $questionNumber, 0, '');");

		// Commit the transaction.
		$conn->commit();		

		// Close the connetion.
		$conn->close();
	}
?>