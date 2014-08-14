<?php
	session_start();

	$questionNumber = mysql_real_escape_string($_POST['questionNumber']);
	$questionType = mysql_real_escape_string($_POST['createType']);
	$questionText = mysql_real_escape_string($_POST['questionText']);
	$trueFalseType = mysql_real_escape_string($_POST['trueFalsetype']);
	$trueFalsecustom1 = mysql_real_escape_string($_POST['trueFalsecustom1']);
	$trueFalsecustom2 = mysql_real_escape_string($_POST['trueFalsecustom2']);	
	$surveyId = mysql_real_escape_string($_POST['surveyId']);
	$surveyName = mysql_real_escape_string($_POST['surveyName']);
	$everyQuestion = mysql_real_escape_string($_POST['everyQuestion']);

	// Get the type of headings for the true/false question. 
	switch ($trueFalseType)
	{
		case "ab":
			$trueFalseHeading1 = 'A';
			$trueFalseHeading2 = 'B';
			break;
		case "yesno":
			$trueFalseHeading1 = 'Yes';
			$trueFalseHeading2 = 'No';
			break;
		case "custom":
			$trueFalseHeading1 = mysql_real_escape_string($_POST['trueFalsecustom1']);
			$trueFalseHeading2 = mysql_real_escape_string($_POST['trueFalsecustom2']);
			break;
		default:
			$trueFalseHeading1 = 'True';
			$trueFalseHeading2 = 'False';
			break;
	}

	addTrueFalse($surveyId, $questionNumber, $questionType, $questionText, $trueFalseHeading1, $trueFalseHeading2);

	$_SESSION['surveyInProgress'] = 'YES';
	$_SESSION['surveyId'] = $surveyId;
	$_SESSION['surveyName'] = $surveyName;
	$_SESSION['questionNumber'] = $questionNumber;
	$_SESSION['everyQuestion'] = $everyQuestion;

	header('Location: createsurvey.php');

	// ----------------------------------------------------------------------
	// Add the trueFalse question to the database.
	// ----------------------------------------------------------------------
	function addTrueFalse($surveyId, $questionNumber, $questionType, $questionText, $trueFalseHeading1, $trueFalseHeading2)
	{
		// Connect to the database.
		include("connectToDB.php");

		// Start a transaction.
		$conn->autocommit(false);

		// Insert the Question into the database.
		$conn->query("INSERT INTO questions(surveyId, questionNumber, questionText, questionType, lastmodified) 
							VALUES ($surveyId, $questionNumber, '$questionText', 'trueFalse', now());");

		// Insert the first Answer into the database. 
		$conn->query("INSERT INTO answers(surveyId, questionNumber, answerNumber, answerText) 
							VALUES($surveyId, $questionNumber, 0, '$trueFalseHeading1');");

		// Insert the second Answer into the database.
		$conn->query("INSERT INTO answers(surveyId, questionNumber, answerNumber, answerText) 
							VALUES($surveyId, $questionNumber, 1, '$trueFalseHeading2');");

		// Commit the transaction.
		$conn->commit();		

		// Close the connetion.
		$conn->close();
	}
