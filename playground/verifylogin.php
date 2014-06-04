<?php
	session_start();

	if (! $_SESSION['user'])
	{
		header("location: index.php");
	}

	$user = $_SESSION['user'];
?>
