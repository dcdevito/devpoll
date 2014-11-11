<?php
	// Display the selected question so it can be edited

	// Make sure the person is logged in.
	include("verifylogin.php");

	// Constant values.
	include("constants.php");
?>

<html>
	<head>
		<title>DevPoll</title>

		<script type="text/javascript">
			// AJAX calls to save the edited questions.

			//*****************************
			//	Default AJAX call stuff
			//*****************************
			var XMLHttpRequestObject = false;

			if (window.XMLHttpRequest)
			{
				XMLHttpRequestObject = new XMLHttpRequest();
			}
			else if (window.ActiveXObject)
			{
				XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
			}

			//*************************************************************
			//	Load the answer boxes for the multiple choice question
			//*************************************************************
			function loadMultipleChoice()
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
				var answerCount = document.getElementById('mcAnswerCount').value;

//				alert("answers = " + answers);
//				alert("answerCount = " + answerCount);

				var mcAnswer;
				var mcAnswerNumber;
				var sendstring = "answers=" + answers + "&answercount=" + answerCount;

				for (var i = 1; i <= answerCount; i++)
				{
					mcAnswerNumber = 'mcanswer' + i;

//					alert("mcAnswerNumber = " + mcAnswerNumber);

					mcAnswer = document.getElementById(mcAnswerNumber).value;

//					alert("mcAnswer = " + mcAnswer);

					sendstring = sendstring + '&' + mcAnswerNumber + '=' + mcAnswer;

//					alert("final sendstring = " + sendstring);
				}

//				alert("sendstring = " + sendstring);

				XMLHttpRequestObject.send(sendstring);
			}

			//************************************************************
			//	Load the answer boxes for the several answer question
			//************************************************************
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
				var answerCount = document.getElementById('sevAnswerCount').value;

				var sevAnswer;
				var sevAnswerNumber;
				var sendstring = "answers=" + answers + "&answercount=" + answerCount;
				
				for (var i = 1; i <= answerCount; i++)
				{
					sevAnswerNumber = 'sevanswer' + i;

					sevAnswer = document.getElementById(sevAnswerNumber).value;

					sendstring = sendstring + '&' + sevAnswerNumber + '=' + sevAnswer;
				}

				XMLHttpRequestObject.send(sendstring);				
			}

			//*************************************************
			//	Load the boxes for the rating descriptions
			//*************************************************
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

			//*******************************************
			//	Save the edited true/false question 
			//*******************************************
			function saveTrueFalse()
			{
				if (XMLHttpRequestObject)
				{
					XMLHttpRequestObject.open("POST", "editTrueFalseQuestion.php");

					XMLHttpRequestObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

					XMLHttpRequestObject.onreadystatechange = function()
					{
						if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200)
						{
							var returnedData = XMLHttpRequestObject.responseText;

							var truefalseDiv = document.getElementById('truefalseDiv');
							truefalseDiv.innerHTML = returnedData;
						}
					}
				}

				var truefalseId = document.getElementById('truefalseId').checked;
				var abId = document.getElementById('abId').checked;
				var yesnoId = document.getElementById('yesnoId').checked;
				var customId = document.getElementById('customId').checked;

				alert("truefalseId = " + truefalseId);
				alert("ab = " + abId);
				alert("yesnoId = " + yesnoId);
				alert("customId = " + customId);

				var answer1;
				var answer2;

				if (truefalseId == true)
				{
					answer1 = "True";
					answer2 = "False";
				}
				else if (abId == true)
				{
					answer1 = "A";
					answer2 = "B";
				}
				else if (yesnoId == true)
				{
					answer1 = "Yes";
					answer2 = "No";
				}
				else if (customId == true)
				{
					answer1 = document.getElementById('customHeader1').value;
					answer2 = document.getElementById('customHeader2').value;
				}
				else
				{
					return;
				}

				alert("answer1 = " + answer1);
				alert("answer2 = " + answer2);

				districtId = document.getElementById('districtId').value;
				surveyId = document.getElementById('surveyId').value;
				questionNumber = document.getElementById('questionNumber').value;
				questionId = document.getElementById('questionId').value;


				sendstring = sendstring + '&' + mcAnswerNumber + '=' + mcAnswer;

				alert("sendstring = " + sendstring);

				XMLHttpRequestObject.send(sendstring);
			}

			//***************************************
			//	Launch the includequestions page
			//***************************************
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

				// Get the question for the given question id for the given survey id.
				$result = getSelectedQuestion($districtId, $surveyId, $questionId);

				// Get the number of rows returned by the query.
				$rows = $result->num_rows;

				// Display the selected question to edit.
				displayQuestion($districtId, $surveyId, $result);
			//}

	//******************************************
	//	Add the survey name to the Database
	//******************************************
	function addSurveyToDB($surveyName, $districtId)
	{
		// Connect to the database.
		include("connectToDB.php");

		// Insert the values into the database.
		$query = "INSERT INTO survey(surveyName, districtId, dateopen) VALUES (?, ?, now())"; 

		// Prepare the prepared statement.
		if ($stmt = $conn->prepare($query))
		{
			// Populate the prepared statement.
			$stmt->bind_param('si', $surveyName, $districtId);

			// Run the query.
			if ($stmt->execute())
			{
				//echo "Success ".$stmt->insert_id."<br/>";
				//$_SESSION['surveyName'] = $surveyName;
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

		// Close the database connection.
		$stmt->close(); 
		$conn->close();
	}

	//**********************************
	//	Create a trueFalse question
	//**********************************
	function editTrueFalse($districtId, $surveyId, $questionId, $questionNumber, $questionText, $trueAnswer, $falseAnswer)
	{
		echo "
			<div id='trueFalse'>
				<form action='editTrueFalseQuestion.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Edit True / False Question</p>
					<p>Please enter the question:</p>
					<p><textarea name='questionText' id='truefalseQuestionText' rows='3' cols='30' required>$questionText</textarea></p>
		";

		$yesNoSelected = "";
		$abSelected = "";
		$trueFalseSelected = "";
		$yesNoSelected = "";
		$customSelected = "";

		// Get which choice should be selected when they are displayed.
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

		// Display the different choices.
		echo "
					<p>Answer type:</p>
					<p>
					<input type='radio' name='trueFalsetype' id='truefalseId' value='trueFalse' $trueFalseSelected>True / False<br/>
					<input type='radio' name='trueFalsetype' id='abId' value='ab' $abSelected>A / B<br/>
					<input type='radio' name='trueFalsetype' id='yesnoId' value='yesno' $yesNoSelected>Yes / No<br/>
					<input type='radio' name='trueFalsetype' id='customId' value='custom' $customSelected>Custom<br/>
		";

		// If the custom option is chosen - populate the custom headers.
		if ($customSelected == "selected")
		{
			echo "
					<input type='text' name='trueFalsecustom1' id='customHeader1' value='$trueAnswer'>&nbsp;/&nbsp;
					<input type='text' name='trueFalsecustom2' id='customHeader2' value='$falseAnswer'><br/>
			";
		}
		else
		{			
			echo "
					<input type='text' name='trueFalsecustom1' id='customHeader1' value=''>&nbsp;/&nbsp;
					<input type='text' name='trueFalsecustom2' id='customHeader2' value=''><br/>
			";
		}

		// Hidden fields hold the information needed to save the question in the database.
		echo "
					</p>
					<input type='button' value='Save' onclick='saveTrueFalse();'>
					&nbsp;&nbsp;
					<input type='button' value='Cancel' onclick='cancel();'>

					<p><div id='truefalseDiv'></div></p>

					<input type='hidden' name='districtId' id='districtId' value='$districtId'>
					<input type='hidden' name='createdQuestion' id='createdQuestion' value='".TRUE_FALSE."'>
					<input type='hidden' name='surveyId' id='surveyId' value='$surveyId'>
					<input type='hidden' name='createType' id='createType' value='".TRUE_FALSE."'>
					<input type='hidden' name='questionNumber' id='questionNumber' value='$questionNumber'>
					<input type='hidden' name='questionId' id='questionId' value='$questionId'>
				</form>
			</div>
		";
	}

	//****************************************
	//	Create a multiple choice question
	//****************************************
	function editMultipleChoice($districtId, $surveyId, $questionId, $questionNumber, $questionText, $answers)
	{
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

		// How many possible answers are required for this multiple choice question.
		echo "How many answers?";

		// Display the drop down box with the number of answers.
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

		// Perform the AJAX call to display the answer boxes for the mutliple choice answers.
		echo "<p><input type='button' value='Create Answers' onclick='loadMultipleChoice();'></p>";

		// A div to hold the results of the AJAX call.
		echo "<div id='mcAnswers'>";

		// Display the answers that have already been filled in.
		for ($i = 0; $i < count($answers); $i++)
		{
			$value = $answers[$i];

			$answerNumber = $i + 1;
			$name = 'mcanswer'.$answerNumber;

			echo "Answer $answerNumber: <input type='text' name='$name' id='$name' value='$value'><br/>";
		}
		echo "<br/>";

		echo "</div>";
		echo "</p>";

		// Hidden fields hold the information needed to save the question in the database.
		echo "
					<p>
						<input type='submit' value='Save'>
						&nbsp;&nbsp;
						<input type='button' value='Cancel'>
					</p>
					<input type='hidden' name='districtId' value='$districtId'>
					<input type='hidden' name='createdQuestion' value='".MULTIPLE_CHOICE."'>
					<input type='hidden' name='surveyId' value='$surveyId'>
					<input type='hidden' name='createType' value='".MULTIPLE_CHOICE."'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
					<input type='hidden' name='questionId' value='$questionId'>
					<input type='hidden' name='answerCount' id='mcAnswerCount' value='$answerCount'>
				</form>
			</div>
		";
	}

	//***************************************
	//	Create a several answer question
	//***************************************
	function editSeveralAnswer($districtId, $surveyId, $questionId, $questionNumber, $questionText, $answers)
	{
		// Get how many answers we have.
		$answerCount = count($answers);

		echo "
			<div id='severalAnswer'>
				<form action='editSeveralAnswerQuestion.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create Several Answer Question</p>
					<p>Please enter the question:</p>
					<p><textarea name='questionText' rows='3' cols='30'>$questionText</textarea></p>
					<p>
		";

		// How many possible answers are required for this several answer question.
		echo "How many answers?";

		// Display the drop down box with the number of answers.
		echo "<select id='severalAnswers' name='severalAnswers' required='required'>";

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


		// Perform the AJAX call to display the answer boxes for the several answer answers.
		echo "<p><input type='button' value='Create Answers' onclick='loadSeveralAnswers();'></p>";

		// A div to hold the results of the AJAX call.
		echo "<div id='sevAnswers'>";

		// Display the answers that have already been filled in.
		for ($i = 0; $i < count($answers); $i++)
		{
			$value = $answers[$i];

			$answerNumber = $i + 1;
			$name = 'sevanswer'.$answerNumber;

			echo "Answer $answerNumber: <input type='text' name='$name' id='$name' value='$value'><br/>";
		}
		echo "<br/>";

		echo "</div>";
		echo "</p>";

		// Hidden fields hold the information needed to save the question in the database.
		echo "
					<p>
						<input type='submit' value='Save'>
						&nbsp;&nbsp;
						<input type='button' value='Cancel'>
					</p>
					<input type='hidden' name='districtId' value='$districtId'>
					<input type='hidden' name='createdQuestion' value='".SEVERAL_ANSWER."'>
					<input type='hidden' name='surveyId' value='$surveyId'>
					<input type='hidden' name='createType' value='".SEVERAL_ANSWER."'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
					<input type='hidden' name='questionId' value='$questionId'>
					<input type='hidden' name='answerCount' id='sevAnswerCount' value='$answerCount'>
				</form>
			</div>
		";
	}

	//***************************************
	//	Create a free form text question
	//***************************************
	function editFreeFormText($districtId, $surveyId, $questionId, $questionNumber, $questionText)
	{
		echo "
			<div id='freeFormText'>
				<form action='editFreeFormQuestion.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create Free Form Question</p>
					<p>Please enter the question:</p>
					<p><textarea name='questionText' rows='3' cols='30'>$questionText</textarea></p>
		";

		// Hidden fields hold the information needed to save the question in the database.
		echo "
					<p>
						<input type='submit' value='Save'>
						&nbsp;&nbsp;
						<input type='button' value='Cancel'>
					</p>
					<input type='hidden' name='districtId' value='$districtId'>
					<input type='hidden' name='createdQuestion' value='".FREE_FORM."'>
					<input type='hidden' name='surveyId' value='$surveyId'>
					<input type='hidden' name='createType' value='".FREE_FORM."'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
					<input type='hidden' name='questionId' value='$questionId'>
				</form>
			</div>
		";
	}

	//*******************************
	//	Create a rating question
	//*******************************
	function editRating($districtId, $surveyId, $questionId, $questionNumber, $questionText, $lowValue, $highValue, $descriptions)
	{
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
						
						<p><input type='button' value='Add Rating Descriptions' onclick='addRatingDescriptions($descriptions);'></p>
		";

		echo "<div id='ratingDescriptions'>";

		for ($i = 0; $i < count($descriptions); $i++)
		{
			$value = $descriptions[$i];

			$descriptionNumber = $i + 1;
			echo "Description $answerNumber: <input type='text' name='rating$descriptionNumber' value='$value'><br/>";
		}
		echo "<br/>";

		echo "</div>";
		echo "</p>";

		// Hidden fields hold the information needed to save the question in the database.
		echo "
					<p>
						<input type='submit' value='Save'>
						&nbsp;&nbsp;
						<input type='button' value='Cancel'>
					</p>
					<input type='hidden' name='districtId' value='$districtId'>
					<input type='hidden' name='createdQuestion' value='".RATING."'>
					<input type='hidden' name='surveyId' value='$surveyId'>
					<input type='hidden' name='createType' value='".RATING."'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
					<input type='hidden' name='questionId' value='$questionId'>
				</form>
			</div>
		";
	}


	//************************************************************************
	//	Get the question for the given question id in the given survey id
	//************************************************************************
	function getSelectedQuestion($districtId, $surveyId, $questionId)
	{
		// Connect to the database.
		require("connectToDB.php");

		$questionQuery = "SELECT q.questionid,
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
						AND   s.surveyid = $surveyId
						AND   q.questionid = $questionId;";

		// Get the questions and answers for this survey.
		$result = $conn->query($questionQuery);

		// Close the connection.
		$conn->close();		

		return $result;
	}

	//****************************************************
	//	Display the questions we have already created
	//****************************************************
	function displayQuestion($districtId, $surveyId, $result)
	{
		$answers = array();
		$ratingdescriptions = array();
		$answerLoop = 0;

		// Loop through the result and create the question.
		while($row = $result->fetch_assoc()) 
		{
			$questionId = $row['questionid'];
			$questionNumber = $row['questionnumber'];
			$questionText = $row['questiontext'];
			$questionType = $row['questiontype'];

			$answerNumber = $row['answernumber'];

			$answerText = $row['answertext'];
			$answers[] = $answerText;

			// The high and low rating values.
			$lowValue = $row['lowvalue'];
			$highValue = $row['highvalue'];

			// The rating descriptions.
			$ratingdescriptions[0] = $row['ratingdescription1'];
			$ratingdescriptions[1] = $row['ratingdescription2'];
			$ratingdescriptions[2] = $row['ratingdescription3'];
			$ratingdescriptions[3] = $row['ratingdescription4'];
			$ratingdescriptions[4] = $row['ratingdescription5'];
			$ratingdescriptions[5] = $row['ratingdescription6'];
			$ratingdescriptions[6] = $row['ratingdescription7'];
			$ratingdescriptions[7] = $row['ratingdescription8'];
			$ratingdescriptions[8] = $row['ratingdescription9'];
			$ratingdescriptions[9] = $row['ratingdescription10'];
		}

		// Redirect to edit the question.
		switch($questionType)
		{
			case FREE_FORM:
				editFreeFormText($districtId, $surveyId, $questionId, $questionNumber, $questionText);
				break;
			case RATING:
				editRating($districtId, $surveyId, $questionId, $questionNumber, $questionText, $lowValue, $highValue, $descriptions);
				break;
			case TRUE_FALSE:
				$trueAnswer = $answers[0];
				$falseAnswer = $answers[1];

				editTrueFalse($districtId, $surveyId, $questionId, $questionNumber, $questionText, $trueAnswer, $falseAnswer);
				break;
			case MULTIPLE_CHOICE:
				editMultipleChoice($districtId, $surveyId, $questionId, $questionNumber, $questionText, $answers);
				break;
			case SEVERAL_ANSWER:
				editSeveralAnswer($districtId, $surveyId, $questionId, $questionNumber, $questionText, $answers);
				break;
		}
	}
?>
