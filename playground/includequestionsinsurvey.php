<?php
	// Make sure the person is logged in.
	include("verifylogin.php");
?>

<?php
	try
	{
		$surveyId = $_POST['surveyId'];
		$returnPage = $_POST['returnPage'];

		if(!empty($_POST['includeSurveyQuestion']))
		{		
			// Get the maximum question number for this survey.
			$questionNumber = getMaxQuestionNumber($surveyId);

			foreach($_POST['includeSurveyQuestion'] as $questionId)
			{
				$questionResult = getQuestionForId($questionId);
				$answerResult = getAnswersForId($questionId);

				insertQuestionToSurvey($surveyId, ++$questionNumber, $questionResult, $answerResult);
			}
		}

		// The value for this is set in editselectedsurvey.php
		if ($returnPage == 89267)
		{
			$location = "editsurvey.php";
		}
		else
		{
			$location = "createsurvey.php";
		}

		redirect($location);
	}
	catch(Exception $e)
	{
		echo "Error in includequestionsinsurvey ", $e->getMessage(), "<br/>";
	}

	// Redirect page to a different url.
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

	function getMaxQuestionNumber($surveyId)
	{
		// Connect to the database.
		require("connectToDB.php");

		$numberQuery = "
						SELECT max(questionnumber) as questionnumber
						FROM devpoll.questions
						WHERE surveyid = $surveyId;
						";

		// Get the questions and answers for this survey.
		$maxRS = $conn->query($numberQuery);

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

	function getQuestionForId($questionId)
	{
		// Connect to the database.
		require("connectToDB.php");

		$questionQuery = "
						SELECT
						q.surveyid,
						q.questionid,
						q.questionnumber,
						q.questiontext,
						q.questiontype,
						q.datecreated
						FROM devpoll.questions q
						WHERE q.questionid = $questionId;
		";

		// Get the questions and answers for this survey.
		$result = $conn->query($questionQuery);

		// Close the connection.
		$conn->close();

		// Return the result.
		return $result;		
	}

	function getAnswersForId($questionId)
	{
		// Connect to the database.
		require("connectToDB.php");

		$questionQuery = "
						SELECT
						a.surveyid,
						a.answernumber,
						a.answertext,
						coalesce(a.lowvalue, 0) as lowvalue,
						coalesce(a.highvalue, 0) as highvalue,
						a.lowdescription,
						a.highdescription,
						a.datecreated
						FROM devpoll.questions q
						JOIN devpoll.answers a
						ON q.surveyid = a.surveyid
						AND q.questionnumber = a.questionnumber
						WHERE q.questionid = $questionId;
		";

		// Get the questions and answers for this survey.
		$result = $conn->query($questionQuery);

		// Close the connection.
		$conn->close();

		// Return the result.
		return $result;		
	}

	function insertQuestionToSurvey($surveyId, $questionNumber, $questionResult, $answerResult)
	{
		// Connect to the database.
		require("connectToDB.php");

		// Start the Transaction.
    	$conn->autocommit(FALSE);

		try 
		{
	    	while ($row = $questionResult->fetch_assoc())
	    	{
	    		$questionText = $row['questiontext'];
	    		$questionType = $row['questiontype'];
	    		$dateCreated = $row['datecreated'];

	    		$sql = "INSERT INTO devpoll.questions(surveyid, questionnumber, questiontext, questiontype, datecreated, lastmodified) 
	    				VALUES($surveyId, $questionNumber, '$questionText', '$questionType', '$dateCreated', 'now()');";

	    		$conn->query($sql);
	    	}

	    	while ($row = $answerResult->fetch_assoc())
	    	{
	    		$answerNumber = $row['answernumber'];
	    		$answerText = $row['answertext'];
	    		$lowValue = $row['lowvalue'];
	    		$highValue = $row['highvalue'];
	    		$lowDescription = $row['lowdescription'];
	    		$highDescription = $row['highdescription'];
	    		$dateCreated = $row['datecreated'];

	    		$sql = "INSERT INTO devpoll.answers(surveyid, questionnumber, answernumber, answertext, 
	    											lowvalue, highvalue, lowdescription, highdescription, datecreated) 
	    				VALUES($surveyId, $questionNumber, $answerNumber, '$answerText', 
	    						$lowValue, $highValue, '$lowDescription', '$highDescription', '$dateCreated');";

	    		$conn->query($sql);
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
