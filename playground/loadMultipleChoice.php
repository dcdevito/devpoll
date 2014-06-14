<?php
	$answers = $_POST['answers'];


	// Holds the value to be returned back to the createsurvey page.
	$answersValue = "";

	for ($i = 1; $i <= $answers; $i++)
	{
		$answersValue .= "Answer $i: <input type='text' name='mcanswer$i'><br/>";
	}
	$answersValue .= "<br/>";
	$answersValue .= "<input type='hidden' name='numberOfAnswers' value='$answers'>";

	echo $answersValue;
?>
