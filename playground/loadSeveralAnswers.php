<?php
	/*********************************************************************************
		Create the textboxes returned by the AJAX call for several answer questions
	*********************************************************************************/
?>

<?php
	$answers = $_POST['answers'];
	$answerCount = $_POST['answercount'];

	$sevAnswer = array();

	for ($i = 0; $i < $answerCount; $i++)
	{
		$sevAnswerName = "sevanswer".($i + 1);

		$sevAnswer[$i] = $_POST[$sevAnswerName];
	}

	// Holds the value to be returned back to the createsurvey page.
	$answersValue = "";

	for ($i = 1; $i <= $answers; $i++)
	{
		$value = $sevAnswer[$i - 1];

		$answersValue .= "Answer $i: <input type='text' name='sevanswer$i' id='sevanswer$i' value='$value'><br/>";
	}

	$answersValue .= "<br/>";
	$answersValue .= "<input type='hidden' name='numberOfAnswers' value='$answers'>";
	$answersValue .= "<p><input type='submit' value='Create Question'>&nbsp;&nbsp;<input type='button' value='Cancel'></p>";

	echo $answersValue;
?>
