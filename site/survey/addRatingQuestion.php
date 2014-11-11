<?php
	// Add the rating question to the database

	// Constant values.
	include("constants.php");

	// Get the variables posted to the page.
	$surveyId = mysql_real_escape_string($_POST['sId']);
	$questionNumber = mysql_real_escape_string($_POST['quNo']);
	$questionType = mysql_real_escape_string($_POST['quType']);
	$questionText = mysql_real_escape_string($_POST['quText']);
	$numberOfDescriptions = mysql_real_escape_string($_POST['descCount']);

	$lowValue = 1;
	$highValue= $numberOfDescriptions;

	// Rating descriptions.
	// Loop through the descriptions and create an array of the values.
	$descs = array();

	for ($i = 1; $i <= $numberOfDescriptions; $i++)
	{
		$descNum = 'radesc'.$i;

		$desc = mysql_real_escape_string($_POST[$descNum]);
		$descs[] = $desc;
	}

	// Add a rating question to the database.
	$message = "a".addRatingQuestion($surveyId, $questionNumber, $questionType, $questionText, $lowValue, $highValue, $descs);

	// Return to the create survey page.
	echo "<p>$message</p>";

	//**********************************************
	//	Add the rating question to the database
	//**********************************************
	function addRatingQuestion(	$surveyId, $questionNumber, $questionType, $questionText, $lowValue, $highValue, $descs)
	{
		$questionAdded = true;
		$message = '';

		// Connect to the database.
		include("connectToDB.php");

		// Start a transaction.
		$conn->autocommit(false);

		// Insert the rating question to the database.
		$questionQuery = "INSERT INTO questions(surveyid,
																						questionnumber,
																						questiontext,
																						questiontype,
																						datecreated,
																						lastmodified)
										       				VALUES (	$surveyId,
																						$questionNumber,
																						'$questionText',
																						'".RATING."',
																						now(),
																						now());";

		$result = $conn->query($questionQuery);

		if (!$result)
		{
			$questionAdded = false;
			$message = "x $surveyId x $questionNumber x Q: "."ErrNo = ".$conn->errno." Error = ".$conn->error." Query = ".$questionQuery;
			return $message;
		}

		// Insert the rating descriptions to the database.
		$answerQuery = "INSERT INTO answers(	surveyid,
																					questionnumber,
																					answernumber,
																					answertext,
																					lowvalue,
																					highvalue,
																					ratingdescription1,
																					ratingdescription2,
																					ratingdescription3,
																					ratingdescription4,
																					ratingdescription5,
																					ratingdescription6,
																					ratingdescription7,
																					ratingdescription8,
																					ratingdescription9,
																					ratingdescription10,
																					datecreated,
																					lastmodified)
																VALUES (	$surveyId,
																					$questionNumber,
																					0,
																					'',
																					$lowValue,
																					$highValue,
																					'$descs[0]',
																					'$descs[1]',
																					'$descs[2]',
																					'$descs[3]',
																					'$descs[4]',
																					'$descs[5]',
																					'$descs[6]',
																					'$descs[7]',
																					'$descs[8]',
																					'$descs[9]',
																					now(),
																					now());";

		$aResult = $conn->query($answerQuery);

		if (!$aResult)
		{
			$questionAdded = false;
			$message = " A2: "."Errno = ".$conn->errno." Error = ".$conn->error." Query = ".$answerQuery;
			return $message;
		}

		// Commit the transaction.
		$conn->commit();

		// Close the connetion.
		$conn->close();

		if ($questionAdded)
		{
			$message = SUCCESS;
		}

		return $message;
	}
?>
