<?php
	function getSurveyQuestionsAndAnsers($surveyId)
	{
		// Connect to the database.
		require("connectToDB.php");

		echo "In display questions and surveyId = $surveyId<br/>";
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
						a.lowdescription,
						a.highdescription
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

		return $result;
	}

	function displaySurveyQuestions($result, $surveyName)
	{
		echo "<table border='2'>";
		echo "<tr><td colspan='2'>Survey $surveyName</td></tr>";

		// Initialize the value of $questionNumber.
		$questionNumber = -1;

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

				echo "<tr><td colspan='2'>&nbsp;</td></tr>";
				echo "<tr><td colspan='2'>Question $questionNumber</td></tr>";
				echo "<tr><td colspan='2'>$questionText</td></tr>";

				switch($questionType)
				{
					case "freeForm":
						echo "<tr><td colspan='2'><textarea name='freeText' rows='5' cols='5'></textarea></td></tr>";
						break;
					case "rating":
						$lowValue = $row['lowvalue'];
						$highValue = $row['highvalue'];
						$lowDescription = $row['lowdescription'];
						$highDescription = $row['highdescription'];

						echo "<tr><td>$lowDescription</td><td>$highDescription</td></tr>";

						echo "<tr><td colspan='2'>";

						echo "<ul>";
						for ($i = 1; $i <= $highValue; $i++)
						{
							echo "<li><input type='radio' name='rate' value='$i'>$i</li>";
						}	
						echo "</ul>";

						echo "</td></tr>";
						break;
					case "trueFalse":
						echo "<tr><td colspan='2'><input type='radio' name='tf' value='$answerText'>$answerText</td></tr>";
						break;
					case "multipleChoice":
						echo "<tr><td colspan='2'><input type='radio' name='mc' value='$answerText'>$answerText</td></tr>";
						break;
					case "severalAnswer":
						echo "<tr><td colspan='2'><input type='checkbox' name='sa' value='$answerText'>$answerText</td></tr>";
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
						echo "<tr><td colspan='2'><input type='radio' name='tf' value='$answerText'>$answerText</td></tr>";
						break;
					case "multipleChoice":
						echo "<tr><td colspan='2'><input type='radio' name='mc' value='$answerText'>$answerText</td></tr>";
						break;
					case "severalAnswer":
						echo "<tr><td colspan='2'><input type='checkbox' name='sa' value='$answerText'>$answerText</td></tr>";
						break;
				}			
			}
		}

		echo "</table>";
		
		// Close the connection.
		$conn->close();
	}


	function displayEditQuestionsAndAnswers($result)
	{
		echo "<table border='2'>";

		// Initialize the value of $questionNumber.
		$questionNumber = -1;
		$loop = 0;

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

				echo "<tr>";
				echo "<th>select</th>";
				echo "<th>Survey Name</th>";
				echo "<th>Question Number</th>";
				echo "<th>Question</th>";
				echo "<th>Question Type</th>";
				echo "<th colspan='5'>&nbsp;</th>";
				echo "</tr>";

				echo "<tr>";
				echo "<td><input type='checkbox' name='editSurveyQuestion' value=''</td>";
				echo "<td>$surveyName</td>";
				echo "<td>$questionNumber</td>";
				echo "<td>$questionText</td>";

				switch($questionType)
				{
					case "freeForm":
						//echo "<tr><td colspan='2'><textarea name='freeText' rows='5' cols='5'></textarea></td></tr>";
						echo "<td>Free Form Text</td>";
						echo "<td colspan='5'>&nbsp;</td>";
						break;
					case "rating":
						echo "<td>Rating</td>";

						$lowValue = $row['lowvalue'];
						$highValue = $row['highvalue'];
						$lowDescription = $row['lowdescription'];
						$highDescription = $row['highdescription'];

						echo "<td>$lowDescription</td>";
						echo "<td>$highDescription</td>";
						echo "<td>$lowValue</td>";
						echo "<td>$highValue</td>";
						echo "<td>&nbsp;</td>";

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
						break;
					case "rating":
						break;
					case "trueFalse":
						echo "<td colspan='3'>$answerText</td>";
						break;
					case "multipleChoice":
						if ($loop == 6)
						{
							echo "</tr>";
							echo "<tr>";
							echo "<td colspan='5'>&nbsp;</td>";
						}

						echo "<td>$answerText</td>";
						break;
					case "severalAnswer":
						if ($loop == 6)
						{
							echo "</tr>";
							echo "<tr>";
							echo "<td colspan='5'>&nbsp;</td>";
						}

						echo "<td>$answerText</td>";
						break;
				}			
			}

			$loop++;
		}
		echo "</tr>";
		echo "</table>";
		
		// Close the connection.
		$conn->close();
	}
?>
