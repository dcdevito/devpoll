<?php
	// Make sure the person is logged in.
	include("verifylogin.php");
?>

<?php
	echo "Inside includequestionsinsurvey.php<br/>";

	try
	{
		$surveyId = $_POST['surveyId'];

		//echo "The surveyId is $surveyId<br/>";

		if(!empty($_POST['includeSurveyQuestion']))
		{		
			// Get the maximum question number for this survey.
			$questionNumber = getMaxQuestionNumber($surveyId);

			//echo "The max question was $questionNumber<br/>";

			foreach($_POST['includeSurveyQuestion'] as $questionId)
			{
				//echo "Question Id = $questionId<br/>";
				$questionResult = getQuestionForId($questionId);
				$answerResult = getAnswersForId($questionId);

				//echo "Number of questions = ".$questionResult->num_rows;
				//echo "Number of answers = ".$answerResult->num_rows;

				insertQuestionToSurvey($surveyId, ++$questionNumber, $questionResult, $answerResult);
			}
		}

		header('Location: createsurvey.php');	
	}
	catch(Exception $e)
	{
		echo "Error in includequestionsinsurvey ", $e->getMessage(), "<br/>";
	}

	function getMaxQuestionNumber($surveyId)
	{
		// Connect to the database.
		require("connectToDB.php");

		$numberQuery = "
						SELECT max(q.questionnumber) as questionnumber
						FROM devpoll.questions
						WHERE surveyid = $surveyId;
						";

		// Get the questions and answers for this survey.
		$max = $conn->query($questionQuery);

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

		//echo "In getQuestionForId = $questionId<br/>";
		
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

		//echo "Question query = $questionQuery<br/>";

		// Get the questions and answers for this survey.
		$result = $conn->query($questionQuery);

		//echo $conn->errno." ".$conn->error;

		// Close the connection.
		$conn->close();

		// Return the result.
		return $result;		
	}

	function getAnswersForId($questionId)
	{
		// Connect to the database.
		require("connectToDB.php");

		echo "In getAnswersForId = $questionId<br/>";
		
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
		echo "Question number = $questionNumber<br/>";

		// Connect to the database.
		require("connectToDB.php");

		// Start the Transaction.
    	$conn->autocommit(FALSE);

		try 
		{
	    	echo "We are inserting the question<br/>";

	    	while ($row = $questionResult->fetch_assoc())
	    	{
	    		$questionText = $row['questiontext'];
	    		$questionType = $row['questiontype'];
	    		$dateCreated = $row['datecreated'];

	    		$sql = "INSERT INTO devpoll.questions(surveyid, questionnumber, questiontext, questiontype, datecreated, lastmodified) 
	    				VALUES($surveyId, $questionNumber, '$questionText', '$questionType', '$dateCreated', 'now()');";

	    		echo "sql = $sql<br/>";

	    		$conn->query($sql);

	    		echo "Insert done: ".$conn->errno." ".$conn->error."<br/>";
	    	}

	    	echo "We are inserting the answer<br/>";

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

	    		echo "sql = $sql<br/>";

	    		$conn->query($sql);

	    		echo "Insert done: ".$conn->errno." ".$conn->error."<br/>";   		
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
