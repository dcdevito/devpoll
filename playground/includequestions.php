<?php
	// Display the list of existing questions to include in a survey

	// Make sure the person is logged in.
	include("verifylogin.php");

	// Constant values.
	include("constants.php");

	//
	//	Load all of the questions from the database into a grid (i.e. a table).
	//	Next to each row there will be an edit and delete button.
	//	The edit button will allow the person to change the question and the answers.
	//	The delete button will remove the question from the survey.
	//

	// Get the variables passed via a Get call to the page.
	//@@@@@@$surveyId = $_GET['si'];
	//@@@@@@$returnPage = $_GET['rp'];

	// The district Id will be read in for the user.
	// *** For testing we will assume DISTRICT ID = 1 ***
	$districtId = 1;

	// Get the question and answers for this survey Id.
	$result = getDistrictQuestionsAndAnsers($districtId, $surveyId);

	// Display the questions and answers for this survey Id.
	displayEditQuestionsAndAnswers($result, $surveyId, $returnPage);

	//************************************************************
	//	Retrieve the questions and answers for this survey Id
	//************************************************************
	function getDistrictQuestionsAndAnsers($districtId, $surveyId)
	{
		// Connect to the database.
		require("connectToDB.php");

		$questionQuery = "SELECT 	q.questionid,
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
						AND   s.surveyid <> $surveyId;";

		// Get the questions and answers for this survey.
		$result = $conn->query($questionQuery);

		// Close the connection.
		$conn->close();		

		return $result;
	}


	//*******************************************************************
	//	Display the questions and answers for this survey in a table
	//*******************************************************************
	function displayEditQuestionsAndAnswers($result, $surveyId, $returnPage)
	{
		echo "<form action='includequestionsinsurvey.php' method='POST'>";
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
		$backcolorflag = LIGHT;
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
				if ($backcolorflag == LIGHT)
				{
					$backcolor = "white";
					$backcolorflag = DARK;
				}
				else
				{
					$backcolor = "WhiteSmoke";
					$backcolorflag = LIGHT;
				}	

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
						<input type='checkbox' name='includeSurveyQuestion[]' value='$questionid'>
						<input type='hidden' name='qid' value='$questionid'>
						<input type='hidden' name='surveyId' value='$surveyId'>
						<input type='hidden' name='returnPage' value='$returnPage'>
						</td>";
				echo "<td>$surveyName</td>";
				echo "<td>$questionNumber</td>";
				echo "<td>$questionText</td>";

				switch($questionType)
				{
					case "freeForm":
						echo "<td>Free Form Text</td>";
						echo "<td colspan='".MAX_COLSPAN."'>&nbsp;</td>";
						echo "</tr>";
						break;
					case "rating":
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
						$answercount = MAX_COLSPAN;
						break;
					case "rating":
						$answercount = MAX_COLSPAN;
						break;
					case "trueFalse":
						echo "<td colspan='3'>$answerText</td>";
						echo "</tr>";
						$answercount = MAX_COLSPAN;
						break;
					case "multipleChoice":
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
					case "severalAnswer":
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
		echo "<input type='submit' value='Include Questions'>";
		echo "</form>";
	}
?>
