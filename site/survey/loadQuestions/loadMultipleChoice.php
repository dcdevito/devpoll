<?php
	// Create the textboxes returned by the AJAX call for multiple choice questions

	$answers = $_POST['answers'];
	$answerCount = $_POST['answercount'];

	print "answers = $answers<br/>";
	print "answerCount = $answerCount<br/>";

	$mcAnswer = array();

	for ($i = 0; $i < $answerCount; $i++)
	{
		$mcAnswerName = "mcanswer".($i + 1);

		$mcAnswer[$i] = $_POST[$mcAnswerName];
	}

	// Holds the value to be returned back to the createsurvey page.
	$answersValue = "";

	for ($i = 1; $i <= $answers; $i++)
	{
		$value = $mcAnswer[$i - 1];

		$answersValue .= "Answer $i: <input type='text' id='mcanswer$i' name='mcanswer$i' value='$value'><br/>";
	}

	$answersValue .= "<br/>";
	$answersValue .= "<input type='hidden' id='mcAnswerCount' name='numberOfAnswers' value='$answers'>";
	$answersValue .= "<p>";
	$answersValue .= "<input type='button' value='Create Question' onclick='addMultipleChoice();'>";
	$answersValue .= "&nbsp;&nbsp;";
	$answersValue .= "<input type='button' value='Cancel'></p>";

	echo $answersValue;
?>
