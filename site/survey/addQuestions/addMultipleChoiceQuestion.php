<?php
	// Add the Multiple Choice question to the database

	// Constant values.
	include("constants.php");

	// Get the variables posted to the page.
	$surveyId = mysql_real_escape_string($_POST['sId']);
	$questionNumber = mysql_real_escape_string($_POST['quNo']);
	$questionType = mysql_real_escape_string($_POST['quType']);
	$questionText = mysql_real_escape_string($_POST['quText']);
	$numberOfAnswers = mysql_real_escape_string($_POST['ansCount']);

	// Loop through the answers and create an array of the values.
	$answers = array();
	for ($i = 1; $i <= $numberOfAnswers; $i++)
	{
		$answerNum = 'mcanswer'.$i;

		$answer = mysql_real_escape_string($_POST[$answerNum]);
		$answers[] = $answer;
	}

	// Add the multiple choice question to the database.
	$message = addMultipleChoice($surveyId, $questionNumber, $questionType, $questionText, $numberOfAnswers, $answers);

	// Return to the create survey page.
	echo "<p>$message</p>";


	//******************************************************
	//	Add the Multiple Choice question to the database
	//******************************************************
	function addMultipleChoice($surveyId, $questionNumber, $questionType, $questionText, $numberOfAnswers, $answers)
	{
		$questionAdded = true;
		$message = '';

		// Connect to the database.
		include("connectToDB.php");

		// Start a transaction.
		$conn->autocommit(false);

		// Insert the multiple choice question to the database.
		$questionQuery = "INSERT INTO questions(	surveyId,
													questionNumber,
													questionText,
													questionType,
													datecreated,
													lastmodified)
										VALUES (	$surveyId,
													$questionNumber,
													'$questionText',
													'".MULTIPLE_CHOICE."',
													now(),
													now());";

		$qResult = $conn->query($questionQuery);

		if (!$qResult)
		{
			$questionAdded = false;
			$message = "Q: "."ErrNo = ".$conn->errno." Error = ".$conn->error." Query = ".$questionQuery;
		}

		// Loop through the answers entered and insert them to the database.
		if ($numberOfAnswers > 0)
		{
			for ($i = 0; $i < $numberOfAnswers; $i++)
			{
				$answer = $answers[$i];

				$answerQuery = "INSERT INTO answers(	surveyId,
														questionNumber,
														answerNumber,
														answerText,
														datecreated,
														lastmodified)
											VALUES(		$surveyId,
														$questionNumber,
														$i,
														'$answer',
														now(),
														now());";

				$aResult = $conn->query($answerQuery);

				if (!$aResult)
				{
					$questionAdded = false;
					$message = $message." A1: "."Errno = ".$conn->errno." Error = ".$conn->error." Query = ".$answerQuery;
				}
			}
		}

		// Commit the transaction.
		$conn->commit();

		// Close the connetion.
		$conn->close();

		// If everything worked - return a successful messgae.
		if ($questionAdded)
		{
			$message = SUCCESS;
		}

		return $message;
	}
?>
