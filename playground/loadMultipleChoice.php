<?php
	$answers = $_POST['answers'];

	$mcAnswer = array();
	$mcAnswer[0] = $_POST['mcanswer1'];
	$mcAnswer[1] = $_POST['mcanswer2'];
	$mcAnswer[2] = $_POST['mcanswer3'];
	$mcAnswer[3] = $_POST['mcanswer4'];
	$mcAnswer[4] = $_POST['mcanswer5'];
	$mcAnswer[5] = $_POST['mcanswer6'];
	$mcAnswer[6] = $_POST['mcanswer7'];
	$mcAnswer[7] = $_POST['mcanswer8'];
	$mcAnswer[8] = $_POST['mcanswer9'];
	$mcAnswer[9] = $_POST['mcanswer10'];

echo "Answers = $answers<br/>";
echo "mcAnswer 0 = $mcAnswer[0]<br/>";
echo "mcAnswer 1 = $mcAnswer[1]<br/>";
echo "mcAnswer 2 = $mcAnswer[2]<br/>";
echo "mcAnswer 3 = $mcAnswer[3]<br/>";
echo "mcAnswer 4 = $mcAnswer[4]<br/>";
echo "mcAnswer 5 = $mcAnswer[5]<br/>";
echo "mcAnswer 6 = $mcAnswer[6]<br/>";
echo "mcAnswer 7 = $mcAnswer[7]<br/>";
echo "mcAnswer 8 = $mcAnswer[8]<br/>";
echo "mcAnswer 9 = $mcAnswer[9]<br/>";

	// Holds the value to be returned back to the createsurvey page.
	$answersValue = "";

	for ($i = 1; $i <= $answers; $i++)
	{
		$value = $mcAnswer[$i - 1];

		$answersValue .= "Answer $i: <input type='text' name='mcanswer$i' value='$value'><br/>";
	}
	$answersValue .= "<br/>";
	$answersValue .= "<input type='hidden' name='numberOfAnswers' value='$answers'>";
	$answersValue .= "<p><input type='submit' value='Create Question'>&nbsp;&nbsp;<input type='button' value='Cancel'></p>";

	echo $answersValue;
?>
