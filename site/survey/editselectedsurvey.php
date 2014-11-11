<?php
	// Display the answers for the selected survey, so they can be edited

	// Make sure the person is logged in.
	include("verifylogin.php");

	// Constant values.
	include("constants.php");
?>

<html>
	<head>
		<script>
			//**************************************
			//	Call the include questions page
			//**************************************
			function includeQuestions(surveyId)
			{
				// Launch the Include Questions page.
				window.location = "includequestions.php?si=" + surveyId + "&rp=89267";
			}

			//**********************************
			//	Call the create survey page 
			//**********************************
			function addQuestions(surveyId)
			{
				window.location = "createsurvey.php?si=" + surveyId;
			}
		</script>
	</head>
	<body>
<?php
		//if(!empty($_POST['editSurveyQuestion']))
		//{	
			$surveyId = $_POST['editsurvey'];

			echo "SurveyId = $surveyId<br/>";
		//}
?>

<?php
	//
	//	Load all of the questions from the database into a grid (i.e. a table).
	//	Next to each row there will be an edit and delete button.
	//	The edit button will allow the person to change the question and the answers.
	//	The delete button will remove the question from the survey.
	//
	$districtid = 1;

	// Get the questions and answers for the given district id and survey id.
	$result = getDistrictQuestionsAndAnsers($districtid, $surveyId);

	// Display the questions and answers in a table.
	displayEditQuestionsAndAnswers($result, $surveyId);

	//************************************************************
	//	Get the questions and answers for the given survey id
	//************************************************************
	function getDistrictQuestionsAndAnsers($districtId, $surveyId)
	{
		// Connect to the database.
		require("connectToDB.php");

		$questionQuery = "SELECT q.questionid,
								 a.answerid,
								 s.surveyname,
								 q.questionnumber,
								 q.questiontext,
								 q.questiontype,
								 a.answernumber,
								 a.answertext,
								 a.lowvalue,
								 a.highvalue,
								 a.ratingdescription1,
								 a.ratingdescription2,
								 a.ratingdescription3,
								 a.ratingdescription4,
								 a.ratingdescription5,
								 a.ratingdescription6,
								 a.ratingdescription7,
								 a.ratingdescription8,
								 a.ratingdescription9,
 								 a.ratingdescription10
						FROM  devpoll.questions q
						JOIN  devpoll.answers a
						ON    q.surveyid = a.surveyid
						AND   q.questionnumber = a.questionnumber
						JOIN  devpoll.survey s
						ON    q.surveyid = s.surveyid
						WHERE s.districtId = $districtId
						AND   s.surveyid = $surveyId;";

		// Get the questions and answers for this survey.
		$result = $conn->query($questionQuery);

		// Close the connection.
		$conn->close();		

		return $result;
	}


	//***************************************************
	//	Display the questions and answers in a table
	//***************************************************
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
		echo "<th colspan='".MAX_COLSPAN."'>Answers</th>";
		echo "</tr>";

		// Initialize the value of $questionNumber.
		$questionNumber = -1;
		$loop = 0;
		$backcolorflag = LIGHT;
		$backcolor = LIGHT_COLOR;
		$answercount = 0;

		if($result->num_rows > 0) 
		{
			// Loop through the results.
			while($row = $result->fetch_assoc()) 
			{
				// Take the fields from the query.
				$surveyName = $row['surveyname'];
				$questionText = $row['questiontext'];
				$questionType = $row['questiontype'];
				$answerNumber = $row['answernumber'] + 1;
				$answerText = $row['answertext'];
				$answerId = $row['answerid'];

				$qn = $row['questionnumber'];
				
				// Because each answer is on a different row in the query results,
				// we keep looping until the question number changes, which means it's a different question.
				if ($questionNumber != $qn)
				{
					// This is the start of a new question.

					// Alternate the row color.
					if ($backcolorflag == LIGHT)
					{
						$backcolor = LIGHT_COLOR;
						$backcolorflag = DARK;
					}
					else
					{
						$backcolor = DARK_COLOR;
						$backcolorflag = LIGHT;
					}	

					// We only want to display a certain number of answers in one row.
					if ($answercount > 0 && $answercount < MAX_COLSPAN)
					{
						$colspanAmount = MAX_COLSPAN - $answercount;

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

					// Display different information based on the question type.
					switch($questionType)
					{
						case FREE_FORM:
							echo "<td>Free Form Text</td>";
							echo "<td colspan='".MAX_COLSPAN."'>&nbsp;</td>";
							echo "</tr>";
							break;
						case RATING:
							echo "<td>Rating</td>";

							$lowDescription = $row['ratingdescription1'];
							$highDescription = $row['ratingdescription10'];
							$lowValue = $row['lowvalue'];
							$highValue = $row['highvalue'];

							echo "<td colspan='2'>$lowDescription</td>";
							echo "<td colspan='2'>$highDescription</td>";
							echo "<td>&nbsp;</td>";
							echo "</tr>";
							echo "<tr bgcolor='$backcolor'>";
							echo "<td colspan='".MAX_COLSPAN."'>&nbsp;</td>";
							echo "<td colspan='2'>$lowValue</td>";
							echo "<td colspan='2'>$highValue</td>";
							echo "<td>&nbsp;</td>";
							echo "</tr>";

							break;
						case TRUE_FALSE:
							echo "<td>True False</td>";
							echo "<td colspan='2'>$answerText</td>";
							break;
						case MULTIPLE_CHOICE:
							$loop = 0;

							echo "<td>Multiple Choice</td>";
							echo "<td>$answerText</td>";
							break;
						case SEVERAL_ANSWER:
							$loop = 0;

							echo "<td>Several Answer</td>";
							echo "<td>$answerText</td>";
							break;
					}
				}
				else
				{
					// We are continuing different answers from the same question.

					// Display different information based on the question type.
					switch($questionType)
					{
						case FREE_FORM:
							$answercount = MAX_COLSPAN;
							break;
						case RATING:
							$answercount = MAX_COLSPAN;
							break;
						case TRUE_FALSE:
							echo "<td colspan='3'>$answerText</td>";
							echo "</tr>";
							$answercount = MAX_COLSPAN;
							break;
						case MULTIPLE_CHOICE:
							if ($loop == MAX_COLSPAN)
							{
								echo "</tr>";
								echo "<tr bgcolor='$backcolor'>";
								echo "<td colspan='".MAX_COLSPAN."'>&nbsp;</td>";

								$answercount = 0;
							}

							echo "<td>$answerText</td>";
							$answercount++;
							break;
						case SEVERAL_ANSWER:
							if ($loop == MAX_COLSPAN)
							{
								echo "</tr>";
								echo "<tr bgcolor='$backcolor'>";
								echo "<td colspan='".MAX_COLSPAN."'>&nbsp;</td>";

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
		}
		
		echo "<input type='button' value='Include Existing Questions' onclick='includeQuestions($surveyId);'>";
		echo "<br/>";
		echo "<input type='button' value='Add Additional Questions' onclick='addQuestions($surveyId);'>";
		echo "</form>";
	}
?>
	</body>
</html>

