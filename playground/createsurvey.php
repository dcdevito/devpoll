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
				alert("Answers = " + answers);
				
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
					$canenterquestions = true;

					// Check if we just created a question.
					if ($createtruefalse == 'yes')
					{
						$questionnumber = mysql_real_escape_string($_POST['questionNumber']);
						$questiontype = mysql_real_escape_string($_POST['questiontype']);
						$questiontext = mysql_real_escape_string($_POST['questiontext']);

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

						//addTrueFalse($questionnumber, $questiontype, $questiontext, $truefalseheading1, $truefalseheading2);

					}
					elseif ($createmultiplechoice == 'yes')
					{
						$questionnumber = mysql_real_escape_string($_POST['questionNumber']);
						$questiontype = mysql_real_escape_string($_POST['questiontype']);
						$questiontext = mysql_real_escape_string($_POST['questiontext']);

						$answer1 = mysql_real_escape_string($_POST['mcanswer1']);
						$answer2 = mysql_real_escape_string($_POST['mcanswer2']);
						$answer3 = mysql_real_escape_string($_POST['mcanswer3']);
						$answer4 = "";
						$answer5 = "";

						if ($questiontype == 'mcfour')
						{
							$answer4 = mysql_real_escape_string($_POST['mcanswer4']);
						}
						elseif ($questiontype == 'mcfive')
						{
							$answer4 = mysql_real_escape_string($_POST['mcanswer4']);
							$answer5 = mysql_real_escape_string($_POST['mcanswer5']);
						}

						//addMultipleChoice($questionnumber, $questiontype, $questiontext, $answer1, $answer2, $answer3, $answer4, $answer5);
					}
					elseif ($createseveralanswer == 'yes')
					{
						$questionnumber = mysql_real_escape_string($_POST['questionNumber']);
						$questiontype = mysql_real_escape_string($_POST['questiontype']);
						$questiontext = mysql_real_escape_string($_POST['questiontext']);

						//addTrueFalse($questionnumber);
					}
					elseif ($createfreeform == 'yes')
					{
						$questionnumber = mysql_real_escape_string($_POST['questionNumber']);
						$questiontype = mysql_real_escape_string($_POST['questiontype']);
						$questiontext = mysql_real_escape_string($_POST['questiontext']);

						//addTrueFalse($questionnumber);
					}
					elseif ($createrating == 'yes')
					{
						$questionnumber = mysql_real_escape_string($_POST['questionNumber']);
						$questiontype = mysql_real_escape_string($_POST['questiontype']);
						$questiontext = mysql_real_escape_string($_POST['questiontext']);
						
						//addTrueFalse($questionnumber);
					}
				}

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

					if ($questiontype == "truefalse")
					{
						createTrueFalse($surveyname, $questionNumber, $everyquestion);
					}
					elseif ($questiontype == "mcthree")
					{
						createMultipleChoice($surveyname, 3, $questionNumber, $everyquestion);	
					}
					elseif ($questiontype == "mcfour")
					{
						createMultipleChoice($surveyname, 4, $questionNumber, $everyquestion);
					}
					elseif ($questiontype == "mcfive")
					{
						createMultipleChoice($surveyname, 5, $questionNumber, $everyquestion);
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
	function addTrueFalse($questionnumber, $questiontype, $questiontext, $truefalseheading1, $truefalseheading2)
	{
		// Add true false question to DB
	}

	function createQuestionDiv($surveyname, $questionNumber, $everyquestion)
	{
		echo "
			<div id='questions'>
				<form action='createsurvey.php' method='POST'>
					<p>Survey $surveyname</p>
					<p>Please select the type of question:</p>
					<p><input type='radio' name='questiontype' value='truefalse'>True / False</p>
					<p>Multiple Choice:</br>
					<input type='radio' name='questiontype' value='mcthree'>3 questions<br/>
					<input type='radio' name='questiontype' value='mcfour'>4 questions<br/>
					<input type='radio' name='questiontype' value='mcfive'>5 questions<br/>
					</p>
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

	function createTrueFalse($surveyname, $questionNumber, $everyquestion)
	{
		echo "
			<div id='truefalse'>
				<form action='createsurvey.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create True / False Question</p>
					<p>Please enter the question:</p>
					<p><textarea name='questiontext' rows='3' cols='30'></textarea></p>
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
					<input type='hidden' name='createtruefalse' value='yes'>
					<input type='hidden' name='surveyname' value='$surveyname'>
					<input type='hidden' name='createtype' value='truefalse'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";      
	}

	function createMultipleChoice($surveyname, $answers, $questionNumber, $everyquestion)
	{
		/***
		echo "
			<div id='multiplechoice'>
				<form action='createsurvey.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create Multiple Choice Question</p>
					<p>Please enter the question:</p>
					<p><textarea name='questiontext' rows='3' cols='30'></textarea></p>
					<p>
		";

		// Answer 1, 2 and 3
		$type = "mcthree";

		echo "
			Answer 1: <input type='text' id='mcanswer1'><br/>
			Answer 2: <input type='text' id='mcanswer2'><br/>
			Answer 3: <input type='text' id='mcanswer3'><br/>
		";

		// Answer 4
		if ($answers == 4 || $answers == 5)
		{
			$type = "mcfour";
			echo "Answer 4: <input type='text' id='mcanswer4'><br/>";
		}

		// Answer 5
		if ($answers == 5)
		{
			$type = "mcfive";
			echo "Answer 5: <input type='text' id='mcanswer5'><br/>";        
		}

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
					<input type='hidden' name='createmultiplechoice' value='yes'>
					<input type='hidden' name='surveyname' value='$surveyname'>
					<input type='hidden' name='createtype' value='$type'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";
		***/
	}

	function createSeveralAnswer($surveyname, $questionNumber, $everyquestion)
	{
		echo "
			<div id='severalanswer'>
				<form action='createSeveralAnswer.php' method='POST'>
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
		echo "<p>aaa<input type='button' value='Create Answers' onclick='loadSeveralAnswers();'>zzz</p>";

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
					<input type='hidden' name='createseveralanswer' value='yes'>
					<input type='hidden' name='surveyname' value='$surveyname'>
					<input type='hidden' name='createtype' value='severalanswer'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";
	}

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
					<input type='hidden' name='createfreeform' value='yes'>
					<input type='hidden' name='surveyname' value='$surveyname'>
					<input type='hidden' name='createtype' value='freeform'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";  
		***/
	}

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
					<input type='hidden' name='createrating' value='yes'>
					<input type='hidden' name='surveyname' value='$surveyname'>
					<input type='hidden' name='createtype' value='rating'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";	
		***/
	}

	function displayQuestions($surveyname)
	{
		echo "<p>Questions go here</p>";
	}

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
