<?php
	$answers = $_POST['answers'];


	// Holds the value to be returned back to the createsurvey page.
	$answersvalue = "";

	for ($i = 1; $i <= $answers; $i++)
	{
		$answersvalue .= "Answer $i: <input type='text' name='mcanswer$i'><br/>";
	}
	$answersvalue .= "<br/>";
	$answersvalue .= "<input type='hidden' name='numberofanswers' value='$answers'>";

	echo $answersvalue;
?>
