<?php
	// Make sure the person is logged in.
	include("verifylogin.php");
?>

<?php
	function drawQuestions($surveyId, $surveyName)
	{
		include("questionsandanswersfunctions.php");

		echo "Getting the questions and answers<br/>";
		$result = getSurveyQuestionsAndAnsers($surveyId);

		echo "We got the questions and answers<br/>";
		displaySurveyQuestions($result, $surveyName, $surveyId);
	}
	
?>
