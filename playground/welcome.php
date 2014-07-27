<?php
	// Make sure the person is logged in.
	include("verifylogin.php");	
?>

<html>
	<head>
		<title>Welcome to DevPoll</title>
	</head>
	<body>
		<h2>Welcome <?php echo "$user"; ?> to DevPoll</h2>
		<br/>
		<p>Please choose what you would like to do:</p>
		<br/>
		<ul>
			<li><a href="createsurvey.php">Create Survey</a></li>
			<li><a href="editsurvey.php">Edit Survey</a></li>
			<li><a href="deletesurvey.php">Delete Survey</a></li>
			<li><a href="listsurveys.php">List Surveys</a></li>
			<li><a href="createparticipants.php">Create Participant List</a></li>
			<li><a href="editparticipants.php">Edit Participant List</a></li>
			<li><a href="deleteparticipants.php">Delete Participant List</a></li>
			<li><a href="listparticipants.php">List All Participants</a></li>
			<li><a href="sendsurvey.php">Send Survey</a></li>
			<li><a href="reports.php">Reports</a></li>
			<li><a href="summary.php">View Summary</a></li>
			<li><a href="logout.php">Logout</a></li>
		</ul>
	</body>
</html>
