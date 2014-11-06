<?php
	// Add the True/False question to the database

	// Get the variables posted to the page.
	$surveyId = mysql_real_escape_string($_POST['sId']);
	$questionNumber = mysql_real_escape_string($_POST['quNo']);
	$questionType = mysql_real_escape_string($_POST['quType']);
	$questionText = mysql_real_escape_string($_POST['quText']);
	$tfType = mysql_real_escape_string($_POST['tfType']);
	$tfCustom1 = mysql_real_escape_string($_POST['tfCustom1']);
	$tfCustom2 = mysql_real_escape_string($_POST['tfCustom2']);	

	// Get the type of headings for the true/false question. 
	switch ($tfType)
	{
		case "ab":
			$tfHeading1 = 'A';
			$tfHeading2 = 'B';
			break;
		case "yesno":
			$tfHeading1 = 'Yes';
			$tfHeading2 = 'No';
			break;
		case "custom":
			$tfHeading1 = mysql_real_escape_string($_POST['tfCustom1']);
			$tfHeading2 = mysql_real_escape_string($_POST['tfCustom2']);
			break;
		default:
			$tfHeading1 = 'True';
			$tfHeading2 = 'False';
			break;
	}

	// Add the true/false question to the database.
	$message = addtf($surveyId, $questionNumber, $questionType, $questionText, $tfHeading1, $tfHeading2);

	// Return to the create survey page.
	echo "<p>$message</p>";


	//******************************************************
	//	Add the True/False question to the database
	//******************************************************
	function addtf($surveyId, $questionNumber, $questionType, $questionText, $tfHeading1, $tfHeading2)
	{
		$questionAdded = true;
		$message = '';

		// Connect to the database.
		include("connectToDB.php");

		// Start a transaction.
		$conn->autocommit(false);

		// Insert the Question into the database.
		$query = "INSERT INTO questions(	surveyId, 
											questionNumber, 
											questionText, 
											questionType, 
											datecreated,
											lastmodified) 
								VALUES (	$surveyId, 
											$questionNumber, 
											'$questionText',
											'truefalse', 
											now(),
											now());";

		$result = $conn->query($query);

		if (!$result)
		{
			$questionAdded = false;
			$message = "Q: "."ErrNo = ".$conn->errno." Error = ".$conn->error." Query = ".$query;
		}

		// Insert the first Answer into the database. 
		$aQuery1 = "INSERT INTO answers(	surveyId, 
											questionNumber, 
											answerNumber, 
											answerText,
											datecreated,
											lastmodified) 
								VALUES(		$surveyId, 
											$questionNumber, 
											0, 
											'$tfHeading1',
											now(),
											now());";

		$aResult1 = $conn->query($aQuery1);

		if (!$aResult1)
		{
			$questionAdded = false;
			$message = $message." A1: "."Errno = ".$conn->errno." Error = ".$conn->error." Query = ".$aQuery1;
		}

		// Insert the second Answer into the database.
		$aQuery2 = "INSERT INTO answers(	surveyId, 
											questionNumber, 
											answerNumber, 
											answerText,
											datecreated,
											lastmodified) 
								VALUES(		$surveyId, 
											$questionNumber, 
											1, 
											'$tfHeading2',
											now(),
											now());";

		$aResult2 = $conn->query($aQuery2);

		if (!$aResult2)
		{
			$questionAdded = false;
			$message = $message." A2: "."Errno = ".$conn->errno." Error = ".$conn->error." Query = ".$aQuery2;
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


