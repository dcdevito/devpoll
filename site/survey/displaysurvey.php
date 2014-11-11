<?php
	// Draw the questions and answers on the screen for a given survey id

	// Make sure the person is logged in.
	include("verifylogin.php");

	// Constant values.
	include("constants.php");

	//**********************************************
	//	Draw the questions on the screen
	//**********************************************
	function drawQuestions($surveyId, $surveyName)
	{
		echo "Getting the questions and answers<br/>";
		// Get the questions and answers from the database.
		$result = getSurveyQuestionsAndAnsers($surveyId);

		echo "We got the questions and answers<br/>";
		// Draw the questions and answers in a table.
		displaySurveyQuestions($result, $surveyName, $surveyId);
	}

	//*************************************************************
	//	Return the questions and answers for a given survey id 
	//*************************************************************
	function getSurveyQuestionsAndAnsers($surveyId)
	{
		echo "Surveyid = $surveyId<br/>";

		// Connect to the database.
		require("connectToDB.php");

		$questionQuery = "SELECT s.surveyname,
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
						WHERE q.surveyid = $surveyId;";

		// Get the questions and answers for this survey.
		$result = $conn->query($questionQuery);
		
		echo "errors = ".$conn->errors;

		// Close the connection.
		$conn->close();

		echo "End of getSurveyQuestionsAndAnsers<br/>";
		echo "We have ".$result->num_rows;

		// Return the result.
		return $result;
	}

	//***********************************************************************
	//	Get the maximum rating value for any rating question in a survey,
	//	for formatting the answers across the page properly
	//***********************************************************************
	function getMaxRatingValue($surveyId)
	{
		echo "Beginning of getMaxRatingValue<br/>";

		// Connect to the database.
		require("connectToDB.php");

		$numberQuery = "SELECT 	max(a.highvalue) as maxhighvalue
						FROM  devpoll.answers a
						WHERE surveyid = $surveyId;";

		echo "Query = $numberQuery<br/>";

		// Get the questions and answers for this survey.
		$maxRS = $conn->query($numberQuery);

		echo "Errors = ".$conn->error;
		echo "<br/>";

		$maxArray = $maxRS->fetch_array(MYSQLI_ASSOC);
		$max = $maxArray['maxhighvalue'];

		// If there are no questions - set max question to zero.
		if ($max == null)
		{
			$max = 0;
		}

		echo "Max = $max<br/>";		

		// Close the connection.
		$conn->close();

		echo "The end of getMaxRatingValue max = $max<br/>";

		// Return the result.
		return $max;
	}	

	//***************************************************
	//	Display the questions and answers in a table
	//***************************************************
	function displaySurveyQuestions($questionsAndAnswers, $surveyName, $surveyId)
	{
		echo "Beginning of displaySurveyQuestions<br/>";

		$maxhighvalue = getMaxRatingValue($surveyId);
		if ($maxhighvalue == 0)
		{
			$maxhighvalue = 2;
		}

		echo "maxhighvalue = $maxhighvalue<br/>";

		echo "<html>
			<head>
			<style>
				#ratingList ul
				{
					margin: 0;
					padding: 0;
					list-style-type: none;
					width: 100%;
					text-align: center;
				}

				#ratingList ul li { display: inline; }

				#ratingList ul li
				{
					text-decoration: none;
					padding: .2em 1em;
				}			
			</style>
			</head>
			<body>";
		echo "<table border='1' style='border-color: lightgrey;' width='80%'>";
		echo "<tr><td colspan='$maxhighvalue'>Survey $surveyName</td></tr>";

		// Initialize the value of $questionNumber.
		$questionNumber = -1;

		$numrows = $questionsAndAnswers->num_rows;
		echo "We got $numrows rows in the result<br/>";

		while($row = $questionsAndAnswers->fetch_assoc()) 
		{
			$surveyName = $row['surveyname'];
			$questionText = $row['questiontext'];
			$questionType = $row['questiontype'];
			$answerNumber = $row['answernumber'] + 1;
			$answerText = $row['answertext'];

			$qn = $row['questionnumber'];

			// Becuase each answer is stored in a separate row of the answers table,
			// we loop through each question number to pick up the different answers stored.
			// But each answer for the same question will have the same question number, 
			// so when the question number changes, it means a new question. 
			if ($questionNumber != $qn)
			{
				$questionNumber = $qn;

				echo "<tr><td colspan='$maxhighvalue'>&nbsp;</td></tr>";
				echo "<tr><td colspan='$maxhighvalue'>Question $questionNumber</td></tr>";
				echo "<tr><td colspan='$maxhighvalue'>$questionText</td></tr>";

				switch($questionType)
				{
					case FREE_FORM:
						$cols = $maxhighvalue * 10;
						echo "<tr><td colspan='$maxhighvalue'><textarea name='freeText' rows='5' cols='$cols'></textarea></td></tr>";
						break;
					case RATING:
						$lowValue = $row['lowvalue'];
						$highValue = $row['highvalue'];
						$ratingdescription = array(
										$row['ratingdescription1'],
										$row['ratingdescription2'],
										$row['ratingdescription3'],
										$row['ratingdescription4'],
										$row['ratingdescription5'],
										$row['ratingdescription6'],
										$row['ratingdescription7'],
										$row['ratingdescription8'],
										$row['ratingdescription9'],
										$row['ratingdescription10']
									);

						/*****
						echo "<tr><td style='text-align:left; width:50%'>$lowDescription</td><td style='text-align:right; width:50%;'>$highDescription</td></tr>";

						echo "<tr><td colspan='2'>";

						echo "<div id='ratingList'>";
						echo "<ul>";
						for ($i = 1; $i <= $highValue; $i++)
						{
							echo "<li><input type='radio' name='rate' value='$i'>$i</li>";
						}	
						echo "</ul>";
						echo "</div>";

						echo "</td></tr>";
						*****/

						$columnWidth = 100 / $maxhighvalue;
						echo "columnWidth = $columnWidth<br/>";

						$columnValue = "";

						echo "<tr>";
						for ($i = 0; $i < $highValue; $i++)
						{
							$columnValue = $ratingdescription[$i];
							echo "<td width='$columnWidth%'>$columnValue</td>";
						}
						echo "</tr>";

						echo "<tr>";
						for ($i = 1; $i <= $highValue; $i++)
						{
							echo "<td width='$columnWidth%'><input type='radio' name='rate' value='$i'>$i</td>";
						}
						echo "</tr>";
						break;
					case TRUE_FALSE:
						echo "<tr><td colspan='$maxhighvalue'><input type='radio' name='tf' value='$answerText'>$answerText</td></tr>";
						break;
					case MULTIPLE_CHOICE:
						echo "<tr><td colspan='$maxhighvalue'><input type='radio' name='mc' value='$answerText'>$answerText</td></tr>";
						break;
					case SEVERAL_ANSWER:
						echo "<tr><td colspan='$maxhighvalue'><input type='checkbox' name='sa' value='$answerText'>$answerText</td></tr>";
						break;
				}
			}
			else
			{
				switch($questionType)
				{
					case FREE_FORM:
						break;
					case RATING:
						break;
					case TRUE_FALSE:
						echo "<tr><td colspan='$maxhighvalue'><input type='radio' name='tf' value='$answerText'>$answerText</td></tr>";
						break;
					case MULTIPLE_CHOICE:
						echo "<tr><td colspan='$maxhighvalue'><input type='radio' name='mc' value='$answerText'>$answerText</td></tr>";
						break;
					case SEVERAL_ANSWER:
						echo "<tr><td colspan='$maxhighvalue'><input type='checkbox' name='sa' value='$answerText'>$answerText</td></tr>";
						break;
				}			
			}
		}

		echo "</table>
			</body>
			</html>";

	}


?>
