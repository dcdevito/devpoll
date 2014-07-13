<?php
	// Make sure the person is logged in.
	include("verifylogin.php");
?>

<?php
	// Connect to the database.
	require("connectToDB.php");

	$surveyid = $_SESSION['surveyId'];

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
					WHERE q.surveyid = $surveyid;
	";

	// Get the questions and answers for this survey.
	$result = $conn->query($questionQuery);

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

		//echo "Question Number = $questionNumber<br/>";
		//echo "Row question number = $qn<br/>";

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

?>
