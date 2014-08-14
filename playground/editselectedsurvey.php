<?php
	// Make sure the person is logged in.
	include("verifylogin.php");
?>

<html>
	<head>
		<script>
			function includeQuestions()
			{
				// Launch the Include Questions page.
				window.location = "includequestions.php";
			}
		</script>
	</head>
	<body>
<?php
		//if(!empty($_POST['editSurveyQuestion']))
		//{	
			$surveyId = $_POST['editsurvey'];
		//}
?>

<?php
	/*
		Load all of the questions from the database into a grid (i.e. a table).
		Next to each row there will be an edit and delete button.
		The edit button will allow the person to change the question and the answers.
		The delete button will remove the question from the survey.
	*/
	session_start();
	$_SESSION['surveyId'] = $surveyId;

	$result = getDistrictQuestionsAndAnsers(1, $surveyId);

	displayEditQuestionsAndAnswers($result, $surveyId);


	function getDistrictQuestionsAndAnsers($districtId, $surveyId)
	{
		// Connect to the database.
		require("connectToDB.php");

		$questionQuery = "
						SELECT
						q.questionid,
						s.surveyname,
						q.questionnumber,
						q.questiontext,
						q.questiontype,
						a.answernumber,
						a.answertext,
						a.lowvalue,
						a.highvalue,
						a.lowdescription,
						a.highdescription
						FROM devpoll.questions q
						JOIN devpoll.answers a
						ON q.surveyid = a.surveyid
						AND q.questionnumber = a.questionnumber
						JOIN devpoll.survey s
						ON q.surveyid = s.surveyid
						WHERE s.districtId = $districtId
						AND s.surveyid = $surveyId;
		";

		// Get the questions and answers for this survey.
		$result = $conn->query($questionQuery);

		// Close the connection.
		$conn->close();		

		return $result;
	}


	function displayEditQuestionsAndAnswers($result, $surveyId)
	{
		echo "<form action='editselectedquestion.php' method='POST'>";
		echo "<table border='1' cellspacing='0' cellpadding='0' width='80%' style='border-color:LightGrey'>";
		echo "<tr>";
		echo "<th>select</th>";
		echo "<th>Survey Name</th>";
		echo "<th>Question Number</th>";
		echo "<th>Question</th>";
		echo "<th>Question Type</th>";
		echo "<th colspan='5'>Answers</th>";
		echo "</tr>";

		// Initialize the value of $questionNumber.
		$questionNumber = -1;
		$loop = 0;
		$backcolorflag = 0;
		$backcolor = "WhiteSmoke";
		$answercount = 0;

		while($row = $result->fetch_assoc()) 
		{
			$surveyName = $row['surveyname'];
			$questionText = $row['questiontext'];
			$questionType = $row['questiontype'];
			$answerNumber = $row['answernumber'] + 1;
			$answerText = $row['answertext'];

			$qn = $row['questionnumber'];
			
			if ($questionNumber != $qn)
			{
				if ($backcolorflag == 0)
				{
					$backcolor = "white";
					$backcolorflag = 1;
				}
				else
				{
					$backcolor = "WhiteSmoke";
					$backcolorflag = 0;
				}	

				if ($answercount > 0 && $answercount < 5)
				{
					$colspanAmount = 5 - $answercount;

					echo "<td colspan='$colspanAmount'>$nbsp</td>";
					echo "</tr>";
				}
				$answercount = 0;

				$questionNumber = $qn;
				$questionid = $row['questionid'];

				echo "<tr bgcolor='$backcolor'>";
				echo "<td>
						<input type='radio' name='editQuestion' value='$questionid'>
						<input type='hidden' name='surveyId' value='$surveyId'>
						</td>";
				echo "<td>$surveyName</td>";
				echo "<td>$questionNumber</td>";
				echo "<td>$questionText</td>";

				switch($questionType)
				{
					case "freeForm":
						echo "<td>Free Form Text</td>";
						echo "<td colspan='5'>&nbsp;</td>";
						echo "</tr>";
						break;
					case "rating":
						echo "<td>Rating</td>";

						$lowDescription = $row['lowdescription'];
						$highDescription = $row['highdescription'];
						$lowValue = $row['lowvalue'];
						$highValue = $row['highvalue'];

						echo "<td colspan='2'>$lowDescription</td>";
						echo "<td colspan='2'>$highDescription</td>";
						echo "<td>&nbsp;</td>";
						echo "</tr>";
						echo "<tr bgcolor='$backcolor'>";
						echo "<td colspan='5'>&nbsp;</td>";
						echo "<td colspan='2'>$lowValue</td>";
						echo "<td colspan='2'>$highValue</td>";
						echo "<td>&nbsp;</td>";
						echo "</tr>";

						break;
					case "trueFalse":
						echo "<td>True False</td>";
						echo "<td colspan='2'>$answerText</td>";
						break;
					case "multipleChoice":
						$loop = 0;

						echo "<td>Multiple Choice</td>";
						echo "<td>$answerText</td>";
						break;
					case "severalAnswer":
						$loop = 0;

						echo "<td>Several Answer</td>";
						echo "<td>$answerText</td>";
						break;
				}
			}
			else
			{
				switch($questionType)
				{
					case "freeForm":
						$answercount = 5;
						break;
					case "rating":
						$answercount = 5;
						break;
					case "trueFalse":
						echo "<td colspan='3'>$answerText</td>";
						echo "</tr>";
						$answercount = 5;
						break;
					case "multipleChoice":
						if ($loop == 5)
						{
							echo "</tr>";
							echo "<tr bgcolor='$backcolor'>";
							echo "<td colspan='5'>&nbsp;</td>";

							$answercount = 0;
						}

						echo "<td>$answerText</td>";
						$answercount++;
						break;
					case "severalAnswer":
						if ($loop == 5)
						{
							echo "</tr>";
							echo "<tr bgcolor='$backcolor'>";
							echo "<td colspan='5'>&nbsp;</td>";

							$answercount = 0;
						}

						echo "<td>$answerText</td>";
						$answercount++;
						break;
				}			
			}

			$loop++;
		}

		echo "</table>";
		echo "<input type='submit' value='Edit Question'>";
		echo "<br/>";
		echo "<input type='button' value='Include Existing Questions' onclick='includeQuestions();'>";
		echo "</form>";
	}
?>
	</body>
</html>

