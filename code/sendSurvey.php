<html>
<head>
</head>
<body>
	<?php
			//if ($_SERVER["REQUEST_METHOD"] == "POST")
			//{
				//   ***********************************************************
				//  *************************************************************
				// **** WE NEED TO REPLACE districtId WITH A SESSION VARIABLE ****
				//  *************************************************************
				//   ***********************************************************
				$districtId = 1;

				//$email1 = $_POST['email1'];
				//$email2 = $_POST['email2'];
				//$email3 = $_POST['email3'];
				//
				$from = $_POST['email1'];
				$subject = $_POST['email2'];
				$message = $_POST['email3'];
				// message lines should not exceed 70 characters (PHP rule), so wrap it
				$message = wordwrap($message, 70);
				// send mail
				mail("ddevito@gmail.com",$subject,$message,"From: $from\n");
				echo "Thank you for sending us feedback";
	?>
</body>
</html>