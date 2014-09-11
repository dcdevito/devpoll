<?php
	$answers = $_POST['answers'];


	// Holds the value to be returned back to the createsurvey page.
	$answersValue = "";

	for ($i = 1; $i <= $answers; $i++)
	{
		$answersValue .= "Answer $i: <input type='text' name='sevanswer$i'><br/>";
	}
	$answersValue .= "<br/>";
	$answersValue .= "<input type='hidden' name='numberOfAnswers' value='$answers'>";
	$answersValue .= "<p><input type='submit' value='Create Question'>&nbsp;&nbsp;<input type='button' value='Cancel'></p>";

	echo $answersValue;
?>
