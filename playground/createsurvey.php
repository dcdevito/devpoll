<?php
	// Make sure the person is logged in.
	include("verifylogin.php");
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

							var mcanswersDiv = document.getElementById('mcanswers');
							mcanswersDiv.innerHTML = returnedData;
						}
					}
				}

				var answers = document.getElementById('mchoice').value;
				
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

							var saanswersDiv = document.getElementById('saanswers');
							saanswersDiv.innerHTML = returnedData;
						}
					}
				}

				var answers = document.getElementById('severalanswers').value;
				
				XMLHttpRequestObject.send('answers=' + answers);
			}
		</script>
	</head>
	<body>
		<?php 
			if ($_SERVER["REQUEST_METHOD"] == "POST")
			{
				//   ***********************************************************
				//  *************************************************************
				// **** WE NEED TO REPLACE districtid WITH A SESSION VARIABLE ****
				//  *************************************************************
				//   ***********************************************************
				$districtid = 1;

				$entername = mysql_real_escape_string($_POST['entername']);
				$surveyname = mysql_real_escape_string($_POST['surveyname']);

				// Are we entering the name for the first time.
				if ($entername == "entername")
				{
					$canenterquestions = false;

					// Connect to the database.
					include("connectToDB.php");

					// Check if the name is taken for this district.
					$result = $conn->query("SELECT surveyname FROM survey WHERE districtid = $districtid");

					$nameOK = true;
					while($row = $result->fetch_assoc()) 
					{
						if ($row['surveyname'] == $surveyname)
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
						$result = addSurveyToDB($surveyname, $districtid);

						$canenterquestions = true;
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
					$canenterquestions = true;

					// Check if we just created a question.
					$createdquestion = mysql_real_escape_string($_POST['createdquestion']);

					// What type of question are we creating.
					if ($createdquestion == 'truefalse')
					{
						$questionnumber = mysql_real_escape_string($_POST['questionNumber']);
						$questiontype = mysql_real_escape_string($_POST['createdquestion']);
						$questiontext = mysql_real_escape_string($_POST['questiontext']);

						// Get the type of headings for the true/false question. 
						$truefalsetype = mysql_real_escape_string($_POST['truefalsetype']);
						switch ($truefalsetype)
						{
							case "ab":
								$truefalseheading1 = 'A';
								$truefalseheading2 = 'B';
								break;
							case "yesno":
								$truefalseheading1 = 'Yes';
								$truefalseheading2 = 'No';
								break;
							case "custom":
								$truefalseheading1 = mysql_real_escape_string($_POST['truefalsecustom1']);
								$truefalseheading2 = mysql_real_escape_string($_POST['truefalsecustom2']);
								break;
							default:
								$truefalseheading1 = 'true';
								$truefalseheading2 = 'false';
								break;
						}

						// Add the truefalse question to the database.
						addTrueFalse($questionnumber, $questiontype, $questiontext, $truefalseheading1, $truefalseheading2);

					}
					elseif ($createdquestion == 'multiplechoice')
					{
						$questionnumber = mysql_real_escape_string($_POST['questionNumber']);
						$questiontype = mysql_real_escape_string($_POST['createdquestion']);
						$questiontext = mysql_real_escape_string($_POST['questiontext']);

						$numberofanswers = mysql_real_escape_string($_POST['numberofanswers']);

						// Loop through the answers and create an array of the values.
						$answers = array();
						for ($i = 1; $i <= $numberofanswers; $i++)
						{
							$answernum = 'mcanswer'.$i;

							$answer = mysql_real_escape_string($_POST[$answernum]);
							$answers[] = $answer;
						}

						// Add the multiple choice question to the database.
						addMultipleChoice($questionnumber, $questiontype, $questiontext, $numberofanswers, $answers);
					}
					elseif ($createdquestion == 'severalanswer')
					{
						$questionnumber = mysql_real_escape_string($_POST['questionNumber']);
						$questiontype = mysql_real_escape_string($_POST['createdquestion']);
						$questiontext = mysql_real_escape_string($_POST['questiontext']);

						$numberofanswers = mysql_real_escape_string($_POST['numberofanswers']);

						// Loop through the answers and create an array of the values.
						$answers = array();
						for ($i = 1; $i <= $numberofanswers; $i++)
						{
							$answernum = 'saanswer'.$i;

							$answer = mysql_real_escape_string($_POST[$answernum]);
							$answers[] = $answer;
						}

						// Add the severalanswer question to the database.
						addSeveralAnswer($questionnumber, $questiontype, $questiontext, $numberofanswers, $answers);
					}
					elseif ($createdquestion == 'freeform')
					{
						$questionnumber = mysql_real_escape_string($_POST['questionNumber']);
						$questiontype = mysql_real_escape_string($_POST['questiontype']);
						$questiontext = mysql_real_escape_string($_POST['questiontext']);

						//addTrueFalse($questionnumber);
					}
					elseif ($createdquestion == 'rating')
					{
						$questionnumber = mysql_real_escape_string($_POST['questionNumber']);
						$questiontype = mysql_real_escape_string($_POST['questiontype']);
						$questiontext = mysql_real_escape_string($_POST['questiontext']);
						
						//addTrueFalse($questionnumber);
					}
				}

				// 
				if ($canenterquestions == true)
				{
					// See if every question is of the same type.
					$everyquestion = mysql_real_escape_string($_POST['everyquestion']);

					// The checkbox has been checked, so every question is of the same type.
					$createtype = mysql_real_escape_string($_POST['createtype']);

					if ($everyquestion == "everyquestion" && $createtype != "selecttype")
					{
						// Every question is of the same type.
						$questiontype = $createtype;
					}
					else
					{
						// Check what type of question was chosen and load that box.
						$questiontype = mysql_real_escape_string($_POST['questiontype']);
					}

					// Get question number.
					$questionNumber = mysql_real_escape_string($_POST['questionNumber']);
					// Next question number.
					$questionNumber++;

					// What question type was chosen.
					if ($questiontype == "truefalse")
					{
						createTrueFalse($surveyname, $questionNumber, $everyquestion);
					}
					elseif ($questiontype == "multiplechoice")
					{
						createMultipleChoice($surveyname, $questionNumber, $everyquestion);	
					}
					elseif ($questiontype == "severalanswer")
					{
						createSeveralAnswer($surveyname, $questionNumber, $everyquestion);
					}
					elseif ($questiontype == "freeform")
					{
						createFreeFormText($surveyname, $questionNumber, $everyquestion);
					}
					elseif ($questiontype == "rating")
					{
						createRating($surveyname, $questionNumber, $everyquestion);
					}
					else
					{
						// Choose a question type.
						$questionNumber = 0; 

						createQuestionDiv($surveyname, $questionNumber, $everyquestion);

						displayQuestions($surveyname);
					}
				}
			}
			else
			{
				enterSurveyName();
			}
		?>
	</body>
</html>

<?php
	// Add the truefalse question to the database.
	function addTrueFalse($questionnumber, $questiontype, $questiontext, $truefalseheading1, $truefalseheading2)
	{
		// Add true false question to DB.
		echo "Inside addTrueFalse<br/><br/>";

		echo "
			Question Number = $questionnumber<br/>
			Question Type = $questiontype<br/>
			Question Text = $questiontext<br/>
			<br/>
			Heading1 = $truefalseheading1<br/>
			Heading2 = $truefalseheading2<br/>
		";
	}

	// Add the severalanswer question to the database.
	function addSeveralAnswer($questionnumber, $questiontype, $questiontext, $numberofanswers, $answers)
	{
		// Add several answers question to DB.
		echo "Inside addSeveralAnswer<br/><br/>";

		echo "
			Question Number = $questionnumber<br/>
			Question Type = $questiontype<br/>
			Question Text = $questiontext<br/>
			<br/>
			Number of Answers = $numberofanswers<br/>
			<br/>
			Answers:
		";

		for ($i = 0; $i < $numberofanswers; $i++)
		{
			$answer = $answers[$i];

			echo "Answer ".($i + 1)." = ".$answer."<br/>";
		}

		echo "
			<br/>
			End of Function.
		";
	}


	// Display the question type selection.
	function createQuestionDiv($surveyname, $questionNumber, $everyquestion)
	{
		echo "
			<div id='questions'>
				<form action='createsurvey.php' method='POST'>
					<p>Survey $surveyname</p>
					<p>Please select the type of question:</p>
					<p><input type='radio' name='questiontype' value='truefalse'>True / False</p>
					<p><input type='radio' name='questiontype' value='multiplechoice'>Multiple Choice<br/>
					<p><input type='radio' name='questiontype' value='severalanswer'>Several Answers</p>
					<p><input type='radio' name='questiontype' value='freeform'>Free Form Text</p>
					<p><input type='radio' name='questiontype' value='rating'>Rating</p>
		";

		echo "<p><input type='checkbox' name='everyquestion' value='everyquestion'";

		if ($everyquestion == "everyquestion")
		{
			echo " checked";
		}

		echo ">Every question is of this type</p>";

		echo "
					<p><input type='submit' value='Create Question'></p>
					<p><input type='button' value='Include Existing Question'></p>
					<p><input type='button' value='Start Over' onclick='startOver();'></p>
					<p><input type='button' value='Exit'></p>
					<input type='hidden' name='surveyname' value='".$surveyname."'>
					<input type='hidden' name='createtype' value='selecttype'>
					<input type='text' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";
	}

	// Add the survey name to the Database.
	function addSurveyToDB($surveyname, $districtid)
	{
		// Connect to the database.
		include("connectToDB.php");

		// Insert the values into the database.
		$query = "INSERT INTO survey(surveyname, districtid, dateopen) VALUES (?, ?, now())"; 

		echo "Query is ".$query."<br/>";
		echo "districtid = ".$districtid."<br/>";
		echo "surveyname = ".$surveyname."<br/>";

		if ($stmt = $conn->prepare($query))
		{
			$stmt->bind_param('si', $surveyname, $districtid);

			if ($stmt->execute())
			{
				echo "Success ".$stmt->insert_id."<br/>";
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

	function enterSurveyName()
	{
		echo "
			<div id='surveynamepart'>
				<form action='createsurvey.php' method='POST'>
					<label>Survey Name: </label><input type='text' name='surveyname'>
					<br/>
					<input type='submit' value='Create Survey'>
					<input type='hidden' name='entername' value='entername'>
				</form>
			</div>
		";		
	}

	// Create a truefalse question.
	function createTrueFalse($surveyname, $questionNumber, $everyquestion)
	{
		echo "
			<div id='truefalse'>
				<form action='createsurvey.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create True / False Question</p>
					<p>Please enter the question:</p>
					<p><textarea name='questiontext' rows='3' cols='30' required></textarea></p>
					<p>Answer type:</p>
					<p>
					<input type='radio' name='truefalsetype' value='truefalse'>True / False<br/>
					<input type='radio' name='truefalsetype' value='ab'>A / B<br/>
					<input type='radio' name='truefalsetype' value='yesno'>Yes / No<br/>
					<input type='radio' name='truefalsetype' value='custom'>Custom<br/>
					<input type='text' id='truefalsecustom1'>&nbsp;/&nbsp;
					<input type='text' id='truefalsecustom2'><br/>
					</p>
		";

		// Check the "everyquestion" checkbox if it was checked already.
		echo "<p><input type='checkbox' name='everyquestion' value='everyquestion'";

		if ($everyquestion == "everyquestion")
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
					<input type='hidden' name='createdquestion' value='truefalse'>
					<input type='hidden' name='surveyname' value='$surveyname'>
					<input type='hidden' name='createtype' value='truefalse'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";      
	}

	// Create a multiple choice question.
	function createMultipleChoice($surveyname, $answers, $questionNumber, $everyquestion)
	{
		echo "
			<div id='multiplechoice'>
				<form action='createsurvey.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create Multiple Choice Question</p>
					<p>Please enter the question:</p>
					<p><textarea name='questiontext' rows='3' cols='30'></textarea></p>
					<p>
		";

		// Answer 1 and 2
		echo "
			How many answers?
		";

		echo "
			<select id='mchoice' name='mchoice' required='required'>
		";

		for ($i = 1; $i <= 20; $i++)
		{
			echo "<option value='$i'>$i</option>";
		}

		echo "</select><br/>";
		echo "<p><input type='button' value='Create Answers' onclick='loadMultipleChoice();'></p>";

		echo "<div id='mcanswers'></div>";
		echo "
			</p>
		";

		// Check the "everyquestion" checkbox if it was checked already.
		echo "<p><input type='checkbox' name='everyquestion' value='everyquestion'";

		if ($everyquestion == "everyquestion")
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
					<input type='hidden' name='createdquestion' value='multiplechoice'>
					<input type='hidden' name='surveyname' value='$surveyname'>
					<input type='hidden' name='createtype' value='multiplechoice'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";
	}

	// Create a several answer question.
	function createSeveralAnswer($surveyname, $questionNumber, $everyquestion)
	{
		echo "
			<div id='severalanswer'>
				<form action='createsurvey.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create Several Answer Question</p>
					<p>Please enter the question:</p>
					<p><textarea name='questiontext' rows='3' cols='30'></textarea></p>
					<p>
		";

		// Answer 1 and 2
		echo "
			How many answers?
		";

		echo "
			<select id='severalanswers' name='severalanswers' required='required'>
		";

		for ($i = 1; $i <= 20; $i++)
		{
			echo "<option value='$i'>$i</option>";
		}

		echo "</select><br/>";
		echo "<p><input type='button' value='Create Answers' onclick='loadSeveralAnswers();'></p>";

		echo "<div id='saanswers'></div>";
		echo "
			</p>
		";

		// Check the "everyquestion" checkbox if it was checked already.
		echo "<p><input type='checkbox' name='everyquestion' value='everyquestion'";

		if ($everyquestion == "everyquestion")
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
					<input type='hidden' name='createdquestion' value='severalanswer'>
					<input type='hidden' name='surveyname' value='$surveyname'>
					<input type='hidden' name='createtype' value='severalanswer'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";
	}

	// Create a free form text question. 
	function createFreeFormText($surveyname, $questionNumber, $everyquestion)
	{
		/***
		echo "
			<div id='freeformtext'>
				<form action='createFreeForm.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create Free Form Question</p>
					<p>Please enter the question:</p>
					<p><textarea name='questiontext' rows='3' cols='30'></textarea></p>
		";

		// Check the "everyquestion" checkbox if it was checked already.
		echo "<p><input type='checkbox' name='everyquestion' value='everyquestion'";

		if ($everyquestion == "everyquestion")
		{
			echo " checked";
		}

		echo ">Every question is of this type</p>";

		echo "
					<p><input type='checkbox' name='everyquestion' value='everyquestion'>Every question is of this type</p>
					<p>
						<input type='submit' value='Create Question'>
						&nbsp;&nbsp;
						<input type='button' value='Cancel'>
					</p>
					<input type='hidden' name='createdquestion' value='freeform'>
					<input type='hidden' name='surveyname' value='$surveyname'>
					<input type='hidden' name='createtype' value='freeform'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";  
		***/
	}

	// Create a rating question.
	function createRating($surveyname, $questionNumber, $everyquestion)
	{
		/***
		echo "
			<div id='rating'>
				<form action='createRating.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create Rating Question</p>
					<p>Please enter the question:</p>
					<p><textarea name='questiontext' rows='3' cols='30'></textarea></p>

					<p>
						Rating from 1 to 
						<select name='values'>
						<option value='10'>10</option>
						<option value='9'>9</option>
						<option value='8'>8</option>
						<option value='7'>7</option>
						<option value='6'>6</option>
						<option value='5'>5</option>
						<option value='4'>4</option>
						<option value='3'>3</option>
						</select>
					</p>

					<p>
						Is 1 the low value?
						<input type='radio' name='rating1low' value='yes'>Yes
						<input type='radio' name='rating1low' value='no'>No
					</p>

					<p>
						Enter the word to describe the lowest rating:
						<input type='text' id='ratinglowvalue'>
					</p>

					<p>
						Enter the word to describe the highest rating:
						<input type='text' id='ratinghighvalue'>
					</p>
		";

		// Check the "everyquestion" checkbox if it was checked already.
		echo "<p><input type='checkbox' name='everyquestion' value='everyquestion'";

		if ($everyquestion == "everyquestion")
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
					<input type='hidden' name='createdquestion' value='rating'>
					<input type='hidden' name='surveyname' value='$surveyname'>
					<input type='hidden' name='createtype' value='rating'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";	
		***/
	}

	// Display the questions we have already created.
	function displayQuestions($surveyname)
	{
		echo "<p>Questions go here</p>";
	}

	// Choose an existing question from a list.
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
?>
