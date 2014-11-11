<?php
	// Create the survey

	// Need a couple of checkbox that say:
	// 1) Add a message at the top.
	// 2) Add rating headings - For a rating question.
	// We need another question type which is a rating grid...like Sandy wants for DevPoll.

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

							var mcAnswersDiv = document.getElementById('mcAnswersDiv');
							mcAnswersDiv.innerHTML = returnedData;
						}
					}
				}

				var answers = document.getElementById('mChoice').value;

				XMLHttpRequestObject.send('answers=' + answers);
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

							var saAnswersDiv = document.getElementById('saAnswersDiv');
							saAnswersDiv.innerHTML = returnedData;
						}
					}
				}

				var answers = document.getElementById('severalAnswers').value;

				XMLHttpRequestObject.send('answers=' + answers);
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

							var raDescriptions = document.getElementById('raDescriptionsDiv');
							raDescriptions.innerHTML = returnedData;
						}
					}
				}

				var descriptions = document.getElementById('raHighValue').value;

				XMLHttpRequestObject.send('descriptions=' + descriptions);
			}

			//************************************************************
			//	Add a True/False question
			//************************************************************
			function addTrueFalse()
			{
				if (XMLHttpRequestObject)
				{
					XMLHttpRequestObject.open("POST", "addTrueFalseQuestion.php");

					XMLHttpRequestObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

					XMLHttpRequestObject.onreadystatechange = function()
					{
						if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200)
						{
							// Put the response in the True/False div.
							var returnedData = XMLHttpRequestObject.responseText;

							var tfDiv = document.getElementById('tfDiv');
							tfDiv.innerHTML = returnedData;

							if (returnedData == '<p>Success</p>')
							{
								// Submit the True/False form - which loops back to createsurvey.php.
								document.getElementById("tfForm").submit();
							}
 						}
					}
				}

				// Get the values for the True/False question.
				var sId = document.getElementById('tfSurveyId').value;
				var quNo = document.getElementById('tfQuestionNumber').value;
				var quType = document.getElementById('tfCreateType').value;
				var quText = document.getElementById('tfQuestionText').value;
				var tfType = '';

				// Get the answer type.
				if (document.getElementById('tfAB').checked)
				{
					tfType = 'ab';
				}
				else if (document.getElementById('tfYesNo').checked)
				{
					tfType = 'yesno';
				}
				else if (document.getElementById('tfCustom').checked)
				{
					tfType = 'custom';
				}
				else
				{
					tfType = 'tfTrueFalse';
				}

				var tfCustom1 = document.getElementById('tfCustom1').value;
				var tfCustom2 = document.getElementById('tfCustom2').value;

				// Submit the AJAX request.
				XMLHttpRequestObject.send("sId=" + sId + "&quNo=" + quNo + "&quType=" + quType +
											"&quText=" + quText + "&tfType=" + tfType +
											"&tfCustom1=" + tfCustom1 + "&tfCustom2=" + tfCustom2);
			}

			//************************************************************
			//	Add a Multiple Choice question
			//************************************************************
			function addMultipleChoice()
			{
				if (XMLHttpRequestObject)
				{
					XMLHttpRequestObject.open("POST", "addMultipleChoiceQuestion.php");

					XMLHttpRequestObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

					XMLHttpRequestObject.onreadystatechange = function()
					{
						if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200)
						{
							// Put the response in the Multiple Choi div.
							var returnedData = XMLHttpRequestObject.responseText;

							var mcDiv = document.getElementById('mcDiv');
							mcDiv.innerHTML = returnedData;

							if (returnedData == '<p>Success</p>')
							{
								// Submit the Multiple Choice form - which loops back to createsurvey.php.
								document.getElementById("mcForm").submit();
							}
 						}
					}
				}

				// Get the values for the Multiple Choice question.
				var sId = document.getElementById('mcSurveyId').value;
				var quNo = document.getElementById('mcQuestionNumber').value;
				var quType = document.getElementById('mcCreateType').value;
				var quText = document.getElementById('mcQuestionText').value;
				var ansCount = document.getElementById('mcAnswerCount').value;
				var ajaxSend = "sId=" + sId + "&quNo=" + quNo + "&quType=" + quType + "&quText=" + quText + "&ansCount=" + ansCount;

				// Add the answers
				var i = 1;
				var answer = '';
				var answerNumber = '';

				while (i <= ansCount)
				{
					answerNumber = 'mcanswer' + i;

					answer = document.getElementById(answerNumber).value;

					ajaxSend = ajaxSend + "&mcanswer" + i + "=" + answer;

					i = i + 1;
				}

				// Submit the AJAX request.
				XMLHttpRequestObject.send(ajaxSend);
			}

			//************************************************************
			//	Add a Several Answer question
			//************************************************************
			function addSeveralAnswer()
			{
				if (XMLHttpRequestObject)
				{
					XMLHttpRequestObject.open("POST", "addSeveralAnswerQuestion.php");

					XMLHttpRequestObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

					XMLHttpRequestObject.onreadystatechange = function()
					{
						if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200)
						{
							// Put the response in the Several Answer div.
							var returnedData = XMLHttpRequestObject.responseText;

							var saDiv = document.getElementById('saDiv');
							saDiv.innerHTML = returnedData;

							if (returnedData == '<p>Success</p>')
							{
								// Submit the Several Answer form - which loops back to createsurvey.php.
								document.getElementById("saForm").submit();
							}
 						}
					}
				}

				// Get the values for the Several Answer question.
				var sId = document.getElementById('saSurveyId').value;
				var quNo = document.getElementById('saQuestionNumber').value;
				var quType = document.getElementById('saCreateType').value;
				var quText = document.getElementById('saQuestionText').value;
				var ansCount = document.getElementById('saAnswerCount').value;
				var ajaxSend = "sId=" + sId + "&quNo=" + quNo + "&quType=" + quType + "&quText=" + quText + "&ansCount=" + ansCount;

				// Add the answers
				var i = 1;
				var answer = '';
				var answerNumber = '';

				while (i <= ansCount)
				{
					answerNumber = 'saanswer' + i;

					answer = document.getElementById(answerNumber).value;

					ajaxSend = ajaxSend + "&saanswer" + i + "=" + answer;

					i = i + 1;
				}

				// Submit the AJAX request.
				XMLHttpRequestObject.send(ajaxSend);
			}

			//************************************************************
			//	Add a Free Form question
			//************************************************************
			function addFreeForm()
			{
				if (XMLHttpRequestObject)
				{
					XMLHttpRequestObject.open("POST", "addFreeFormQuestion.php");

					XMLHttpRequestObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

					XMLHttpRequestObject.onreadystatechange = function()
					{
						if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200)
						{
							// Put the response in the Free Form div.
							var returnedData = XMLHttpRequestObject.responseText;

							var ffDiv = document.getElementById('ffDiv');
							ffDiv.innerHTML = returnedData;

							if (returnedData == '<p>Success</p>')
							{
								// Submit the Free Form form - which loops back to createsurvey.php.
								document.getElementById("ffForm").submit();
							}
 						}
					}
				}

				// Get the values for the Free Form question.
				var sId = document.getElementById('ffSurveyId').value;
				var quNo = document.getElementById('ffQuestionNumber').value;
				var quType = document.getElementById('ffCreateType').value;
				var quText = document.getElementById('ffQuestionText').value;

				// Submit the AJAX request.
				XMLHttpRequestObject.send("sId=" + sId + "&quNo=" + quNo + "&quType=" + quType + "&quText=" + quText);
			}

			//************************************************************
			//	Add a Rating question
			//************************************************************
			function addRatingQuestion()
			{
				if (XMLHttpRequestObject)
				{
					XMLHttpRequestObject.open("POST", "addRatingQuestion.php");

					XMLHttpRequestObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

					XMLHttpRequestObject.onreadystatechange = function()
					{
						if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200)
						{
							// Put the response in the Rating div.
							var returnedData = XMLHttpRequestObject.responseText;

							var raDiv = document.getElementById('raDiv');
							raDiv.innerHTML = returnedData;

							if (returnedData == '<p>Success</p>')
							{
								// Submit the Rating form - which loops back to createsurvey.php.
								document.getElementById("raForm").submit();
							}
 						}
					}
				}

				alert ("1 - Start getting data - About to add raSurveyId");

				// Get the values for the Multiple Choice question.
				var sId = document.getElementById('raSurveyId').value;
				var quNo = document.getElementById('raQuestionNumber').value;
				var quType = document.getElementById('raCreateType').value;
				var quText = document.getElementById('raQuestionText').value;
				var descCount = document.getElementById('raDescCount').value;

				alert("sId = " + sId);
				alert("quNo = " + quNo);
				alert("quType = " + quType);
				alert("quText = " + quText);
				alert("descCount = " + descCount);

				var ajaxSend = "sId=" + sId + "&quNo=" + quNo + "&quType=" + quType + "&quText=" + quText + "&descCount=" + descCount;

				alert("ajexSend = " + ajaxSend);

				// Add the answers
				var i = 1;
				var description = '';
				var raDescription = '';

				while (i <= descCount)
				{
					raDescription = 'radescription' + i;
					description = document.getElementById(raDescription).value;
					ajaxSend = ajaxSend + "&radesc" + i + "=" + description;

					i = i + 1;
				}

				alert("AJAX send = " + ajaxSend);

				// Submit the AJAX request.
				XMLHttpRequestObject.send(ajaxSend);
			}

			//***************************************
			//	Launch the includequestions page
			//***************************************
			function includeQuestions(surveyId)
			{
				// Launch the Include Questions page.
				window.location = "includequestions.php?si=" + surveyId + "&rp=99112";
			}

			//*******************************
			//	Stop creating the survey
			//*******************************
			function exitCreateSurvey()
			{
				window.location = "exitCreateSurvey.php";
			}
		</script>
	</head>
	<body>
		<?php
			// The page calls itself when the form is submitted.
			if ($_SERVER["REQUEST_METHOD"] == "POST")
			{
				//   ***********************************************************
				//  *************************************************************
				// **** WE NEED TO REPLACE districtId WITH A SESSION VARIABLE ****
				//  *************************************************************
				//   ***********************************************************
				$districtId = 1;

				// If we are entering the name of the survey, this will be populated.
				$enterName = mysql_real_escape_string($_POST['enterName']);

				// The other values are submitted by the form.
				$surveyName = mysql_real_escape_string($_POST['surveyName']);
				$surveyId = mysql_real_escape_string($_POST['surveyId']);

				$questionNumber = mysql_real_escape_string($_POST['questionNumber']);

				// See if every question is of the same type.
				$everyQuestion = mysql_real_escape_string($_POST['everyQuestion']);

				// The checkbox has been checked, so every question is of the same type.
				$createType = mysql_real_escape_string($_POST['createType']);

				// Check what type of question was chosen and load that box.
				$questionType = mysql_real_escape_string($_POST['questionType']);

				$valuesPassed = true;
			}

//*******************
//*******************
//*******************
//*******************
// Need to add isset to check that the values are entered correctly.
//if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//    if (!isset($_POST['name'])) {
//        // at this point you know that `name` was not passed as part of the request
//        // this could be treated as an error
//    }
//}

//if ( isset ($_POST['fname']{0}) and isset( $_POST['lname']{0}) and isset( $_POST['mail']{0}) ){
//   // Insert into db
//}
//else{
//   echo "Please fill all the feilds";
//}
//What happens here is even if the user didnt enter any value into the fname feild, still the $_POST['fname'] will be set. So the isset ($_POST['fname']) will always return true if the form was submitted.

//But when you check for isset ($_POST['fname']{0}) you are making sure that atleast one charater is entered and the feild is not empty. you can also use an is_empty but this is much better way.

//Also The catch in using this is "{}" are going to be removed in php version 6. so if you are planning to upgrade your servers in the future then this might cause a small problem. But using "[]" instead of "{}" will solve that problem in php version 6.				// ----------------------------------------------------------------------

//why not use isset in combination with empty? isset(...) && !empty(..)

//*******************
//*******************
//*******************
//*******************
//*******************

			// The form was submitted and values were passed to it.
			if ($valuesPassed == true)
			{
				// Are we entering the name for the first time.
				if ($enterName == ENTER_NAME)
				{
					$canEnterQuestions = false;

					// Connect to the database.
					include("connectToDB.php");

					// Check if the name is taken for this district.
					$result = $conn->query("SELECT surveyName FROM survey WHERE districtId = $districtId");

					// Find the name of the survey we entered.
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

					// If the name has not been found add it to the database.
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
					// The survey name is valid, and we can enter a question.
					if (empty($surveyId) or is_null($surveyId))
					{
						// Get the survey id for the name entered.
						$surveyId = getSurveyId($surveyName, $districtId);
					}

					$canEnterQuestions = true;
				}

				// Create the question of the selected type.
				if ($canEnterQuestions == true)
				{
					// Check that the "every question" checkbox was checked.
					if ($everyQuestion == EVERY_QUESTION && $createType != SELECT_TYPE)
					{
						$questionNumber++;

						// Every question is of the same type.
						$questionType = $createType;
					}

					// What question type was chosen.
					if ($questionType == TRUE_FALSE)
					{
						createTrueFalse($surveyId, $surveyName, $questionNumber, $everyQuestion);
					}
					elseif ($questionType == MULTIPLE_CHOICE)
					{
						createMultipleChoice($surveyId, $surveyName, $questionNumber, $everyQuestion);
					}
					elseif ($questionType == SEVERAL_ANSWER)
					{
						createSeveralAnswer($surveyId, $surveyName, $questionNumber, $everyQuestion);
					}
					elseif ($questionType == FREE_FORM)
					{
						createFreeFormText($surveyId, $surveyName, $questionNumber, $everyQuestion);
					}
					elseif ($questionType == RATING)
					{
						createRating($surveyId, $surveyName, $questionNumber, $everyQuestion);
					}
					else
					{
						// Next question number.
						$questionNumber++;

						// Display the create question selection page.
						createQuestionDiv($surveyId, $surveyName, $questionNumber, $everyQuestion);
					}
				}
			}
			else
			{
				// No survey is active - create a new survey.
				enterSurveyName();
			}
		?>
	</body>
</html>

<?php
	//******************************************
	//	Display the question type selection
	//******************************************
	function createQuestionDiv($surveyId, $surveyName, $questionNumber, $everyQuestion)
	{
		echo "
			<div id='questions'>
				<form action='createsurvey.php' method='POST'>
					<p>Survey $surveyName</p>
					<p>Question Number: $questionNumber</p>
					<p>Please select the type of question:</p>
					<p><input type='radio' name='questionType' value='".TRUE_FALSE."'>True / False</p>
					<p><input type='radio' name='questionType' value='".MULTIPLE_CHOICE."'>Multiple Choice<br/>
					<p><input type='radio' name='questionType' value='".SEVERAL_ANSWER."'>Several Answers</p>
					<p><input type='radio' name='questionType' value='".FREE_FORM."'>Free Form Text</p>
					<p><input type='radio' name='questionType' value='".RATING."'>Rating</p>
		";

		echo "<p><input type='checkbox' name='everyQuestion' value='".EVERY_QUESTION."'";

		if ($everyQuestion == EVERY_QUESTION)
		{
			echo " checked";
		}

		echo ">Every question is of this type</p>";

		echo "
					<p><input type='submit' value='Create Question'></p>
					<p><input type='button' value='Include Existing Question' onclick='includeQuestions($surveyId);'></p>
					<p><input type='button' value='Start Over' onclick='startOver();'></p>
					<p><input type='button' value='Stop entering questions' onclick='exitCreateSurvey();'></p>
					<input type='hidden' name='surveyName' value='$surveyName'>
					<input type='hidden' name='surveyId' value='$surveyId'>
					<input type='hidden' name='createType' value='".SELECT_TYPE."'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";

		echo "About to display the questions<br/>";
		displayQuestions($surveyId, $surveyName);
	}

	//******************************************
	//	Add the survey name to the Database
	//******************************************/
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

		$stmt -> close();
	}

	//**********************************
	//	Enter the name of the survey
	//**********************************
	function enterSurveyName()
	{
		echo "
			<div id='surveyNamepart'>
				<form action='createsurvey.php' method='POST'>
					<label>Survey Name: </label><input type='text' name='surveyName'>
					<br/>
					<input type='submit' value='Create Survey'>
					<input type='hidden' name='enterName' value='".ENTER_NAME."'>
				</form>
			</div>
		";
	}

	//***********************************
	//	Create a trueFalse question
	//***********************************
	function createTrueFalse($surveyId, $surveyName, $questionNumber, $everyQuestion)
	{
		echo "
			<div id='trueFalse'>
				<form id='tfForm' action='createsurvey.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create True / False Question</p>
					<p>Please enter the question:</p>
					<p><textarea id='tfQuestionText' name='questionText' rows='3' cols='30' required></textarea></p>
					<p>Answer type:</p>
					<p>
					<input type='radio' id='tfTrueFalse' name='tfType' value='".TRUE_FALSE."'>True / False<br/>
					<input type='radio' id='tfAB' name='tfType' value='ab'>A / B<br/>
					<input type='radio' id='tfYesNo' name='tfType' value='yesno'>Yes / No<br/>
					<input type='radio' id='tfCustom' name='tfType' value='custom'>Custom<br/>
					<input type='text' id='tfCustom1' name='tfCustom1'>&nbsp;/&nbsp;
					<input type='text' id='tfCustom2' name='tfCustom2'><br/>
					</p>
		";

		echo "<p>";
		echo "<div id='tfDiv'></div>";
		echo "</p>";

		// Check the "everyQuestion" checkbox if it was checked already.
		echo "<p><input type='checkbox' name='everyQuestion' value='".EVERY_QUESTION."'";

		if ($everyQuestion == EVERY_QUESTION)
		{
			echo " checked";
		}

		echo ">Every question is of this type</p>";

		echo "
					<p>
					<input type='button' value='Create Question' onclick='addTrueFalse();'>
					&nbsp;&nbsp;
					<input type='button' value='Cancel'>
					</p>
					<input type='hidden' id='tfCreatedQuestion' name='createdQuestion' value='".TRUE_FALSE."'>
					<input type='hidden' id='tfSurveyName' name='surveyName' value='$surveyName'>
					<input type='hidden' id='tfSurveyId' name='surveyId' value='$surveyId'>
					<input type='hidden' id='tfCreateType' name='createType' value='".TRUE_FALSE."'>
					<input type='hidden' id='tfQuestionNumber' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";
	}

	//***************************************
	//	Create a multiple choice question
	//***************************************
	function createMultipleChoice($surveyId, $surveyName, $questionNumber, $everyQuestion)
	{
		echo "
			<div id='multipleChoice'>
				<form id='mcForm' action='createsurvey.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create Multiple Choice Question</p>
					<p>Please enter the question:</p>
					<p><textarea id='mcQuestionText' name='questionText' rows='3' cols='30'></textarea></p>
					<p>
		";

		// Create the drop down box to hold the number of answers we want.
		echo "How many answers?";

		echo "<select id='mChoice' name='mChoice' required='required'>";

		for ($i = 1; $i <= MAX_ANSWERS; $i++)
		{
			echo "<option value='$i'>$i</option>";
		}

		echo "</select><br/>";
		echo "<p><input type='button' value='Create Answers' onclick='loadMultipleChoice();'></p>";

		// div to hold the results of the AJAX call.
		echo "<p>";
		echo "<div id='mcAnswersDiv'></div>";
		echo "</p>";
		echo "<p>";
		echo "<div id='mcDiv'></div>";
		echo "</p>";

		// Check the "everyQuestion" checkbox if it was checked already.
		echo "<p><input type='checkbox' name='everyQuestion' value='".EVERY_QUESTION."'";

		if ($everyQuestion == EVERY_QUESTION)
		{
			echo " checked";
		}

		echo ">Every question is of this type</p>";

		echo "
					<input type='hidden' id='mcCreatedQuestion' name='createdQuestion' value='".MULTIPLE_CHOICE."'>
					<input type='hidden' id='mcSurveyName' name='surveyName' value='$surveyName'>
					<input type='hidden' id='mcSurveyId' name='surveyId' value='$surveyId'>
					<input type='hidden' id='mcCreateType' name='createType' value='".MULTIPLE_CHOICE."'>
					<input type='hidden' id='mcQuestionNumber' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";
	}

	//***************************************
	//	Create a several answer question
	//***************************************
	function createSeveralAnswer($surveyId, $surveyName, $questionNumber, $everyQuestion)
	{
		echo "
			<div id='severalAnswer'>
				<form id='saForm' action='createsurvey.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create Several Answer Question</p>
					<p>Please enter the question:</p>
					<p><textarea id='saQuestionText' name='questionText' rows='3' cols='30'></textarea></p>
		";

		// Create the drop down box to hold the number of answers we want.
		echo "<p>How many answers?";

		echo "<select id='severalAnswers' name='severalAnswers' required='required'>";

		for ($i = 1; $i <= MAX_ANSWERS; $i++)
		{
			echo "<option value='$i'>$i</option>";
		}

		echo "</select></p><br/>";
		echo "<p><input type='button' value='Create Answers' onclick='loadSeveralAnswers();'></p>";

		// div to hold the results of the AJAX call.
		echo "<p>";
		echo "<div id='saAnswersDiv'></div>";
		echo "</p>";
		echo "<p>";
		echo "<div id='saDiv'></div>";
		echo "</p>";

		// Check the "everyQuestion" checkbox if it was checked already.
		echo "<p><input type='checkbox' name='everyQuestion' value='".EVERY_QUESTION."'";

		if ($everyQuestion == EVERY_QUESTION)
		{
			echo " checked";
		}

		echo ">Every question is of this type</p>";

		echo "
					<input type='hidden' id='saCreatedQuestion' name='createdQuestion' value='".SEVERAL_ANSWER."'>
					<input type='hidden' id='saSurveyName' name='surveyName' value='$surveyName'>
					<input type='hidden' id='saSurveyId' name='surveyId' value='$surveyId'>
					<input type='hidden' id='saCreateType' name='createType' value='".SEVERAL_ANSWER."'>
					<input type='hidden' id='saQuestionNumber' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";
	}

	//***************************************
	//	Create a free form text question
	//***************************************
	function createFreeFormText($surveyId, $surveyName, $questionNumber, $everyQuestion)
	{
		echo "
			<div id='freeFormText'>
				<form id='ffForm' action='createsurvey.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create Free Form Question</p>
					<p>Please enter the question:</p>
					<p><textarea id='ffQuestionText' name='questionText' rows='3' cols='30'></textarea></p>
		";

		echo "<p>";
		echo "<div id='ffDiv'></div>";
		echo "</p>";

		// Check the "everyQuestion" checkbox if it was checked already.
		echo "<p><input type='checkbox' name='everyQuestion' value='".EVERY_QUESTION."'";

		if ($everyQuestion == EVERY_QUESTION)
		{
			echo " checked";
		}

		echo ">Every question is of this type</p>";

		echo "
					<p>
						<input type='button' value='Create Question' onclick='addFreeForm();'>
						&nbsp;&nbsp;
						<input type='button' value='Cancel'>
					</p>
					<input type='hidden' id='ffCreatedQuestion' name='createdQuestion' value='".FREE_FORM."'>
					<input type='hidden' id='ffSurveyId' name='surveyId' value='$surveyId'>
					<input type='hidden' id='ffCreateType' name='createType' value='".FREE_FORM."'>
					<input type='hidden' id='ffQuestionNumber' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";
	}

	//*******************************
	//	Create a rating question
	//*******************************
	function createRating($surveyId, $surveyName, $questionNumber, $everyQuestion)
	{
		echo "
			<div id='rating'>
				<form id='raForm' action='addRatingQuestion.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create rating Question</p>
					<p>Please enter the question:</p>
					<p><textarea id='raQuestionText' name='questionText' rows='3' cols='30'></textarea></p>
		";

		echo "<p>Rating from 1 to";

		echo "<select id='raHighValue' name='ratingValue' required='required'>";

		// Create the drop down box to hold the number of ratings we want.
		for ($i = MAX_RATING; $i >= 1; $i--)
		{
			echo "<option value='$i'>$i</option>";
		}

		echo "</select></p><br/>";
		echo "<p><input type='button' value='Add Rating Descriptions' onclick='addRatingDescriptions();'></p>";

		// div to hold the results of the AJAX call.
		echo "<p>";
		echo "<div id='raDescriptionsDiv'></div>";
		echo "</p>";
		echo "<p>";
		echo "<div id='raDiv'></div>";
		echo "</p>";

		// Check the "everyQuestion" checkbox if it was checked already.
		echo "<p><input type='checkbox' name='everyQuestion' value='".EVERY_QUESTION."'";

		if ($everyQuestion == EVERY_QUESTION)
		{
			echo " checked";
		}

		echo ">Every question is of this type</p>";

		echo "
					<p>
						<input type='button' value='Create Question' onclick='addRatingQuestion();'>
						&nbsp;&nbsp;
						<input type='button' value='Cancel'>
					</p>
					<input type='hidden' id='raCreatedQustion' name='createdQuestion' value='".RATING."'>
					<input type='hidden' id='raSurveyId' name='surveyId' value='$surveyId'>
					<input type='hidden' id='raCreateType' name='createType' value='".RATING."'>
					<input type='hidden' id='raQuestionNumber' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";
	}

	//***************************************************
	//	Display the questions we have already created
	//***************************************************
	function displayQuestions($surveyId, $surveyName)
	{
		include("displaysurvey.php");
		drawQuestions($surveyId, $surveyName);
	}

	//***************************************************
	//	Get the survey id for the given survey name
	//***************************************************
	function getSurveyId($surveyName, $districtId)
	{
		try
		{
			// Connect to the databasse.
			include("connectToDB.php");

			// Make sure the email address is in the database.
			$sql = "SELECT surveyId
					FROM survey s 
					WHERE s.surveyname = ? 
					AND s.districtid = ?";

			$statement = $conn->prepare($sql);
			$statement->bind_param('si', $surveyName, $districtId);

			$statement->execute();

			$statement->bind_result($returned_surveyId);

			// Obtain a resultset from the query.
			$statement->fetch();

			$statement->free_result();

			return $returned_surveyId;
		}
		catch(Exception $e)
		{
			trigger_error($e, E_USER_ERROR);
		}
	}

	//**************************************************************
	//	Get the maximum question number for the given survey id,
	//	so we can add any new questions to the end of the survey
	//**************************************************************
	function getMaxQuestionNumber($surveyId)
	{
		// Connect to the database.
		require("connectToDB.php");

		$numberQuery = "SELECT 	max(questionnumber) as questionnumber
						FROM  devpoll.questions
						WHERE surveyid = $surveyId;";

		// Get the questions and answers for this survey.
		$maxRS = $conn->query($numberQuery);

		$maxArray = $maxRS->fetch_array(MYSQLI_ASSOC);
		$max = $maxArray['questionnumber'];

		// If there are no questions - set max question to zero.
		if ($max == null)
		{
			$max = 0;
		}

		// Close the connection.
		$conn->close();

		// Return the result.
		return $max;

	}

	//**************************************************
	//	Get the survey name for the given survey id
	//**************************************************
	function getSurveyName($surveyId)
	{
		// Connect to the database.
		require("connectToDB.php");

		$nameQuery = "SELECT  surveyname
						FROM  devpoll.survey
						WHERE surveyid = $surveyId;";

		// Get the questions and answers for this survey.
		$nameRS = $conn->query($nameQuery);

		$nameArray = $nameRS->fetch_array(MYSQLI_ASSOC);
		$name = $nameArray['surveyname'];

		// Close the connection.
		$conn->close();

		// Return the result.
		return $name;

	}
?>
