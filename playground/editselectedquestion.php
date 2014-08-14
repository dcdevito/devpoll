<?php
	// Make sure the person is logged in.
	include("verifylogin.php");

	define("MAX_ANSWERS", 10);
	define("MAX_RATING", 10);
?>

<html>
	<head>
		<title>DevPoll</title>

		<script type="text/javascript">
			var XMLHttpRequestObject = false;

			if (window.XMLHttpRequest)
			{
				XMLHttpRequestObject = new XMLHttpRequest();
			}
			else if (window.ActiveXObject)
			{
				XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
			}

			function loadMultipleChoice(answerArray)
			{
				if (XMLHttpRequestObject)
				{
					XMLHttpRequestObject.open("POST", "loadMultipleChoice.php");

					XMLHttpRequestObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

					XMLHttpRequestObject.onreadystatechange = function()
					{
						if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200)
						{
							var returnedData = XMLHttpRequestObject.responseText;

							var mcAnswersDiv = document.getElementById('mcAnswers');
							mcAnswersDiv.innerHTML = returnedData;
						}
					}
				}

				var answers = document.getElementById('mChoice').value;
				
				XMLHttpRequestObject.send('answers=' + answers + "&answerArray=" + answerArray);
			}

			function loadSeveralAnswers()
			{
				if (XMLHttpRequestObject)
				{
					XMLHttpRequestObject.open("POST", "loadSeveralAnswers.php");

					XMLHttpRequestObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

					XMLHttpRequestObject.onreadystatechange = function()
					{
						if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200)
						{
							var returnedData = XMLHttpRequestObject.responseText;

							var sevAnswersDiv = document.getElementById('sevAnswers');
							sevAnswersDiv.innerHTML = returnedData;
						}
					}
				}

				var answers = document.getElementById('severalAnswers').value;
				
				XMLHttpRequestObject.send('answers=' + answers);
			}

			function addRatingDescriptions()
			{
				if (XMLHttpRequestObject)
				{
					XMLHttpRequestObject.open("POST", "loadRatingDescriptions.php");

					XMLHttpRequestObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

					XMLHttpRequestObject.onreadystatechange = function()
					{
						if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200)
						{
							var returnedData = XMLHttpRequestObject.responseText;

							var ratingDescriptions = document.getElementById('ratingDescriptions');
							ratingDescriptions.innerHTML = returnedData;
						}
					}
				}

				var descriptions = document.getElementById('ratingValue').value;
				
				XMLHttpRequestObject.send('descriptions=' + descriptions);
			}

			function includeQuestions()
			{
				// Launch the Include Questions page.
				window.location = "includequestions.php";
			}
		</script>
	</head>
	<body>
		<?php
			//if ($_SERVER["REQUEST_METHOD"] == "POST")
			//{
				//   ***********************************************************
				//  *************************************************************
				// **** WE NEED TO REPLACE districtId WITH A SESSION VARIABLE ****
				//  *************************************************************
				//   ***********************************************************
				$districtId = 1;

				$surveyId = $_POST['surveyId'];
				$questionId = $_POST['editQuestion'];

				echo "Survey Id = $surveyId<br/>";
				echo "Question Id = $questionId<br/>";

				echo "About to call getSelectedQuestion<br/>";
				echo "District Id = $districtId<br/>";
				echo "SurveyId  = $surveyId<br/>";
				echo "Question Id = $questionId<br/>";

				$result = getSelectedQuestion($districtId, $surveyId, $questionId);

				$rows = $result->num_rows;

				echo "We got back $rows rows<br/>";
				echo "Calling displayQuestion<br/>";
				// Display the selected question to edit.
				displayQuestion($districtId, $surveyId, $result);
			//}

	// ----------------------------------------------------------------------
	// Add the survey name to the Database.
	// ----------------------------------------------------------------------
	function addSurveyToDB($surveyName, $districtId)
	{
		// Connect to the database.
		include("connectToDB.php");

		// Insert the values into the database.
		$query = "INSERT INTO survey(surveyName, districtId, dateopen) VALUES (?, ?, now())"; 

		if ($stmt = $conn->prepare($query))
		{
			$stmt->bind_param('si', $surveyName, $districtId);

			if ($stmt->execute())
			{
				//echo "Success ".$stmt->insert_id."<br/>";
				$_SESSION['surveyName'] = $surveyName;
			}
			else
			{
				die("Error".$conn->errno." ".$conn->error);
			} 			
		}
		else
		{
			echo "False<br/>";
		}

		$stmt -> close(); 
	}

	// ----------------------------------------------------------------------
	// Create a trueFalse question.
	// ----------------------------------------------------------------------
	function editTrueFalse($districtId, $surveyId, $questionId, $questionNumber, $questionText, $trueAnswer, $falseAnswer)
	{
					echo "In editTrueFalse...<br/>";
					echo "surveyId = $surveyId<br/>";
					echo "questionId = $questionId<br/>";
					echo "questionNumber = $questionNumber<br/>";
					echo "questionText = $questionText<br/>";
					echo "trueAnswer = $trueAnswer<br/>";
					echo "falseAnswer = $falseAnswer<br/>";

		echo "
			<div id='trueFalse'>
				<form action='editTrueFalseQuestion.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Edit True / False Question</p>
					<p>Please enter the question:</p>
					<p><textarea name='questionText' rows='3' cols='30' required>$questionText</textarea></p>
		";

		$yesNoSelected = "";
		$abSelected = "";
		$trueFalseSelected = "";
		$yesNoSelected = "";
		$customSelected = "";
		if ($trueAnswer == "Yes" && $falseAnswer == "No")
		{
			$yesNoSelected = "checked";			
		}
		elseif ($trueAnswer == "A" && $falseAnswer == "B")
		{
			$abSelected = "checked";
		}
		elseif ($trueAnswer == "True" && $falseAnswer == "False")
		{
			$trueFalseSelected = "checked";
		}
		else
		{
			$customSelected = "checked";
		}

		echo "yesNoSelected = $yesNoSelected<br/>";
		echo "abSelected = $abSelected<br/>";
		echo "trueFalseSelected = $trueFalseSelected<br/>";
		echo "customSelected = $customSelected<br/>";

		echo "
					<p>Answer type:</p>
					<p>
					<input type='radio' name='trueFalsetype' value='trueFalse' $trueFalseSelected>True / False<br/>
					<input type='radio' name='trueFalsetype' value='ab' $abSelected>A / B<br/>
					<input type='radio' name='trueFalsetype' value='yesno' $yesNoSelected>Yes / No<br/>
					<input type='radio' name='trueFalsetype' value='custom' $customSelected>Custom<br/>
		";

		if ($customSelected == "selected")
		{
			echo "
					<input type='text' name='trueFalsecustom1' value='$trueAnswer'>&nbsp;/&nbsp;
					<input type='text' name='trueFalsecustom2' value='$falseAnswer'><br/>
			";
		}
		else
		{			
			echo "
					<input type='text' name='trueFalsecustom1' value=''>&nbsp;/&nbsp;
					<input type='text' name='trueFalsecustom2' value=''><br/>
			";
		}

		echo "
					</p>
					<input type='submit' value='Save'>
					&nbsp;&nbsp;
					<input type='button' value='Cancel'>
					</p>
					<input type='hidden' name='districtId' value='$districtId'>
					<input type='hidden' name='createdQuestion' value='trueFalse'>
					<input type='hidden' name='surveyId' value='$surveyId'>
					<input type='hidden' name='createType' value='trueFalse'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
					<input type='hidden' name='questionId' value='$questionId'>
				</form>
			</div>
		";
	}

	// ----------------------------------------------------------------------
	// Create a multiple choice question.
	// ----------------------------------------------------------------------
	function editMultipleChoice($districtId, $surveyId, $questionId, $questionNumber, $questionText, $answers)
	{
					echo "In createMultipleChoice...<br/>";
					echo "surveyId = $surveyId<br/>";
					echo "questionId = $questionId<br/>";
					echo "questionNumber = $questionNumber<br/>";
					echo "questionText = $questionText<br/>";

		// Get how many answers we have.
		$answerCount = count($answers);

		echo "
			<div id='multipleChoice'>
				<form action='editMultipleChoiceQuestion.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create Multiple Choice Question</p>
					<p>Please enter the question:</p>
					<p><textarea name='questionText' rows='3' cols='30'>$questionText</textarea></p>
					<p>
		";

		// Answer 1 and 2
		echo "How many answers?";

		echo "<select id='mChoice' name='mChoice' required='required'>";

		for ($i = 1; $i <= MAX_ANSWERS; $i++)
		{
			echo "<option value='$i'";

			if ($i == $answerCount)
			{
				echo "selected";
			}

			echo ">";
			echo $i;
			echo "</option>";
		}

		echo "</select><br/>";
		echo "<p><input type='button' value='Create Answers' onclick='loadMultipleChoice($answers);'></p>";


		echo "<div id='mcAnswers'>";

		echo "Count = ".count($answers)."<br/>";
		for ($i = 0; $i < count($answers); $i++)
		{
			echo "We are in $i ";
			$value = $answers[$i];

			echo "Value = $value<br/>";

			$answerNumber = $i + 1;
			echo "Answer $answerNumber: <input type='text' name='mcanswer$answerNumber' value='$value'><br/>";
		}
		echo "<br/>";

		echo "</div>";
		echo "</p>";

		echo "
					<p>
						<input type='submit' value='Save'>
						&nbsp;&nbsp;
						<input type='button' value='Cancel'>
					</p>
					<input type='hidden' name='districtId' value='$districtId'>
					<input type='hidden' name='createdQuestion' value='trueFalse'>
					<input type='hidden' name='surveyId' value='$surveyId'>
					<input type='hidden' name='createType' value='trueFalse'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
					<input type='hidden' name='questionId' value='$questionId'>
				</form>
			</div>
		";
	}

	// ----------------------------------------------------------------------
	// Create a several answer question.
	// ----------------------------------------------------------------------
	function editSeveralAnswer($surveyId, $surveyName, $questionNumber, $everyQuestion)
	{
					echo "In createSeveralAnswer...<br/>";
					echo "surveyId = $surveyId<br/>";
					echo "surveyName = $surveyName<br/>";
					echo "questionNumber = $questionNumber<br/>";
					echo "everyQuestion = $everyQuestion<br/>";

		echo "
			<div id='severalAnswer'>
				<form action='editSeveralAnswerQuestion.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create Several Answer Question</p>
					<p>Please enter the question:</p>
					<p><textarea name='questionText' rows='3' cols='30'></textarea></p>
					<p>
		";

		// Answer 1 and 2
		echo "How many answers?";

		echo "<select id='severalAnswers' name='severalAnswers' required='required'>";

		for ($i = 1; $i <= MAX_ANSWERS; $i++)
		{
			echo "<option value='$i'>$i</option>";
		}

		echo "</select><br/>";
		echo "<p><input type='button' value='Create Answers' onclick='loadSeveralAnswers();'></p>";

		echo "<div id='sevAnswers'></div>";
		echo "</p>";

		// Check the "everyQuestion" checkbox if it was checked already.
		echo "<p><input type='checkbox' name='everyQuestion' value='everyQuestion'";

		if ($everyQuestion == "everyQuestion")
		{
			echo " checked";
		}

		echo ">Every question is of this type</p>";

		echo "
					<p>
						<input type='submit' value='Save'>
						&nbsp;&nbsp;
						<input type='button' value='Cancel'>
					</p>
					<input type='hidden' name='districtId' value='$districtId'>
					<input type='hidden' name='createdQuestion' value='trueFalse'>
					<input type='hidden' name='surveyId' value='$surveyId'>
					<input type='hidden' name='createType' value='trueFalse'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
					<input type='hidden' name='questionId' value='$questionId'>
				</form>
			</div>
		";
	}

	// ----------------------------------------------------------------------
	// Create a free form text question. 
	// ----------------------------------------------------------------------
	function editFreeFormText($districtId, $surveyId, $questionId, $questionNumber, $questionText)
	{
					echo "In editFreeFormText...<br/>";
					echo "surveyId = $surveyId<br/>";
					echo "questionId = $questionId<br/>";
					echo "questionNumber = $questionNumber<br/>";
					echo "questionText = $questionText<br/>";

		echo "
			<div id='freeFormText'>
				<form action='editFreeFormQuestion.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create Free Form Question</p>
					<p>Please enter the question:</p>
					<p><textarea name='questionText' rows='3' cols='30'>$questionText</textarea></p>
		";

		echo "
					<p>
						<input type='submit' value='Save'>
						&nbsp;&nbsp;
						<input type='button' value='Cancel'>
					</p>
					<input type='hidden' name='districtId' value='$districtId'>
					<input type='hidden' name='createdQuestion' value='trueFalse'>
					<input type='hidden' name='surveyId' value='$surveyId'>
					<input type='hidden' name='createType' value='trueFalse'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
					<input type='hidden' name='questionId' value='$questionId'>
				</form>
			</div>
		";
	}

	// ----------------------------------------------------------------------
	// Create a rating question.
	// ----------------------------------------------------------------------
	function editRating($districtId, $surveyId, $questionId, $questionNumber, $questionText, $lowValue, $highValue, $lowDescription, $highDescription)
	{
					echo "In createRating...<br/>";
					echo "surveyId = $surveyId<br/>";
					echo "questionId = $questionId<br/>";
					echo "questionNumber = $questionNumber<br/>";
					echo "questionText = $questionText<br/>";
					echo "lowValue = $lowValue<br/>";
					echo "highValue = $highValue<br/>";
					echo "lowDescription = $lowDescription<br/>";
					echo "highDescription = $highDescription<br/>";

		echo "
			<div id='rating'>
				<form action='editRatingQuestion.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create rating Question</p>
					<p>Please enter the question:</p>
					<p><textarea name='questionText' rows='3' cols='30'>$questionText</textarea></p>

					<p>
						rating from 1 to 
						<select id='ratingValue' name='ratingValue' required='required'>
		";

		for ($i = MAX_RATING; $i >= 1; $i--)
		{
			echo "<option value='$i' ";

			if ($i == $highValue)
			{
				echo "selected";
			}

			echo ">";
			echo $i;
			echo "</option>";
		}

		echo "
						</select>
						<br/>
						
						<p><input type='button' value='Add Rating Descriptions' onclick='addRatingDescriptions();'></p>
						<div id='ratingDescriptions'></div>
					</p>
					<p>
						Enter the word to describe the lowest rating:
						<input type='text' name='ratingLowValue' value='$lowDescription'>
					</p>

					<p>
						Enter the word to describe the highest rating:
						<input type='text' name='ratingHighValue' value='$highDescription'>
					</p>
		";

		echo "
					<p>
						<input type='submit' value='Save'>
						&nbsp;&nbsp;
						<input type='button' value='Cancel'>
					</p>
					<input type='hidden' name='districtId' value='$districtId'>
					<input type='hidden' name='createdQuestion' value='trueFalse'>
					<input type='hidden' name='surveyId' value='$surveyId'>
					<input type='hidden' name='createType' value='trueFalse'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
					<input type='hidden' name='questionId' value='$questionId'>
				</form>
			</div>
		";
	}


	function getSelectedQuestion($districtId, $surveyId, $questionId)
	{
		// Connect to the database.
		require("connectToDB.php");

		echo "In getSelectedQuestion($districtId, $surveyId, $questionId)<br/>";
		$questionQuery = "
						SELECT
						q.questionid,
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
						AND s.surveyid = $surveyId
						AND q.questionid = $questionId;
		";

		// Get the questions and answers for this survey.
		$result = $conn->query($questionQuery);

		// Close the connection.
		$conn->close();		

		return $result;
	}

	// ----------------------------------------------------------------------
	// Display the questions we have already created.
	// ----------------------------------------------------------------------
	function displayQuestion($districtId, $surveyId, $result)
	{
		$answers = array();
		$answerLoop = 0;

		echo "In the display questions <br/>";

		// Loop through the result and create the question.
		while($row = $result->fetch_assoc()) 
		{
			$questionId = $row['questionid'];
			$questionNumber = $row['questionnumber'];
			$questionText = $row['questiontext'];
			$questionType = $row['questiontype'];

			$answerNumber = $row['answernumber'];

				echo "Answer number = $answerNumber<br/>";
			$answerText = $row['answertext'];
				echo "Answer text = $answerText<br/>";	
			$answers[] = $answerText;

			$lowValue = $row['lowvalue'];
			$highValue = $row['highvalue'];
			$lowDescription = $row['lowdescription'];
			$highDescription = $row['highdescription'];
		}

		echo "Out of the loop<br/>";
		echo "questionId = $questionId<br/>";
		echo "questionNumber = $questionNumber<br/>";
		echo "questionText = $questionText<br/>";
		echo "lowValue = $lowValue<br/>";
		echo "highValue = $highValue<br/>";
		echo "lowDescription = $lowDescription<br/>";
		echo "highDescription = $highDescription<br/>";
		echo "<br/>";
		echo "questionType = $questionType<br/>";
		echo "<br/>";
		echo "answers: <br/>";

		$i = 0;
		foreach ($answers as $value) 
		{
			echo "answers[$i] = $value<br/>";
			$i++;
		}

		// Redirect to edit the question.
		switch($questionType)
		{
			case "freeForm":
				editFreeFormText($districtId, $surveyId, $questionId, $questionNumber, $questionText);
				break;
			case "rating":
				editRating($districtId, $surveyId, $questionId, $questionNumber, $questionText, $lowValue, $highValue, $lowDescription, $highDescription);
				break;
			case "trueFalse":
				$trueAnswer = $answers[0];
				$falseAnswer = $answers[1];

				echo "True Answer = $trueAnswer<br/>";
				echo "False Answer = $falseAnswer<br/>";

				editTrueFalse($districtId, $surveyId, $questionId, $questionNumber, $questionText, $trueAnswer, $falseAnswer);
				break;
			case "multipleChoice":
				editMultipleChoice($districtId, $surveyId, $questionId, $questionNumber, $questionText, $answers);
				break;
			case "severalAnswer":
				//editSeveralAnswer($districtId, $surveyId, $questionId, $questionNumber, $questionText, $answers);
				break;
		}
	}
?>
