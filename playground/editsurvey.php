<?php
	// Make sure the person is logged in.
	include("verifylogin.php");
?>

<?php
	/*
		Load all of the questions from the database into a grid (i.e. a table).
		Next to each row there will be an edit and delete button.
		The edit button will allow the person to change the question and the answers.
		The delete button will remove the question from the survey.
	*/
	echo "We are in editSurvey<br/>";
	$result = getSurveys(1);

	echo "The number of rows we got is ".$result->num_rows;

	echo "About to call displayEditQuestionsAndAnswers<br/>";

	displaySurveys($result);

	echo "Called it<br/>";

	function getSurveys($districtId)
	{
		// Connect to the database.
		require("connectToDB.php");

		echo "In display questions and districtId = $districtId<br/>";
		$questionQuery = "
			SELECT
				s.surveyid,
				s.surveyname,
				s.districtid,
				coalesce(q.numberofquestions, 0) as numberofquestions,
				s.dateopen
			from devpoll.survey s
			left join (
				select
					surveyid,
					max(questionnumber) as numberofquestions
				from devpoll.questions
				group by surveyid
				) as q
			on
			s.surveyid = q.surveyid
			where s.dateclosed is null
			and s.districtid = $districtId;		
		";

		// Get the questions and answers for this survey.
		$result = $conn->query($questionQuery);

		// Close the connection.
		$conn->close();		

		return $result;
	}


	function displaySurveys($result)
	{
		echo "<form action='editselectedsurvey.php' method='POST'>";
		echo "<table border='1' cellspacing='0' cellpadding='0' width='80%' style='border-color:LightGrey'>";
		echo "<tr>";
		echo "<th>select</th>";
		echo "<th>Survey Name</th>";
		echo "<th>Number of Questions</th>";
		echo "<th>Date Open</th>";
		echo "</tr>";

		// Initialize the value of $questionNumber.
		$loop = 0;
		$backcolorflag = 0;
		$backcolor = "lightblue";
		$answercount = 0;

		while($row = $result->fetch_assoc()) 
		{
			$surveyId = $row['surveyid'];
			$surveyName = $row['surveyname'];
			$numberOfQuestions = $row['numberofquestions'];
			$dateOpen = $row['dateopen'];

			if ($backcolorflag == 0)
			{
				$backcolor = "white";
				$backcolorflag = 1;
			}
			else
			{
				$backcolor = "lightblue";
				$backcolorflag = 0;
			}	

			echo "<tr bgcolor='$backcolor'>";
			echo "<td>
					<input type='radio' name='editsurvey' value='$surveyId'>
				</td>";
			echo "<td>$surveyName</td>";
			echo "<td>$numberOfQuestions</td>";
			echo "<td>$dateOpen</td>";
			echo "</tr>";
		}

		echo "</table>";
		echo "<input type='submit' value='Edit'>";
		echo "</form>";
	}
?>
