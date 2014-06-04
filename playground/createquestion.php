<?php
	// Make sure the person is logged in.
	include("verifylogin.php");
?>

<?php 
//		<script type="text/javascript">
//			function startOver()
//			{
//	      		alert("Are you sure you want to start over?");
//	    	}
//		</script>

	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		// See if every question is of the same type.
		$everyquestion = mysql_real_escape_string($_POST['everyquestion']);

		// Check what type of question was chosen and load that box.
		$questiontype = mysql_real_escape_string($_POST['questiontype']);

		if ($questiontype == "truefalse")
		{
			createTrueFalse();
		}
		elseif ($questiontype == "mcthree")
		{
			createMultipleChoice(3);	
		}
		elseif ($questiontype == "mcfour")
		{
			createMultipleChoice(4);
		}
		elseif ($questiontype == "mcfive")
		{
			createMultipleChoice(5);
		}
		elseif ($questiontype == "severalanswer")
		{
			createSeveralAnswer();
		}
		elseif ($questiontype == "freeform")
		{
			createFreeFormText();
		}
		elseif ($questiontype == "rating")
		{
			createRating();
		}
		else
		{
			header("location: createsurvey.php");
		}

		// True false.
//		if (isset($_POST["mail"]) && !empty($_POST["mail"])) {
//			echo "Yes, mail is set";    
//		} else {  
//			echo "N0, mail is not set";
//		}				

//		$questionNumber = 1; 

//		createQuestionDiv($questionNumber);

//		
//		
//		
//		
//		
//		
//		
//		existingQuestionsList();		
	}
?>

  
	  
		<?php
			function createTrueFalse()
			{
				echo "
					<div id='truefalse'>
						<form action='createTrueFalse.php' method='POST'>
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
							<p><input type='checkbox' name='everyquestion' value='everyquestion'>Every question is of this type</p>
							<p>
							<input type='submit' value='Create Question'>
							&nbsp;&nbsp;
							<input type='button' value='Cancel'>
							</p>
						</form>
					</div>
				";      
			}
		?>

		<?php
			function createMultipleChoice($answers)
			{
				echo "
					<div id='multiplechoice'>
						<form action='createMultipleChoice.php' method='POST'>
							<p>Create Multiple Choice Question</p>
							<p>Please enter the question:</p>
							<p><textarea rows='3' cols='30'></textarea></p>
							<p>
				";

				// Answer 1, 2 and 3
				echo "
					Answer 1: <input type='text' id='mcanswer1'><br/>
					Answer 2: <input type='text' id='mcanswer2'><br/>
					Answer 3: <input type='text' id='mcanswer3'><br/>
				";

				// Answer 4
				if ($answers == 4 || $answers == 5)
				{
					echo "Answer 4: <input type='text' id='mcanswer4'><br/>";
				}

				// Answer 5
				if ($answers == 5)
				{
					echo "Answer 5: <input type='text' id='mcanswer5'><br/>";        
				}

				echo "
							</p>
							<p><input type='checkbox' name='everyquestion' value='everyquestion'>Every question is of this type</p>
							<p>
							<input type='submit' value='Create Question'>
							&nbsp;&nbsp;
							<input type='button' value='Cancel'>
							</p>
						</form>
					</div>
				";
			}
		?>

		<?php
			function createSeveralAnswer()
			{
				echo "
					<div id='severalanswer'>
						<form action='createSeveralAnswer.php' method='POST'>
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
							<p><input type='checkbox' name='everyquestion' value='everyquestion'>Every question is of this type</p>
							<p>
								<input type='submit' value='Create Question'>
								&nbsp;&nbsp;
								<input type='button' value='Cancel'>
							</p>
						</form>
					</div>
				";
			}
		?>

		<?php
			function createFreeFormText()
			{
				echo "
					<div id='freeformtext'>
						<form action='createFreeForm.php' method='POST'>
							<p>Create Free Form Question</p>
							<p>Please enter the question:</p>
							<p><textarea rows='3' cols='30'></textarea></p>
							<p><input type='checkbox' name='everyquestion' value='everyquestion'>Every question is of this type</p>
							<p>
								<input type='submit' value='Create Question'>
								&nbsp;&nbsp;
								<input type='button' value='Cancel'>
							</p>
						</form>
					</div>
				";  
			}
		?>

		<?php
			function createRating()
			{
				echo "
					<div id='rating'>
						<form action='createRating.php' method='POST'>
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

							<p><input type='checkbox' name='everyquestion' value='everyquestion'>Every question is of this type</p>

							<p>
								<input type='submit' value='Create Question'>
								&nbsp;&nbsp;
								<input type='button' value='Cancel'>
							</p>
						</form>
					</div>
				";	
			}
		?>

		<?php
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
