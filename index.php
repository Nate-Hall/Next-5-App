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

		$DEBUG = false;

		$allEventTypes = GetEventTypes("4i5aIrwSTbg9Fo3h", "ACk9xStttAlzqa+u2aHZFXdoRgcfaNj8uqVAMk+HnDo=");
		
		
		$horseRacingTypeID = ExtractEventTypeId($allEventTypes, "Horse Racing");
		$greyhoundRacingTypeID = ExtractEventTypeId($allEventTypes, "Greyhound Racing");

		$allHorseEvents = GetEvents("4i5aIrwSTbg9Fo3h", "ACk9xStttAlzqa+u2aHZFXdoRgcfaNj8uqVAMk+HnDo=", $horseRacingTypeID);
		$allGreyhoundEvents = GetEvents("4i5aIrwSTbg9Fo3h", "ACk9xStttAlzqa+u2aHZFXdoRgcfaNj8uqVAMk+HnDo=", $greyhoundRacingTypeID);

		$horseEvents = GetEventList($allHorseEvents, "Horse Racing");
		$greyhoundEvents = GetEventList($allGreyhoundEvents, "Greyhound Racing");

		$orderedEvents = [];


		for($i = 0; $i < 25; $i++) {
			if(count($horseEvents) >= $i - 1) {
				$orderedEvents[] = $horseEvents[$i];
			}
			if(count($greyhoundEvents) >= $i - 1) {
				$orderedEvents[] = $greyhoundEvents[$i];
			}
		}

		foreach ($orderedEvents as $key => $value) {
			$name[$key] = $value['name'];
		}

		$offset = 0;
		for($i = 0; $i < count($orderedEvents) - $offset; $i++) {
			if(date("d-m-Y", strtotime($orderedEvents[$i]['time']) <= time())) {
				if(date("H:i:s", strtotime($orderedEvents[$i]['time']) < time())) {
					unset($orderedEvents[$i]);
					$orderedEvents = array_values($orderedEvents);
					$offset++;
					$i--;
				}
			}
		}
	?>

	<script>
		var events = <?php echo json_encode($orderedEvents); ?>;

		TriggerCountdowns();

		function TriggerCountdowns() {

			events = <?php echo json_encode($orderedEvents); ?>;

			for(var i = 0; i < events.length; i++) {
				if(new Date(events[i].time).getTime() - new Date().getTime() <= 0 || new Date(events[i].time).getTime() - new Date().getTime() >=  (3 * 1000 * 60 * 60 * 24)){
					events.splice(i, 1);
					i--;
				}
			}

			SortByKey(events, 'time');

			TriggerCountdown(1);
			TriggerCountdown(2);
			TriggerCountdown(3);
			TriggerCountdown(4);
			TriggerCountdown(5);
		}

		function SortByKey(array, key) {
		    return array.sort(function(a, b) {
		        var x = a[key]; var y = b[key];
		        return ((x < y) ? -1 : ((x > y) ? 1 : 0));
		    });
		}

		function TriggerCountdown(timeId) {
			var countDownDate = new Date(events[timeId].time).getTime();

			var x = setInterval(function() {

			  var now = new Date().getTime();

			  var distance = countDownDate - now;

			  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
			  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			  var seconds = Math.floor((distance % (1000 * 60)) / 1000);

			  document.getElementById("Time" + timeId).innerHTML = days + "d " + hours + "h "
			  + minutes + "m " + seconds + "s ";

			  if (distance < 0) {
			    clearInterval(x);
			    document.getElementById("Time" + timeId).innerHTML = "EVENT COMPLETE";
			    TriggerCountdowns();
			  }
			}, 1000);
		}
	</script>

	<div id="Container">
		<div id="Content">
			<div id="Times" class="Info">
				<h1 id="Time1"></h1>
				<h1 id="Time2"></h1>
				<h1 id="Time3"></h1>
				<h1 id="Time4"></h1>
				<h1 id="Time5"></h1>
			</div>
			<div id="Types" class="Info">
				<h1 id="Type1"><?php echo $orderedEvents[0]['type']; ?></h1>
				<h1 id="Type2"><?php echo $orderedEvents[1]['type']; ?></h1>
				<h1 id="Type3"><?php echo $orderedEvents[2]['type']; ?></h1>
				<h1 id="Type4"><?php echo $orderedEvents[3]['type']; ?></h1>
				<h1 id="Type5"><?php echo $orderedEvents[4]['type']; ?></h1>
			</div>
			<div id="Names" class="Info">
				<h1 id="Name1"><a href="eventDetails.php/?eventID=<?php echo  $orderedEvents[0]['id']; ?>" id="Link1"><?php echo $orderedEvents[0]['name']; ?></a></h1>
				<h1 id="Name2"><a href="eventDetails.php/?eventID=<?php echo  $orderedEvents[1]['id']; ?>" id="Link1"><?php echo $orderedEvents[1]['name']; ?></a></h1>
				<h1 id="Name3"><a href="eventDetails.php/?eventID=<?php echo  $orderedEvents[2]['id']; ?>" id="Link1"><?php echo $orderedEvents[2]['name']; ?></a></h1>
				<h1 id="Name4"><a href="eventDetails.php/?eventID=<?php echo  $orderedEvents[3]['id']; ?>" id="Link1"><?php echo $orderedEvents[3]['name']; ?></a></h1>
				<h1 id="Name5"><a href="eventDetails.php/?eventID=<?php echo  $orderedEvents[4]['id']; ?>" id="Link1"><?php echo $orderedEvents[4]['name']; ?></a></h1>
			</div>
		</div>
	</div>
</body>
</html>