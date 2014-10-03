<?php
	/************************************************
		Clear the sessions and log out of the page
	************************************************/
?>

<?php
	session_start();
	session_destroy();
	header("location: index.php");
?>
