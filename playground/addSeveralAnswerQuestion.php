<?php
	// Add the several answer question to the database

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
		$answerNum = 'saanswer'.$i;

		$answer = mysql_real_escape_string($_POST[$answerNum]);
		$answers[] = $answer;
	}

	// Add the several answer question to the database.
	$messgae = addSeveralAnswer($surveyId, $questionNumber, $questionType, $questionText, $numberOfAnswers, $answers);

	// Return to the create survey page.
	echo "<p>$message</p>";


	//*****************************************************
	//	Add the several answer question to the database
	//*****************************************************
	function addSeveralAnswer($surveyId, $questionNumber, $questionType, $questionText, $numberOfAnswers, $answers)
	{
		$questionAdded = true;
		$message = '';

		// Connect to the database.
		include("connectToDB.php");

		// Start a transaction.
		$conn->autocommit(false);

		// Insert the several answer question to the database.
		$questionQuery = "INSERT INTO questions(	surveyId, 
													questionNumber, 
													questionText, 
													questionType, 
													datecreated,
													lastmodified) 
										VALUES (	$surveyId, 
													$questionNumber, 
													'$questionText', 
													'severalAnswer', 
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
			$message = "Success";
		}

		return $message;
	}
?>