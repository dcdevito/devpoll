<?php
	/*****************************************************************************
		Create the textboxes returned by the AJAX call for rating descriptions
	*****************************************************************************/
?>

<?php
	$descriptions = $_POST['descriptions'];


	// Holds the value to be returned back to the createsurvey page.
	$descriptionsValue = "";

	for ($i = 1; $i <= $descriptions; $i++)
	{
		$descriptionsValue .= "Description $i: <input type='text' name='ratingdescription$i'><br/>";
	}
	$descriptionsValue .= "<br/>";
	$descriptionsValue .= "<input type='hidden' name='numberOfDescriptions' value='$descriptions'>";

	echo $descriptionsValue;
?>
