// Need a couple of checkbox that say:
// 1) Add a message at the top.
// 2) Add rating headings - For a rating question.
// We need another question type which is a rating grid...like Sandy wants for DevPoll.


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
				
				XMLHttpRequestObject.send('answers=' + answers);
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
					XMLHttpRequestObject.open("POST", "addRatingDescriptions.php");

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
		</script>
	</head>
	<body>
		<?php 
			if ($_SERVER["REQUEST_METHOD"] == "POST")
			{
				//   ***********************************************************
				//  *************************************************************
				// **** WE NEED TO REPLACE districtId WITH A SESSION VARIABLE ****
				//  *************************************************************
				//   ***********************************************************
				$districtId = 1;

				$enterName = mysql_real_escape_string($_POST['enterName']);
				$surveyName = mysql_real_escape_string($_POST['surveyName']);
				$surveyId = mysql_real_escape_string($_POST['surveyId']);

				$questionNumber = mysql_real_escape_string($_POST['questionNumber']);

				echo "Question Number = $questionNumber<br/>";
				echo "Survey Name = $surveyName<br/>";
				echo "Survey Id = $surveyId<br/>";

				// ----------------------------------------------------------------------
				// Are we entering the name for the first time.
				// ----------------------------------------------------------------------
				if ($enterName == "enterName")
				{
					//-------------------------------------------
					echo "We are entering the survey name<br/>";
					//-------------------------------------------

					$canEnterQuestions = false;

					// Connect to the database.
					include("connectToDB.php");

					// Check if the name is taken for this district.
					$result = $conn->query("SELECT surveyName FROM survey WHERE districtId = $districtId");

					$nameOK = true;
					while($row = $result->fetch_assoc()) 
					{
						if ($row['surveyName'] == $surveyName)
						{
							// The name is taken.
							$nameOK = false;
						}
					}

					// Close the connection.
					$conn->close();

					if ($nameOK == true)
					{
						// Write the name to the database.
						$result = addSurveyToDB($surveyName, $districtId);

						$canEnterQuestions = true;

						$questionNumber = 0;

						// Get survey id.
						$surveyId = getSurveyId($surveyName, $districtId);
					}
					else
					{
						// Enter survey name again.
						echo "That name is already taken. Please enter a new name.<br/><br/>";
						enterSurveyName();
					}
				}
				else
				{
					// ----------------------------------------------------------------------
					// The survey name is valid, and we can enter a question.
					// ----------------------------------------------------------------------
					if (empty($surveyId) or is_null($surveyId))
					{
						$surveyId = getSurveyId($surveyName, $districtId);

						echo "surveyId = $surveyId<br/>";
					}

					$canEnterQuestions = true;
				}

				// ----------------------------------------------------------------------
				// Create the question of the selected type.
				// ----------------------------------------------------------------------
				if ($canEnterQuestions == true)
				{
					echo "In canEnterQuestions and surveyId = $surveyId<br/>";

					// See if every question is of the same type.
					$everyQuestion = mysql_real_escape_string($_POST['everyQuestion']);

					// The checkbox has been checked, so every question is of the same type.
					$createType = mysql_real_escape_string($_POST['createType']);

					if ($everyQuestion == "everyQuestion" && $createType != "selectType")
					{
						$questionNumber++;

						// Every question is of the same type.
						$questionType = $createType;
					}
					else
					{
						// Check what type of question was chosen and load that box.
						$questionType = mysql_real_escape_string($_POST['questionType']);
					}

					// What question type was chosen.
					if ($questionType == "trueFalse")
					{
						createTrueFalse($surveyId, $surveyName, $questionNumber, $everyQuestion);
					}
					elseif ($questionType == "multipleChoice")
					{
						createMultipleChoice($surveyId, $surveyName, $questionNumber, $everyQuestion);	
					}
					elseif ($questionType == "severalAnswer")
					{
						createSeveralAnswer($surveyId, $surveyName, $questionNumber, $everyQuestion);
					}
					elseif ($questionType == "freeForm")
					{
						createFreeFormText($surveyId, $surveyName, $questionNumber, $everyQuestion);
					}
					elseif ($questionType == "rating")
					{
						createRating($surveyId, $surveyName, $questionNumber, $everyQuestion);
					}
					else
					{
						// Next question number.
						$questionNumber++;

						createQuestionDiv($surveyId, $surveyName, $questionNumber, $everyQuestion);
					}
				}
			}
			else
			{
				$surveyInProgress = $_SESSION['surveyInProgress'];
				if ($surveyInProgress == 'YES')
				{
					$surveyId = $_SESSION['surveyId'];
					$surveyName = $_SESSION['surveyName'];
					$questionNumber = $_SESSION['questionNumber'];
					$everyQuestion = $_SESSION['everyQuestion'];

					// Clear the session variables.
					//$_SESSION['surveyInProgress'] = '';
					//$_SESSION['surveyId'] = '';
					//$_SESSION['surveyName'] = '';
					//$_SESSION['questionNumber'] = '';
					//$_SESSION['everyQuestion'] = '';

					echo "In the else NOT POST...<br/>";
					echo "surveyId = $surveyId<br/>";
					echo "surveyName = $surveyName<br/>";
					echo "questionNumber = $questionNumber<br/>";
					echo "everyQuestion = $everyQuestion<br/>";

					// Next question number.
					$questionNumber++;

					createQuestionDiv($surveyId, $surveyName, $questionNumber, $everyQuestion);
				}
				else
				{
					enterSurveyName();
				}
			}
		?>
	</body>
</html>

<?php
	// ----------------------------------------------------------------------
	// Display the question type selection.
	// ----------------------------------------------------------------------
	function createQuestionDiv($surveyId, $surveyName, $questionNumber, $everyQuestion)
	{
					echo "In createQuestionDiv...<br/>";
					echo "surveyId = $surveyId<br/>";
					echo "surveyName = $surveyName<br/>";
					echo "questionNumber = $questionNumber<br/>";
					echo "everyQuestion = $everyQuestion<br/>";

		echo "
			<div id='questions'>
				<form action='createsurvey.php' method='POST'>
					<p>Survey $surveyName</p>
					<p>Question Number: $questionNumber</p>
					<p>Please select the type of question:</p>
					<p><input type='radio' name='questionType' value='trueFalse'>True / False</p>
					<p><input type='radio' name='questionType' value='multipleChoice'>Multiple Choice<br/>
					<p><input type='radio' name='questionType' value='severalAnswer'>Several Answers</p>
					<p><input type='radio' name='questionType' value='freeForm'>Free Form Text</p>
					<p><input type='radio' name='questionType' value='rating'>Rating</p>
		";

		echo "<p><input type='checkbox' name='everyQuestion' value='everyQuestion'";

		if ($everyQuestion == "everyQuestion")
		{
			echo " checked";
		}

		echo ">Every question is of this type</p>";

		echo "
					<p><input type='submit' value='Create Question'></p>
					<p><input type='button' value='Include Existing Question'></p>
					<p><input type='button' value='Start Over' onclick='startOver();'></p>
					<p><input type='button' value='Stop entering questions'></p>
					<input type='hidden' name='surveyName' value='$surveyName'>
					<input type='hidden' name='surveyId' value='$surveyId'>
					<input type='hidden' name='createType' value='selectType'>
					<input type='text' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";

		displayQuestions($surveyId);
	}

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
	// Enter the name of the survey.
	// ----------------------------------------------------------------------
	function enterSurveyName()
	{
		echo "
			<div id='surveyNamepart'>
				<form action='createsurvey.php' method='POST'>
					<label>Survey Name: </label><input type='text' name='surveyName'>
					<br/>
					<input type='submit' value='Create Survey'>
					<input type='hidden' name='enterName' value='enterName'>
				</form>
			</div>
		";		
	}

	// ----------------------------------------------------------------------
	// Create a trueFalse question.
	// ----------------------------------------------------------------------
	function createTrueFalse($surveyId, $surveyName, $questionNumber, $everyQuestion)
	{
					echo "In createTrueFalse...<br/>";
					echo "surveyId = $surveyId<br/>";
					echo "surveyName = $surveyName<br/>";
					echo "questionNumber = $questionNumber<br/>";
					echo "everyQuestion = $everyQuestion<br/>";

		echo "
			<div id='trueFalse'>
				<form action='addTrueFalseQuestion.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create True / False Question</p>
					<p>Please enter the question:</p>
					<p><textarea name='questionText' rows='3' cols='30' required></textarea></p>
					<p>Answer type:</p>
					<p>
					<input type='radio' name='trueFalsetype' value='trueFalse'>True / False<br/>
					<input type='radio' name='trueFalsetype' value='ab'>A / B<br/>
					<input type='radio' name='trueFalsetype' value='yesno'>Yes / No<br/>
					<input type='radio' name='trueFalsetype' value='custom'>Custom<br/>
					<input type='text' name='trueFalsecustom1'>&nbsp;/&nbsp;
					<input type='text' name='trueFalsecustom2'><br/>
					</p>
		";

		// Check the "everyQuestion" checkbox if it was checked already.
		echo "<p><input type='checkbox' name='everyQuestion' value='everyQuestion'";

		if ($everyQuestion == "everyQuestion")
		{
			echo " checked";
		}

		echo ">Every question is of this type</p>";

		echo "
					<p>
					<input type='submit' value='Create Question'>
					&nbsp;&nbsp;
					<input type='button' value='Cancel'>
					</p>
					<input type='hidden' name='createdQuestion' value='trueFalse'>
					<input type='hidden' name='surveyName' value='$surveyName'>
					<input type='hidden' name='surveyId' value='$surveyId'>
					<input type='hidden' name='createType' value='trueFalse'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";
	}

	// ----------------------------------------------------------------------
	// Create a multiple choice question.
	// ----------------------------------------------------------------------
	function createMultipleChoice($surveyId, $surveyName, $questionNumber, $everyQuestion)
	{
					echo "In createMultipleChoice...<br/>";
					echo "surveyId = $surveyId<br/>";
					echo "surveyName = $surveyName<br/>";
					echo "questionNumber = $questionNumber<br/>";
					echo "everyQuestion = $everyQuestion<br/>";

		echo "
			<div id='multipleChoice'>
				<form action='addMultipleChoiceQuestion.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create Multiple Choice Question</p>
					<p>Please enter the question:</p>
					<p><textarea name='questionText' rows='3' cols='30'></textarea></p>
					<p>
		";

		// Answer 1 and 2
		echo "How many answers?";

		echo "<select id='mChoice' name='mChoice' required='required'>";

		for ($i = 1; $i <= MAX_ANSWERS; $i++)
		{
			echo "<option value='$i'>$i</option>";
		}

		echo "</select><br/>";
		echo "<p><input type='button' value='Create Answers' onclick='loadMultipleChoice();'></p>";

		echo "<div id='mcAnswers'></div>";
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
						<input type='submit' value='Create Question'>
						&nbsp;&nbsp;
						<input type='button' value='Cancel'>
					</p>
					<input type='hidden' name='createdQuestion' value='multipleChoice'>
					<input type='hidden' name='surveyName' value='$surveyName'>
					<input type='hidden' name='surveyId' value='$surveyId'>
					<input type='hidden' name='createType' value='multipleChoice'>
					<input type='text' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";
	}

	// ----------------------------------------------------------------------
	// Create a several answer question.
	// ----------------------------------------------------------------------
	function createSeveralAnswer($surveyId, $surveyName, $questionNumber, $everyQuestion)
	{
					echo "In createSeveralAnswer...<br/>";
					echo "surveyId = $surveyId<br/>";
					echo "surveyName = $surveyName<br/>";
					echo "questionNumber = $questionNumber<br/>";
					echo "everyQuestion = $everyQuestion<br/>";

		echo "
			<div id='severalAnswer'>
				<form action='addSeveralAnswerQuestion.php' method='POST'>
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
						<input type='submit' value='Create Question'>
						&nbsp;&nbsp;
						<input type='button' value='Cancel'>
					</p>
					<input type='hidden' name='createdQuestion' value='severalAnswer'>
					<input type='hidden' name='surveyName' value='$surveyName'>
					<input type='hidden' name='surveyId' value='$surveyId'>
					<input type='hidden' name='createType' value='severalAnswer'>
					<input type='text' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";
	}

	// ----------------------------------------------------------------------
	// Create a free form text question. 
	// ----------------------------------------------------------------------
	function createFreeFormText($surveyId, $surveyName, $questionNumber, $everyQuestion)
	{
					echo "In createFreeFormText...<br/>";
					echo "surveyId = $surveyId<br/>";
					echo "surveyName = $surveyName<br/>";
					echo "questionNumber = $questionNumber<br/>";
					echo "everyQuestion = $everyQuestion<br/>";

		echo "
			<div id='freeFormText'>
				<form action='addFreeFormQuestion.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create Free Form Question</p>
					<p>Please enter the question:</p>
					<p><textarea name='questionText' rows='3' cols='30'></textarea></p>
		";

		// Check the "everyQuestion" checkbox if it was checked already.
		echo "<p><input type='checkbox' name='everyQuestion' value='everyQuestion'";

		if ($everyQuestion == "everyQuestion")
		{
			echo " checked";
		}

		echo ">Every question is of this type</p>";

		echo "
					<p>
						<input type='submit' value='Create Question'>
						&nbsp;&nbsp;
						<input type='button' value='Cancel'>
					</p>
					<input type='hidden' name='createdQuestion' value='freeForm'>
					<input type='hidden' name='surveyId' value='$surveyId'>
					<input type='hidden' name='surveyName' value='$surveyName'>
					<input type='hidden' name='createType' value='freeForm'>
					<input type='text' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";
	}

	// ----------------------------------------------------------------------
	// Create a rating question.
	// ----------------------------------------------------------------------
	function createRating($surveyId, $surveyName, $questionNumber, $everyQuestion)
	{
					echo "In createRating...<br/>";
					echo "surveyId = $surveyId<br/>";
					echo "surveyName = $surveyName<br/>";
					echo "questionNumber = $questionNumber<br/>";
					echo "everyQuestion = $everyQuestion<br/>";

		echo "
			<div id='rating'>
				<form action='addRatingQuestion.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create rating Question</p>
					<p>Please enter the question:</p>
					<p><textarea name='questionText' rows='3' cols='30'></textarea></p>

					<p>
						rating from 1 to 
						<select id='ratingValue' name='ratingValue' required='required'>
		";

		for ($i = MAX_RATING; $i >= 1; $i--)
		{
			echo "<option value='$i'>$i</option>";
		}

		echo "
						</select>
						<br/>
						
						<p><input type='button' value='Add Rating Descriptions' onclick='addRatingDescriptions();'></p>
						<div id='ratingDescriptions'></div>
					</p>
					<p>
						Enter the word to describe the lowest rating:
						<input type='text' name='ratingLowValue'>
					</p>

					<p>
						Enter the word to describe the highest rating:
						<input type='text' name='ratingHighValue'>
					</p>
		";

		// Check the "everyQuestion" checkbox if it was checked already.
		echo "<p><input type='checkbox' name='everyQuestion' value='everyQuestion'";

		if ($everyQuestion == "everyQuestion")
		{
			echo " checked";
		}

		echo ">Every question is of this type</p>";

		echo "
					<p>
						<input type='submit' value='Create Question'>
						&nbsp;&nbsp;
						<input type='button' value='Cancel'>
					</p>
					<input type='hidden' name='createdQuestion' value='rating'>
					<input type='hidden' name='surveyName' value='$surveyName'>
					<input type='hidden' name='surveyId' value='$surveyId'>
					<input type='hidden' name='createType' value='rating'>
					<input type='text' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";
	}

	// ----------------------------------------------------------------------
	// Display the questions we have already created.
	// ----------------------------------------------------------------------
	function displayQuestions($surveyId)
	{
		include("displaysurvey.php");
		drawQuestions($surveyId);
	}

	// ----------------------------------------------------------------------
	// Choose an existing question from a list.
	// ----------------------------------------------------------------------
	function existingQuestionsList()
	{
		echo "
			<div id='existingquestions'>
				<table border='1'>
					<tr>
						<th>Select</th>
						<th>Type</th>
						<th>Question</th>
					</tr>
					<tr>
						<td><input type='checkbox' name='select' value='selected'></td>
						<td>multiple choice</td>
						<td>What is the airspeed velocity of a migrating swallow?</td>
					</tr>
					<tr>
						<td><input type='checkbox' name='select' value='selected'></td>
						<td>free form</td>
						<td>What is the meaning of life?</td>
					</tr>
				</table>
			</div>
		";
	}

	// ----------------------------------------------------------------------
	// Make a session variable of the surveyId we are in.
	// ----------------------------------------------------------------------
	function getSurveyId($surveyName, $districtId)
	{
		try
		{
			// Connect to the databasse.
			include("connectToDB.php");

			$surveyIdRS = $conn->query("SELECT surveyId FROM survey WHERE surveyName = '$surveyName' AND districtId = '$districtId';");

			if ($surveyIdRS === false)
			{
				trigger_error('A problem has occurred getting the surveyId: '.$conn->error, E_USER_ERROR);
			}
			else
			{
				$arr = $surveyIdRS->fetch_array(MYSQLI_ASSOC);
				$surveyId = $arr['surveyId'];

				echo "Just read the survey id and it is $surveyId</br>";

				// Close the connection.
				$conn->close();

				return $surveyId;
			}			
		}
		catch(Exception $e)
		{
			trigger_error($e, E_USER_ERROR);
		}
	}
?>
