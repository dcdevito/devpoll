<?php
	// Create the textboxes returned by the AJAX call for several answer questions

	$answers = $_POST['answers'];
	$answerCount = $_POST['answercount'];

	$saAnswer = array();

	for ($i = 0; $i < $answerCount; $i++)
	{
		$saAnswerName = "saanswer".($i + 1);

		$saAnswer[$i] = $_POST[$saAnswerName];
	}

	// Holds the value to be returned back to the createsurvey page.
	$answersValue = "";

	for ($i = 1; $i <= $answers; $i++)
	{
		$value = $saAnswer[$i - 1];

		$answersValue .= "Answer $i: <input type='text' id='saanswer$i' name='saanswer$i' value='$value'><br/>";
	}

	$answersValue .= "<br/>";
	$answersValue .= "<input type='hidden' id='saAnswerCount' name='numberOfAnswers' value='$answers'>";
	$answersValue .= "<p>";
	$answersValue .= "<input type='button' value='Create Question' onclick='addSeveralAnswer();'>";
	$answersValue .= "&nbsp;&nbsp;";
	$answersValue .= "<input type='button' value='Cancel'></p>";

	echo $answersValue;
?>
