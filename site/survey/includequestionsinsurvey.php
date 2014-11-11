<?php
	// Include the selected questions in the survey

	// Make sure the person is logged in.
	include("verifylogin.php");

	// Constant values.
	include("constants.php");

	try
	{
		// The variables past to the page.
		$surveyId = $_POST['surveyId'];
		$returnPage = $_POST['returnPage'];

		// If at least one question has been selected to be included in a survey. 
		if(!empty($_POST['includeSurveyQuestion']))
		{		
			// Get the maximum question number for this survey.
			$questionNumber = getMaxQuestionNumber($surveyId);

			// Loop through the selected questions.
			foreach($_POST['includeSurveyQuestion'] as $questionId)
			{
				// Get the questions and answers for this survey.
				$questionResult = getQuestionForId($questionId);
				$answerResult = getAnswersForId($questionId);

				// Include the selected question in the survey.
				insertQuestionToSurvey($surveyId, ++$questionNumber, $questionResult, $answerResult);
			}
		}

		// The value for this is set in editselectedsurvey.php
		if ($returnPage == EDIT_SURVEY)
		{
			$location = "editsurvey.php";
		}
		else
		{
			$location = "createsurvey.php";
		}

		// Return to the previous page.
		redirect($location);
	}
	catch(Exception $e)
	{
		echo "Error in includequestionsinsurvey ", $e->getMessage(), "<br/>";
	}

	//**************************************
	//	Redirect page to a different url
	//**************************************
	function redirect($url)
	{
	    if (!headers_sent())
	    {    
	        header('Location: '.$url);
	        exit;
		}
	    else
		{  
	        echo '<script type="text/javascript">';
	        echo 'window.location.href="'.$url.'";';
	        echo '</script>';
	        echo '<noscript>';
	        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
	        echo '</noscript>'; exit;
	    }
	}

	//*****************************************************
	//	Get the maximum question number for the survey, 
	//	so we just add the question on the end
	//*****************************************************
	function getMaxQuestionNumber($surveyId)
	{
		// Connect to the database.
		require("connectToDB.php");

		$numberQuery = "SELECT 	max(questionnumber) as questionnumber
						FROM  devpoll.questions
						WHERE surveyid = $surveyId;";

		// Get the questions and answers for this survey.
		$maxRS = $conn->query($numberQuery);

		// Load the array with the maximum question number.
		$maxArray = $maxRS->fetch_array(MYSQLI_ASSOC);
		$max = $maxArray['questionnumber'];

		// If there are no questions - set max question to zero.
		if ($max == null)
		{
			$max = 0;
		}

		// Close the connection.
		$conn->close();

		// Return the result.
		return $max;		

	}

	//*************************************************
	//	Get the question for the given question id
	//*************************************************
	function getQuestionForId($questionId)
	{
		// Connect to the database.
		require("connectToDB.php");

		$questionQuery = "SELECT q.surveyid,
								 q.questionid,
								 q.questionnumber,
								 q.questiontext,
								 q.questiontype,
								 q.datecreated
						FROM  devpoll.questions q
						WHERE q.questionid = $questionId;
		";

		// Get the questions and answers for this survey.
		$result = $conn->query($questionQuery);

		// Close the connection.
		$conn->close();

		// Return the result.
		return $result;		
	}

	//************************************************
	//	Get the answers for the given question id
	//************************************************
	function getAnswersForId($questionId)
	{
		// Connect to the database.
		require("connectToDB.php");

		$questionQuery = "SELECT a.surveyid,
 								 a.answernumber,
								 a.answertext,
								 coalesce(a.lowvalue, 0) as lowvalue,
								 coalesce(a.highvalue, 0) as highvalue,
								 a.ratingdescription1,
								 a.ratingdescription2,
								 a.ratingdescription3,
								 a.ratingdescription4,
								 a.ratingdescription5,
								 a.ratingdescription6,
								 a.ratingdescription7,
								 a.ratingdescription8,
								 a.ratingdescription9,
								 a.ratingdescription10,
								 a.datecreated
						FROM  devpoll.questions q
						JOIN  devpoll.answers a
						ON    q.surveyid = a.surveyid
						AND   q.questionnumber = a.questionnumber
						WHERE q.questionid = $questionId;";

		// Get the questions and answers for this survey.
		$result = $conn->query($questionQuery);

		// Close the connection.
		$conn->close();

		// Return the result.
		return $result;		
	}

	//***********************************************
	//	Insert the given question into the survey 
	//***********************************************
	function insertQuestionToSurvey($surveyId, $questionNumber, $questionResult, $answerResult)
	{
		// Connect to the database.
		require("connectToDB.php");

		// Start the Transaction.
    	$conn->autocommit(FALSE);

		try 
		{
			// Loop through the questions - Insert them into the database.
	    	while ($row = $questionResult->fetch_assoc())
	    	{
	    		// Get the fields from the results.
	    		$questionText = $row['questiontext'];
	    		$questionType = $row['questiontype'];
	    		$dateCreated = $row['datecreated'];

	    		$sql = "INSERT INTO devpoll.questions(	surveyid, 
	    												questionnumber, 
	    												questiontext, 
	    												questiontype, 
	    												datecreated, 
	    												lastmodified) 
	    									VALUES(		$surveyId, 
	    												$questionNumber, 
	    												'$questionText', 
	    												'$questionType', 
	    												'$dateCreated', 
	    												'now()');";

	    		// Execute the query.
	    		$conn->query($sql);
	    	}

	    	// Loop through the answers - Insert them into the database.
	    	while ($row = $answerResult->fetch_assoc())
	    	{
	    		// Get the fields from the results.
	    		$answerNumber = $row['answernumber'];
	    		$answerText = $row['answertext'];
	    		$lowValue = $row['lowvalue'];
	    		$highValue = $row['highvalue'];
	    		$ratingdescription1 = $row['ratingdescription1'];
	    		$ratingdescription2 = $row['ratingdescription2'];
	    		$ratingdescription3 = $row['ratingdescription3'];
	    		$ratingdescription4 = $row['ratingdescription4'];
	    		$ratingdescription5 = $row['ratingdescription5'];
	    		$ratingdescription6 = $row['ratingdescription6'];
	    		$ratingdescription7 = $row['ratingdescription7'];
	    		$ratingdescription8 = $row['ratingdescription8'];
	    		$ratingdescription9 = $row['ratingdescription9'];
	    		$ratingdescription10 = $row['ratingdescription10'];
	    		$dateCreated = $row['datecreated'];

	    		$sql = "INSERT INTO devpoll.answers(surveyid, 
	    											questionnumber, 
	    											answernumber, answertext, 
	    											lowvalue, 
	    											highvalue, 
	    											lowdescription, 
	    											highdescription, 
	    											datecreated,
	    											lastmodified) 
	    								VALUES(		$surveyId, 
	    											$questionNumber, 
	    											$answerNumber, 
	    											'$answerText', 
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
	    											'$dateCreated',
	    											now());";

				// Execute the query.
	    		$result = $conn->query($sql);
	    	}
	
			// Commit the SQL queries and go back to non-transaction mode.
    		$conn->commit();
		}
		catch ( Exception $e ) 
		{
    		// before rolling back the transaction, you'd want
    		// to make sure that the exception was db-related
    		$conn->rollback(); 
		}	

		// End the transaction.    	
    	$conn->autocommit(TRUE); // i.e., end transaction   

    	// Close the connection.
    	$conn->close();
    }
?>
