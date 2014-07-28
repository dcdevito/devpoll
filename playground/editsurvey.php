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
	$result = getDistrictQuestionsAndAnsers(1);

	echo "The number of rows we got is ".$result->num_rows;

	echo "About to call displayEditQuestionsAndAnswers<br/>";

	displayEditQuestionsAndAnswers($result);

	echo "Called it<br/>";

	function getDistrictQuestionsAndAnsers($districtId)
	{
		// Connect to the database.
		require("connectToDB.php");

		echo "In display questions and districtId = $districtId<br/>";
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
						WHERE s.districtId = $districtId;
		";

		// Get the questions and answers for this survey.
		$result = $conn->query($questionQuery);

		return $result;
	}


	function displayEditQuestionsAndAnswers($result)
	{
		echo "<table border='2' width='80%'>";
		echo "<tr>";
		echo "<th>select</th>";
		echo "<th>Survey Name</th>";
		echo "<th>Question Number</th>";
		echo "<th>Question</th>";
		echo "<th>Question Type</th>";
		echo "<th colspan='5'>&nbsp;</th>";
		echo "</tr>";

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
