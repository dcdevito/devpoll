<?php
	$questionNumber = mysql_real_escape_string($_POST['questionNumber']);
	$questionType = mysql_real_escape_string($_POST['createType']);
	$questionText = mysql_real_escape_string($_POST['questionText']);
	$trueFalseType = mysql_real_escape_string($_POST['trueFalsetype']);
	$trueFalsecustom1 = mysql_real_escape_string($_POST['trueFalsecustom1']);
	$trueFalsecustom2 = mysql_real_escape_string($_POST['trueFalsecustom2']);	
	$sid = mysql_real_escape_string($_POST['surveyId']);

	$questionNumberX = $_POST['questionNumber'];
	$questionTypeX = $_POST['createType'];
	$questionTextX = $_POST['questionText'];
	$trueFalseTypeX = $_POST['trueFalsetype'];
	$trueFalsecustom1X = $_POST['trueFalsecustom1'];
	$trueFalsecustom2X = $_POST['trueFalsecustom2'];	

	$questionNumberXX = mysql_real_escape_string($_POST['questionNumberXX']);

	//$sid = $_SESSION['surveyId'];

	echo "Survey ID = $sid<br/>";

	echo "questionNumber = $questionNumber<br/>";
	echo "questionType = $questionType<br/>";
	echo "questionText = $questionText<br/>";
	echo "trueFalseType = $trueFalseType<br/>";
	echo "trueFalsecustom1 = $trueFalsecustom1<br/>";
	echo "trueFalsecustom2 = $trueFalsecustom2<br/>";
	echo "surveyId = $sid<br/>";

	echo "questionNumberX = $questionNumberX<br/>";
	echo "questionTypeX = $questionTypeX<br/>";
	echo "questionTextX = $questionTextX<br/>";
	echo "trueFalseTypeX = $trueFalseTypeX<br/>";
	echo "trueFalsecustom1X = $trueFalsecustom1X<br/>";
	echo "trueFalsecustom2 = $trueFalsecustom2<br/>";

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
			$trueFalseHeading1 = 'true';
			$trueFalseHeading2 = 'false';
			break;
	}

	addTrueFalse($sid, $questionNumber, $questionType, $questionText, $trueFalseHeading1, $trueFalseHeading2);
	header('Location: createsurvey.php');

	// ----------------------------------------------------------------------
	// Add the trueFalse question to the database.
	// ----------------------------------------------------------------------
	function addTrueFalse($sid, $questionNumber, $questionType, $questionText, $trueFalseHeading1, $trueFalseHeading2)
	{
		// Connect to the database.
		include("connectToDB.php");

		// Get the surveyId.
		//$surveyId = $_SESSION['surveyId'];

		// Start a transaction.
		$conn->autocommit(false);

		echo "About to add the question<br/>";

		// Insert the values into the database.
		$conn->query("INSERT INTO questions(surveyId, questionNumber, questionText, questionType, lastmodified) 
							VALUES ($sid, $questionNumber, '$questionText', 'trueFalse', now());");

		echo "About to add answer 1<br/>";

		$answerQuery = "INSERT INTO answers(surveyId, questionNumber, answerNumber, answerText) 
							VALUES($sid, $questionNumber, 0, '$trueFalseHeading1');";
		echo "Answer 1 Query = ".$answerQuery."<br/>";

		$conn->query("INSERT INTO answers(surveyId, questionNumber, answerNumber, answerText) 
							VALUES($sid, $questionNumber, 0, '$trueFalseHeading1');");

		echo "Answer 1 added<br/>";

		echo $conn->errno."<br/>";
		echo $conn->error."<br/>";

		echo "About to add answer 2<br/>";

		$answerQuery = "INSERT INTO answers(surveyId, questionNumber, answerNumber, answerText) 
							VALUES($sid, $questionNumber, 1, '$trueFalseHeading2');";
		echo "Answer 2 Query = ".$answerQuery."<br/>";

		$conn->query("INSERT INTO answers(surveyId, questionNumber, answerNumber, answerText) 
							VALUES($sid, $questionNumber, 1, '$trueFalseHeading2');");

		echo "Answer 2 added<br/>";

		echo $conn->errno."<br/>";
		echo $conn->error."<br/>";

		// Commit the transaction.
		$conn->commit();		

		// Close the connetion.
		$conn->close();
	}
