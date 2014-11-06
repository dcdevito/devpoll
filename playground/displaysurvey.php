<?php
	// Draw the questions and answers on the screen for a given survey id

	// Make sure the person is logged in.
	include("verifylogin.php");

	//**********************************************
	//	Draw the questions on the screen
	//**********************************************
	function drawQuestions($surveyId, $surveyName)
	{
		// Import the library of question and answer functions.
		include("questionsandanswersfunctions.php");

		echo "Getting the questions and answers<br/>";
		// Get the questions and answers from the database.
		$result = getSurveyQuestionsAndAnsers($surveyId);

		echo "We got the questions and answers<br/>";
		// Draw the questions and answers in a table.
		displaySurveyQuestions($result, $surveyName, $surveyId);
	}
	
?>
