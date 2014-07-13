<?php
	$questionNumber = mysql_real_escape_string($_POST['questionNumber']);
	$questionType = mysql_real_escape_string($_POST['questionType']);
	$questionText = mysql_real_escape_string($_POST['questionText']);
	$sid = mysql_real_escape_string($_POST['surveyId']);

	addFreeForm($sid, $questionNumber, $questionType, $questionText);
	header('Location: createsurvey.php');
	
	// ----------------------------------------------------------------------
	// Add the free form question to the database.
	// ----------------------------------------------------------------------
	function addFreeForm($sid, $questionNumber, $questionType, $questionText)
	{
		// Connect to the database.
		include("connectToDB.php");

		// Get the surveyId.
		//$surveyId = $_SESSION['surveyId'];

		// Start a transaction.
		$conn->autocommit(false);

		// Insert the values into the database.
		$conn->query("INSERT INTO questions(surveyId, questionNumber, questionText, questionType, lastmodified) 
							VALUES ($sid, $questionNumber, '$questionText', 'freeForm', now());");

		$conn->query("INSERT INTO answers(surveyId, questionNumber, answerNumber, answerText) 
							VALUES($sid, $questionNumber, 0, '');");

		// Commit the transaction.
		$conn->commit();		

		// Close the connetion.
		$conn->close();
	}
?>