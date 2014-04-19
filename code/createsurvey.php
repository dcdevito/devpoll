<!DOCTYPE html>
<html>
	<head>
		<title>Stokely Communications - Create Survey</title>

		<link rel="stylesheet" type="text/css" href="devpoll.css">
	</head>
	<body>
		<div id="menu">
			<ul>
				<li><img src="./images/DevPollLogo.png"></li>
				<li><a href="index.html">Home</a></li>
				<li><a href="aboutus.html">About Us</a></li>
				<li><a href="services.html">Services</a></li>
				<li><a href="contactus.html">Contact Us</a></li>
			</ul>
		</div>
		<div id="main">
				<h2>Create Survey</h2>
				<br />
				<form name="createsurvey" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
					<br />
					<span id="surveyname">Survey Name:</span>&nbsp;&nbsp;<input type="text" name="surveyname" size="40">
					<br />
					<br />
					<input type="button" value="Add New Question"/>
					<input type="button" value="Include Existing Question"/>
					<br />
					<br />
					<input type="button" value="End Create Survey"/>
					<br />
					<br />
					<br />
						<?php
							displayQuestions();
						?>
				</form>
		</div>
		<div id="footer">
			<span class="copyright">copyright DevPoll 2014</spam>
		</div>		
	</body>
</html>

<?php
	function displayQuestions()
	{
		echo "<div style='width: 50%; margin: 0 auto; text-align: center;'>";
		echo "<div style='text-align:center; background-color: #c4c4c4; color: #111111; border: 1px solid #444444; border-radius: 14px; width:100%; padding:2px;'>";
		echo "<table width='100%'>";
		echo "<tr>";
		echo "<td align='left'>";
		echo "1";
		echo "</td>";
		echo "<td width='35%'>&nbsp;</td>";
		echo "<td width='5%'>&nbsp;</td>";
		echo "<td width='45%' align='right'><a href=''>X</a></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td colspan='4'>";		
		echo "What is the capital of England?";
		echo "</td>";
		echo "</tr>";
		echo "<tr><td colspan='4'>&nbsp;</td></tr>";
		echo "<tr>";
		echo "<td width='15%'>&nbsp;</td>";
		echo "<td width='35%' align='left'>";
		echo "<input type='radio'>London";
		echo "</td>";
		echo "<td width='5%'>&nbsp;</td>";
		echo "<td width='45%' align='left'>";
		echo "<input type='radio'>Paris";
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td width='15%'>&nbsp;</td>";
		echo "<td width='35%' align='left'>";
		echo "<input type='radio'>New York";
		echo "</td>";
		echo "<td width='5%'>&nbsp;</td>";
		echo "<td width='45%' align='left'>";
		echo "<input type='radio'>Rome";
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		echo "</div>";
		echo "<br />";
		echo "<div style='text-align:center; background-color: #c4c4c4; color: #111111; border: 1px solid #444444; border-radius: 14px; width:100%; padding: 2px;'>";
		echo "<table width='100%'>";
		echo "<tr>";
		echo "<td align='left'>";
		echo "2";
		echo "</td>";
		echo "<td width='35%'>&nbsp;</td>";
		echo "<td width='5%'>&nbsp;</td>";
		echo "<td width='45%' align='right'><a href=''>X</a></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td colspan='4'>";		
		echo "Which is better Apple or Microsoft?";
		echo "</td>";
		echo "</tr>";
		echo "<tr><td colspan='4'>&nbsp;</td></tr>";
		echo "<tr>";
		echo "<td width='15%'>&nbsp;</td>";
		echo "<td width='35%' align='left'>";
		echo "<input type='radio'>Apple";
		echo "</td>";
		echo "<td width='5%'>&nbsp;</td>";
		echo "<td width='45%' align='left'>";
		echo "<input type='radio'>Microsoft";
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		echo "</div>";
		echo "</div>";
	}