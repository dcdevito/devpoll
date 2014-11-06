<?php
	// Add the free form question to the database

	// Get the variables posted to the page.
	$surveyId = mysql_real_escape_string($_POST['sId']);
	$questionNumber = mysql_real_escape_string($_POST['quNo']);
	$questionType = mysql_real_escape_string($_POST['quType']);
	$questionText = mysql_real_escape_string($_POST['quText']);

	// Add the free form question to the database
	$message = addFreeForm($surveyId, $questionNumber, $questionType, $questionText);

	// Return to the create survey page.
	echo "<p>$message</p>";
	

	//************************************************
	//	Add the free form question to the database
	//************************************************
	function addFreeForm($surveyId, $questionNumber, $questionType, $questionText)
	{
		$questionAdded = true;
		$message = '';

		// Connect to the database.
		include("connectToDB.php");

		// Start a transaction.
		$conn->autocommit(false);

		// Inset the free form question to the database.
		$query = "INSERT INTO questions(	surveyId, 
											questionNumber, 
											questionText, 
											questionType, 
											datecreated,
											lastmodified) 
								VALUES (	$surveyId, 
											$questionNumber, 
											'$questionText', 
											'freeForm', 
											now(),
											now());";

		$result = $conn->query($query);

		if (!$result)
		{
			$questionAdded = false;
			$message = "Q: "."ErrNo = ".$conn->errno." Error = ".$conn->error." Query = ".$query;
		}		

		// Insert the free form answer (none in this case) to the database.
		$aQuery = "INSERT INTO answers(	surveyId, 
										questionNumber, 
										answerNumber, 
										answerText,
										datecreated) 
								VALUES(	$surveyId, 
										$questionNumber, 
										0, 
										'',
										now());";

		$aResult = $conn->query($aQuery);

		if (!$aResult)
		{
			$questionAdded = false;
			$message = $message." A2: "."Errno = ".$conn->errno." Error = ".$conn->error." Query = ".$aQuery;
		}

		// Commit the transaction.
		$conn->commit();		

		// Close the connetion.
		$conn->close();

		if ($questionAdded)
		{
			$message = "Success";
		}

		return $message;
	}
?>