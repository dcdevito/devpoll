<?php
	$answers = $_POST['answers'];
	$answerArray = $POST['answerArray'];

	// Holds the value to be returned back to the createsurvey page.
	$answersValue = "";

	for ($i = 1; $i <= $answers; $i++)
	{
		$value = $answerArray[$i-1];

		$answersValue .= "Answer $i: <input type='text' name='mcanswer$i' value='$value'><br/>";
	}
	$answersValue .= "<br/>";
	$answersValue .= "<input type='hidden' name='numberOfAnswers' value='$answers'>";

	echo $answersValue;
?>
