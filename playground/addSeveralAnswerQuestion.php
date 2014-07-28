<?php
	session_start();

	$questionNumber = mysql_real_escape_string($_POST['questionNumber']);
	$questionType = mysql_real_escape_string($_POST['createType']);
	$questionText = mysql_real_escape_string($_POST['questionText']);
	$numberOfAnswers = mysql_real_escape_string($_POST['numberOfAnswers']);
	$surveyId = mysql_real_escape_string($_POST['surveyId']);
	$surveyName = mysql_real_escape_string($_POST['surveyName']);
	$everyQuestion = mysql_real_escape_string($_POST['everyQuestion']);

	echo "Question Type = $questionType<br/>";

	// Loop through the answers and create an array of the values.
	$answers = array();
	for ($i = 1; $i <= $numberOfAnswers; $i++)
	{
		$answerNum = 'sevanswer'.$i;

		$answer = mysql_real_escape_string($_POST[$answerNum]);
		$answers[] = $answer;
	}

	addSeveralAnswer($surveyId, $questionNumber, $questionType, $questionText, $numberOfAnswers, $answers);

	$_SESSION['surveyInProgress'] = 'YES';
	$_SESSION['surveyId'] = $surveyId;
	$_SESSION['surveyName'] = $surveyName;
	$_SESSION['questionNumber'] = $questionNumber;
	$_SESSION['everyQuestion'] = $everyQuestion;

	header('Location: createsurvey.php');

	// ----------------------------------------------------------------------
	// Add the severalAnswer question to the database.
	// ----------------------------------------------------------------------
	function addSeveralAnswer($surveyId, $questionNumber, $questionType, $questionText, $numberOfAnswers, $answers)
	{
		// Connect to the database.
		include("connectToDB.php");

		// Get the surveyId.
		//$surveyId = $_SESSION['surveyId'];

		// Start a transaction.
		$conn->autocommit(false);

		// Insert the values into the database.
		$questionQuery = "INSERT INTO questions(surveyId, questionNumber, questionText, questionType, lastmodified) 
							VALUES ($surveyId, $questionNumber, '$questionText', 'severalAnswer', now());";

		$conn->query($questionQuery);

		if ($numberOfAnswers > 0)
		{
			for ($i = 0; $i < $numberOfAnswers; $i++)
			{
				$answer = $answers[$i];

				$answerQuery = "INSERT INTO answers(surveyId, questionNumber, answerNumber, answerText)
									VALUES($surveyId, $questionNumber, $i, '$answer');";

				$conn->query($answerQuery);
			}
		}

		// Commit the transaction.
		$conn->commit();		

		// Close the connetion.
		$conn->close();
	}
?>