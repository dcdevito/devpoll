<?php
	// Add the rating question to the database

	// Get the variables posted to the page.
	$surveyId = mysql_real_escape_string($_POST['sId']);
	$questionNumber = mysql_real_escape_string($_POST['quNo']);
	$questionType = mysql_real_escape_string($_POST['quType']);
	$questionText = mysql_real_escape_string($_POST['quText']);
	$numberOfDescriptions = mysql_real_escape_string($_POST['descCount']);


	$lowValue = 1;
	$highValue= $ratingValue;

	// Rating descriptions.
	$ratingdescription1 = mysql_real_escape_string($_POST['radesc1']);
	$ratingdescription2 = mysql_real_escape_string($_POST['radesc2']);
	$ratingdescription3 = mysql_real_escape_string($_POST['radesc3']);
	$ratingdescription4 = mysql_real_escape_string($_POST['radesc4']);
	$ratingdescription5 = mysql_real_escape_string($_POST['radesc5']);
	$ratingdescription6 = mysql_real_escape_string($_POST['radesc6']);
	$ratingdescription7 = mysql_real_escape_string($_POST['radesc7']);
	$ratingdescription8 = mysql_real_escape_string($_POST['radesc8']);
	$ratingdescription9 = mysql_real_escape_string($_POST['radesc9']);
	$ratingdescription10 = mysql_real_escape_string($_POST['radesc10']);

	// Add a rating question to the database.
	$message = $addRating(	$surveyId, 
							$questionNumber, 
							$questionType, 
							$questionText, 
							$lowValue, 
							$highValue, 
							$ratingdescription1,
							$ratingdescription2,
							$ratingdescription3,
							$ratingdescription4,
							$ratingdescription5,
							$ratingdescription6,
							$ratingdescription7,
							$ratingdescription8,
							$ratingdescription9,
							$ratingdescription10
						);

	// Return to the create survey page.
	echo "<p>$message</p>";

	//**********************************************
	//	Add the rating question to the database
	//**********************************************
	function addRating(	$surveyId, 
						$questionNumber, 
						$questionType, 
						$questionText, 
						$lowValue, 
						$highValue, 
						$ratingdescription1,
						$ratingdescription2,
						$ratingdescription3,
						$ratingdescription4,
						$ratingdescription5,
						$ratingdescription6,
						$ratingdescription7,
						$ratingdescription8,
						$ratingdescription9,
						$ratingdescription10)
	{
		$questionAdded = true;
		$message = '';

		// Connect to the database.
		include("connectToDB.php");

		// Start a transaction.
		$conn->autocommit(false);

		// Insert the rating question to the database.
		$query = "INSERT INTO questions(	surveyId, 
											questionNumber, 
											questionText, 
											questionType, 
											datecreated,
											lastmodified) 
								VALUES (	$surveyId, 
											$questionNumber, 
											'$questionText', 
											'rating', 
											now(),
											now());";

		$result = $conn->query($query);

		if (!$result)
		{
			$questionAdded = false;
			$message = "Q: "."ErrNo = ".$conn->errno." Error = ".$conn->error." Query = ".$query;
		}		

		// Insert the rating descriptions to the database.
		$aQuery = "INSERT INTO answers(	surveyId, 
										questionNumber, 
										answerNumber, 
										lowValue, 
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
										$lowValue, 
										$highValue, 
										'$ratingdescription1', 
										'$ratingdescription2', 
										'$ratingdescription3', 
										'$ratingdescription4', 
										'$ratingdescription5', 
										'$ratingdescription6', 
										'$ratingdescription7', 
										'$ratingdescription8', 
										'$ratingdescription9', 
										'$ratingdescription10',
										now(),
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