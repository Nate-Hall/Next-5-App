<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Next 5 Web App</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<?php

		require_once('APIFunctions.php');
		session_start();

		$eventID = $_GET['eventID'];
		
		$eventDetails = GetEventDetails("4i5aIrwSTbg9Fo3h", "ACk9xStttAlzqa+u2aHZFXdoRgcfaNj8uqVAMk+HnDo=", $eventID);
	?>

	<div id="Container">
		<div id="Content">
			<p> Sorry guys, I had some issues acquiring an Application Key for the Ladbrokes API so I had to improvise and find another source.	</br>
			However, the API I managed to access didn't seem to have competitor or placing details.</br>
			So I've done some location info about the selected event instead!</p>
			<div id="Name" class="Info">
				<h1>Name of Event:</h1>
				<h3 id="Name1"><?php echo $eventDetails[0]['name']; ?></h3>
			</div>
			<div id="Location" class="Info">
				<h1>Venue:</h1>
				<h3 id="Location1"><?php echo $eventDetails[0]['venue']; ?></h3>
			</div>
			<div id="Country" class="Info">
				<h1>Country</h1>
				<h3 id="Country1"><?php echo $eventDetails[0]['country']; ?></h3>
			</div>
		</div>
	</div>
</body>
</html>