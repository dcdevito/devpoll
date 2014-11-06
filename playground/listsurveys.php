<?php
	// Display a list of the surveys that this user can access

	// Make sure the person is logged in.
	include("verifylogin.php");
?>

<html>
<head>
	<script>
		// Go back to the welcome page.
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

	// The district Id will be read in for the user.
	// *** For testing we will assume DISTRICT ID = 1 ***
	$districtId = 1;

	// Get the surveys for the district.
	$result = getSurveys($districtId);

	// Display the surveys for the district.
	displaySurveys($result);

	//**************************************************
	//	Get all of the surveys for this District Id
	//**************************************************
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
						LEFT JOIN (	SELECT 	surveyid,
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

	//*************************************
	//	Display the surveys in a table
	//*************************************
	function displaySurveys($result)
	{
		echo "<form action='editselectedsurvey.php' method='POST'>";
		echo "<table border='1' cellspacing='0' cellpadding='0' width='80%' style='border-color:LightGrey'>";
		echo "<tr>";
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
			echo "<td>$surveyName</td>";
			echo "<td>$numberOfQuestions</td>";
			echo "<td>$dateOpen</td>";
			echo "</tr>";
		}

		echo "</table>";
		echo "<input type='button' value='Main Menu' onclick='goBack();'>";
		echo "</form>";
	}
?>
</body>
</html>
