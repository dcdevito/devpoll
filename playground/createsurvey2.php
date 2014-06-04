<?php
	// Make sure the person is logged in.
	include("verifylogin.php");
?>

<html>
	<head>
		<title>DevPoll</title>
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
					// Connect to the database.
					require_once("connectToDB.php");

					// Check if the name is taken for this district.
					$result = $conn->query("SELECT surveyname FROM survey WHERE districtid = $districtid");

					$nameOK = true;
					echo "Before loop. nameOK = $nameOK<br/>";
					echo "Survey name = $surveyname<br/>";
					while($row = $result->fetch_assoc()) 
					{
						echo "Row survey name = $row['surveyname']<br/>";
						if ($row['surveyname'] == $surveyname)
						{
							echo "Name is taken<br/>";
							// The name is taken.
							$nameOK = false;
						}
					}

					// Close the connection.
					$conn->close();

					echo "Name is ok? $nameOK<br/>";
					if ($nameOK)
					{
						// Write the name to the database.
						addSurveyToDB($districtid, $surveyname);
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
						createTrueFalse($questionNumber, $everyquestion);
					}
					elseif ($questiontype == "mcthree")
					{
						createMultipleChoice(3, $questionNumber, $everyquestion);	
					}
					elseif ($questiontype == "mcfour")
					{
						createMultipleChoice(4, $questionNumber, $everyquestion);
					}
					elseif ($questiontype == "mcfive")
					{
						createMultipleChoice(5, $questionNumber, $everyquestion);
					}
					elseif ($questiontype == "severalanswer")
					{
						createSeveralAnswer($questionNumber, $everyquestion);
					}
					elseif ($questiontype == "freeform")
					{
						createFreeFormText($questionNumber, $everyquestion);
					}
					elseif ($questiontype == "rating")
					{
						createRating($questionNumber, $everyquestion);
					}
					else
					{
						$questionNumber = 0; 

						createQuestionDiv($surveyname, $questionNumber, $everyquestion);

						displayQuestions();
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

		if ($everyquestion == "everyquestion")
		{
			echo "<p><input type='checkbox' name='everyquestion' value='everyquestion' checked>Every question is of this type</p>";
		}
		else
		{
			echo "<p><input type='checkbox' name='everyquestion' value='everyquestion'>Every question is of this type</p>";
		}

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

	function addSurveyToDB($districtid, $surveyname)
	{
		// Connect to the database.
		require_once("connectToDB.php");

		try
		{
			// Start a transaction.
			$conn->autocommit(false);

			// Insert the values into the database.
			$conn->query("INSERT INTO survey(surveyname, districtid, dateopen, lastac) VALUES ('$surveyname', '$districtid', current_timestamp(), current_timestamp());") or die(mysql_error());

			// Commit the transaction.
			$conn->commit();

			$conn->close();

			return true;
		}
		catch(Exception $e)
		{
			$conn->rollback();

			return false;
		}
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

	function createTrueFalse($questionNumber, $everyquestion)
	{
		echo "
			<div id='truefalse'>
				<form action='createsurvey.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create True / False Question</p>
					<p>Please enter the question:</p>
					<p><textarea rows='3' cols='30'></textarea></p>
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

		if ($everyquestion == "everyquestion")
		{
			echo "<p><input type='checkbox' name='everyquestion' value='everyquestion' checked>Every question is of this type</p>";
		}
		else
		{
			echo "<p><input type='checkbox' name='everyquestion' value='everyquestion'>Every question is of this type</p>";
		}

		echo "
					<p>
					<input type='submit' value='Create Question'>
					&nbsp;&nbsp;
					<input type='button' value='Cancel'>
					</p>
					<input type='hidden' name='createtruefalse' value='yes'>
					<input type='hidden' name='surveyname' value='".$surveyname."'>
					<input type='hidden' name='createtype' value='truefalse'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";      
	}

	function createMultipleChoice($answers, $questionNumber, $everyquestion)
	{
		echo "
			<div id='multiplechoice'>
				<form action='createsurvey.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create Multiple Choice Question</p>
					<p>Please enter the question:</p>
					<p><textarea rows='3' cols='30'></textarea></p>
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

		if ($everyquestion == "everyquestion")
		{
			echo "<p><input type='checkbox' name='everyquestion' value='everyquestion' checked>Every question is of this type</p>";
		}
		else
		{
			echo "<p><input type='checkbox' name='everyquestion' value='everyquestion'>Every question is of this type</p>";
		}

		echo "
					<p>
					<input type='submit' value='Create Question'>
					&nbsp;&nbsp;
					<input type='button' value='Cancel'>
					</p>
					<input type='hidden' name='createmultiplechoice' value='$answers'>
					<input type='hidden' name='surveyname' value='".$surveyname."'>
					<input type='hidden' name='createtype' value='$type'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";
	}

	function createSeveralAnswer($questionNumber, $everyquestion)
	{
		echo "
			<div id='severalanswer'>
				<form action='createSeveralAnswer.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create Several Answer Question</p>
					<p>Please enter the question:</p>
					<p><textarea rows='3' cols='30'></textarea></p>
					<p>
		";

		// Answer 1 and 2
		echo "
			Answer 1: <input type='text' id='sevanswer1'><input type='button' value='remove'><br/>
			Answer 2: <input type='text' id='sevanswer2'><input type='button' value='remove'><br/>
		";

		echo "Answer 3: <input type='text' id='sevanswer3'><input type='button' value='remove'><br/>";

		echo "
						<p><input type='button' value='Add Another Answer'></p>
					</p>
		";

		if ($everyquestion == "everyquestion")
		{
			echo "<p><input type='checkbox' name='everyquestion' value='everyquestion' checked>Every question is of this type</p>";
		}
		else
		{
			echo "<p><input type='checkbox' name='everyquestion' value='everyquestion'>Every question is of this type</p>";
		}

		echo "
					<p>
						<input type='submit' value='Create Question'>
						&nbsp;&nbsp;
						<input type='button' value='Cancel'>
					</p>
					<input type='hidden' name='createseveralanswer' value='yes'>
					<input type='hidden' name='surveyname' value='".$surveyname."'>
					<input type='hidden' name='createtype' value='severalanswer'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";
	}

	function createFreeFormText($questionNumber, $everyquestion)
	{
		echo "
			<div id='freeformtext'>
				<form action='createFreeForm.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create Free Form Question</p>
					<p>Please enter the question:</p>
					<p><textarea rows='3' cols='30'></textarea></p>
		";

		if ($everyquestion == "everyquestion")
		{
			echo "<p><input type='checkbox' name='everyquestion' value='everyquestion' checked>Every question is of this type</p>";
		}
		else
		{
			echo "<p><input type='checkbox' name='everyquestion' value='everyquestion'>Every question is of this type</p>";
		}

		echo "
					<p><input type='checkbox' name='everyquestion' value='everyquestion'>Every question is of this type</p>
					<p>
						<input type='submit' value='Create Question'>
						&nbsp;&nbsp;
						<input type='button' value='Cancel'>
					</p>
					<input type='hidden' name='createfreeform' value='yes'>
					<input type='hidden' name='surveyname' value='".$surveyname."'>
					<input type='hidden' name='createtype' value='freeform'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";  
	}

	function createRating($questionNumber, $everyquestion)
	{
		echo "
			<div id='rating'>
				<form action='createRating.php' method='POST'>
					<p>Question $questionNumber</p>
					<p>Create Rating Question</p>
					<p>Please enter the question:</p>
					<p><textarea rows='3' cols='30'></textarea></p>

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

		if ($everyquestion == "everyquestion")
		{
			echo "<p><input type='checkbox' name='everyquestion' value='everyquestion' checked>Every question is of this type</p>";
		}
		else
		{
			echo "<p><input type='checkbox' name='everyquestion' value='everyquestion'>Every question is of this type</p>";
		}

		echo "
					<p>
						<input type='submit' value='Create Question'>
						&nbsp;&nbsp;
						<input type='button' value='Cancel'>
					</p>
					<input type='hidden' name='createrating' value='yes'>
					<input type='hidden' name='surveyname' value='".$surveyname."'>
					<input type='hidden' name='createtype' value='rating'>
					<input type='hidden' name='questionNumber' value='$questionNumber'>
				</form>
			</div>
		";	
	}

	function displayQuestions()
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
