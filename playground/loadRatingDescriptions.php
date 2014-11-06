<?php
	// Create the textboxes returned by the AJAX call for rating descriptions

	$radescriptions = $_POST['ratingdescriptions'];


	// Holds the value to be returned back to the createsurvey page.
	$ratingDescriptionsValue = "";

	for ($i = 1; $i <= $radescriptions; $i++)
	{
		$ratingDescriptionsValue .= "Rating Description $i: <input type='text' id='radescription$i' name='radescription$i'><br/>";
	}
	$ratingDescriptionsValue .= "<br/>";
	$ratingDescriptionsValue .= "<input type='hidden' id='ratingCount' name='numberOfDescriptions' value='$radescriptions'>";

	echo $ratingDescriptionsValue;
?>
