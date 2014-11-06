<?php
	// List the surveys so they can be edited

	// Make sure the person is logged in.
	include("verifylogin.php");

	// Constant values.
	include("constants.php");
?>

<html>
<head>
	<script>
		function goBack()
		{
			window.location = "welcome.php";
		}
	</script>
</head>
<body>
<?php
	//
	//	Load all of the questions from the database into a grid (i.e. a table).
	//	Next to each row there will be an edit and delete button.
	//	The edit button will allow the person to change the question and the answers.
	//	The delete button will remove the question from the survey.
	//
	$districtid = 1;

	// Get the surveys for the given district id.
	$result = getSurveys($districtid);

	// Display the surveys.
	displaySurveys($result);

	//*******************************************************
	//	Get all of the surveys for the given district id
	//*******************************************************
	function getSurveys($districtId)
	{
		// Connect to the database.
		require("connectToDB.php");

		$questionQuery = "SELECT 	s.surveyid,
									s.surveyname, 
									s.districtid,
									coalesce(q.numberofquestions, 0) as numberofquestions,
									s.dateopen
						FROM devpoll.survey s
						LEFT JOIN (SELECT 	surveyid,
											max(questionnumber) as numberofquestions
									FROM devpoll.questions
									GROUP BY surveyid) as q
						ON    s.surveyid = q.surveyid
						WHERE s.dateclosed is null
						AND   s.districtid = $districtId;";

		// Get the questions and answers for this survey.
		$result = $conn->query($questionQuery);

		// Close the connection.
		$conn->close();		

		return $result;
	}

	//********************************************
	//	Display the survey results in a table
	//********************************************
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
		$backcolorflag = LIGHT;
		$backcolor = "lightblue";
		$answercount = 0;

		// Loop through the results.
		while($row = $result->fetch_assoc()) 
		{
			// Get the fields from the results.
			$surveyId = $row['surveyid'];
			$surveyName = $row['surveyname'];
			$numberOfQuestions = $row['numberofquestions'];
			$dateOpen = $row['dateopen'];

			// Alternate the row colors.
			if ($backcolorflag == LIGHT)
			{
				$backcolor = "white";
				$backcolorflag = DARK;
			}
			else
			{
				$backcolor = "lightblue";
				$backcolorflag = LIGHT;
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
		echo "<br/>";
		echo "<input type='button' value='Main Menu' onclick='goBack();'>";
		echo "</form>";
	}
?>
</body>
</html>
