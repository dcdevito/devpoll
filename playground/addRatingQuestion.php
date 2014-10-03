<?php
	/**********************************************
		Add the rating question to the database
	**********************************************/
?>

<?php
	// Start the session values.
	session_start();

	// Get the variables posted to the page.
	$questionNumber = mysql_real_escape_string($_POST['questionNumber']);
	$questionType = mysql_real_escape_string($_POST['createType']);
	$questionText = mysql_real_escape_string($_POST['questionText']);
	$surveyId = mysql_real_escape_string($_POST['surveyId']);
	$surveyName = mysql_real_escape_string($_POST['surveyName']);
	$everyQuestion = mysql_real_escape_string($_POST['everyQuestion']);

	$value = mysql_real_escape_string($_POST['numberOfDescriptions']);
	$ratingValue = mysql_real_escape_string($_POST['ratingValue']);

	$lowValue = 1;
	$highValue= $ratingValue;

	// Rating descriptions.
	$description1 = mysql_real_escape_string($_POST['ratingdescription1']);
	$description2 = mysql_real_escape_string($_POST['ratingdescription2']);
	$description3 = mysql_real_escape_string($_POST['ratingdescription3']);
	$description4 = mysql_real_escape_string($_POST['ratingdescription4']);
	$description5 = mysql_real_escape_string($_POST['ratingdescription5']);
	$description6 = mysql_real_escape_string($_POST['ratingdescription6']);
	$description7 = mysql_real_escape_string($_POST['ratingdescription7']);
	$description8 = mysql_real_escape_string($_POST['ratingdescription8']);
	$description9 = mysql_real_escape_string($_POST['ratingdescription9']);
	$description10 = mysql_real_escape_string($_POST['ratingdescription10']);

	// Add a rating question to the database.
	addRating(	$surveyId, 
				$questionNumber, 
				$questionType, 
				$questionText, 
				$lowValue, 
				$highValue, 
				$description1,
				$description2,
				$description3,
				$description4,
				$description5,
				$description6,
				$description7,
				$description8,
				$description9,
				$description10
			);

	// Set the session values.
	$_SESSION['surveyInProgress'] = 'YES';
	$_SESSION['surveyId'] = $surveyId;
	$_SESSION['surveyName'] = $surveyName;
	$_SESSION['questionNumber'] = $questionNumber;
	$_SESSION['everyQuestion'] = $everyQuestion;

	// Return to the create survey page.
	header('Location: createsurvey.php');

	/**********************************************
		Add the rating question to the database
	**********************************************/
	function addRating(	$surveyId, 
						$questionNumber, 
						$questionType, 
						$questionText, 
						$lowValue, 
						$highValue, 
						$description1,
						$description2,
						$description3,
						$description4,
						$description5,
						$description6,
						$description7,
						$description8,
						$description9,
						$description10 
					)
	{
		// Connect to the database.
		include("connectToDB.php");

		// Start a transaction.
		$conn->autocommit(false);

		// Insert the rating question to the database.
		$conn->query("INSERT INTO questions(surveyId, questionNumber, questionText, questionType, lastmodified) 
							VALUES ($surveyId, $questionNumber, '$questionText', 'rating', now());");

		// Insert the rating descriptions to the database.
		$conn->query("INSERT INTO 
								answers
								(
									surveyId, 
									questionNumber, 
									answerNumber, 
									lowValue, 
									highvalue, 
									description1, 
									description2,
									description3,
									description4,
									description5,
									description6,
									description7,
									description8,
									description9,
									description10
								) 
							VALUES
								(	
									$surveyId, 
									$questionNumber, 
									0, 
									$lowValue, 
									$highValue, 
									'$description1', 
									'$description2', 
									'$description3', 
									'$description4', 
									'$description5', 
									'$description6', 
									'$description7', 
									'$description8', 
									'$description9', 
									'$description10' 
								);
				");

		// Commit the transaction.
		$conn->commit();		

		if ($conn->errno != 0)
		{
			echo "error ".$conn->error."<br/>";
		}

		// Close the connetion.
		$conn->close();
	}
?>