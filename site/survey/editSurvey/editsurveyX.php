<!doctype html>
<html>
<head>
    <title>SCOPE - Create Account</title>
  
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <script src="../components/platform/platform.js"></script>
    <link rel="import" href="../components/font-roboto/roboto.html">  
    <link rel="import" href="../components/core-header-panel/core-header-panel.html">
    <link rel="import" href="../components/core-toolbar/core-toolbar.html">
    <link rel="import" href="../components/paper-tabs/paper-tabs.html">
    <link rel="import" href="../components/core-icons/core-icons.html">
    <link rel="import" href="../components/paper-input/paper-input.html">
    <link rel="import" href="../components/paper-button/paper-button.html">
    <link rel="import" href="../components/paper-radio-button/paper-radio-button.html">
    <link rel="import" href="../components/paper-radio-group/paper-radio-group.html">
    <link rel="import" href="../components/paper-item/paper-item.html">
      
    <link rel="import" href="post-list.html">
<?php
	/*********************************************
		List the surveys so they can be edited
	*********************************************/
?>

<?php
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
	/*
		Load all of the questions from the database into a grid (i.e. a table).
		Next to each row there will be an edit and delete button.
		The edit button will allow the person to change the question and the answers.
		The delete button will remove the question from the survey.
	*/
	$districtid = 1;

	// Get the surveys for the given district id.
	$result = getSurveys($districtid);

	// Display the surveys.
	displaySurveys($result);

	/*******************************************************
		Get all of the surveys for the given district id
	*******************************************************/
	function getSurveys($districtId)
	{
		// Connect to the database.
		require("connectToDB.php");

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

	/********************************************
		Display the survey results in a table
	********************************************/
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
