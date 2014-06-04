<?php
	$answers = $_POST['answers'];


	// Holds the value to be returned back to the createsurvey page.
	$answersvalue = "Number of answers = ".$answers."<br/>";

	for ($i = 1; $i <= $answers; $i++)
	{
		$answersvalue .= "Answer $i: <input type='text' name='saanswer$i'><br/>";
	}
	$answersvalue .= "<br/>";

	echo $answersvalue;
?>
