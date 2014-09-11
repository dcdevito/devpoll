<?php
	function getMaxRatingValue($surveyId)
	{
		echo "Beginning of getMaxRatingValue<br/>";

		// Connect to the database.
		require("connectToDB.php");

		$numberQuery = "
						SELECT
						max(a.highvalue) as maxhighvalue
						from devpoll.answers a
						where surveyid = $surveyId;
						";

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

	function getSurveyQuestionsAndAnsers($surveyId)
	{
		echo "Surveyid = $surveyId<br/>";

		// Connect to the database.
		require("connectToDB.php");

		$questionQuery = "
						SELECT
						s.surveyname,
						q.questionnumber,
						q.questiontext,
						q.questiontype,
						a.answernumber,
						a.answertext,
						a.lowvalue,
						a.highvalue,
						a.description1,
						a.description2,
						a.description3,
						a.description4,
						a.description5,
						a.description6,
						a.description7,
						a.description8,
						a.description9,
						a.description10
						FROM devpoll.questions q
						JOIN devpoll.answers a
						ON q.surveyid = a.surveyid
						AND q.questionnumber = a.questionnumber
						JOIN devpoll.survey s
						ON q.surveyid = s.surveyid
						WHERE q.surveyid = $surveyId;
		";

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

	function displaySurveyQuestions($result, $surveyName, $surveyId)
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

		$numrows = $result->num_rows;
		echo "We got $numrows rows in the result<br/>";

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
				$questionNumber = $qn;

				echo "<tr><td colspan='$maxhighvalue'>&nbsp;</td></tr>";
				echo "<tr><td colspan='$maxhighvalue'>Question $questionNumber</td></tr>";
				echo "<tr><td colspan='$maxhighvalue'>$questionText</td></tr>";

				switch($questionType)
				{
					case "freeForm":
						$cols = $maxhighvalue * 10;
						echo "<tr><td colspan='$maxhighvalue'><textarea name='freeText' rows='5' cols='$cols'></textarea></td></tr>";
						break;
					case "rating":
						$lowValue = $row['lowvalue'];
						$highValue = $row['highvalue'];
						$description = array(
										$row['description1'],
										$row['description2'],
										$row['description3'],
										$row['description4'],
										$row['description5'],
										$row['description6'],
										$row['description7'],
										$row['description8'],
										$row['description9'],
										$row['description10']
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
							$columnValue = $description[$i];
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
					case "trueFalse":
						echo "<tr><td colspan='$maxhighvalue'><input type='radio' name='tf' value='$answerText'>$answerText</td></tr>";
						break;
					case "multipleChoice":
						echo "<tr><td colspan='$maxhighvalue'><input type='radio' name='mc' value='$answerText'>$answerText</td></tr>";
						break;
					case "severalAnswer":
						echo "<tr><td colspan='$maxhighvalue'><input type='checkbox' name='sa' value='$answerText'>$answerText</td></tr>";
						break;
				}
			}
			else
			{
				switch($questionType)
				{
					case "freeForm":
						break;
					case "rating":
						break;
					case "trueFalse":
						echo "<tr><td colspan='$maxhighvalue'><input type='radio' name='tf' value='$answerText'>$answerText</td></tr>";
						break;
					case "multipleChoice":
						echo "<tr><td colspan='$maxhighvalue'><input type='radio' name='mc' value='$answerText'>$answerText</td></tr>";
						break;
					case "severalAnswer":
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
