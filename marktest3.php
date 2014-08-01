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

				$number1 = $_POST['number1'];
				$number2 = $_POST['number2'];

				echo "<p>You entered $number1 and $number2<br/>";
	?>
</body>
</html>