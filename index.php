<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Next 5 Web App</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
	<?php

		$DEBUG = false;

		$allEventTypes = GetEventTypes("4i5aIrwSTbg9Fo3h", "WcMwN+CwkZNTOF44gyjMhxVfvXs0caMM30jPE6PQIi8=");
		
		
		$horseRacingTypeID = ExtractEventTypeId($allEventTypes, "Horse Racing");
		$greyhoundRacingTypeID = ExtractEventTypeId($allEventTypes, "Greyhound Racing");

		$allHorseEvents = GetEvents("4i5aIrwSTbg9Fo3h", "WcMwN+CwkZNTOF44gyjMhxVfvXs0caMM30jPE6PQIi8=", $horseRacingTypeID);
		$allGreyhoundEvents = GetEvents("4i5aIrwSTbg9Fo3h", "WcMwN+CwkZNTOF44gyjMhxVfvXs0caMM30jPE6PQIi8=", $greyhoundRacingTypeID);

		$horseEvents = GetEventList($allHorseEvents, "Horse Racing");
		$greyhoundEvents = GetEventList($allGreyhoundEvents, "Greyhound Racing");

		$orderedEvents = [];


		for($i = 0; $i < 10; $i++) {
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


		//$orderedEvents = array_multisort($name, SORT_ASC, $orderedEvents);

		function FormatTime($rawTime) {
			return date("d M Y H:i:s", strtotime($rawTime));
		}

		function ArraySort($array, $on, $order=SORT_ASC){

		    $new_array = array();
		    $sortable_array = array();

		    if (count($array) > 0) {
		        foreach ($array as $k => $v) {
		            if (is_array($v)) {
		                foreach ($v as $k2 => $v2) {
		                    if ($k2 == $on) {
		                        $sortable_array[$k] = $v2;
		                    }
		                }
		            } else {
		                $sortable_array[$k] = $v;
		            }
		        }

		        switch ($order) {
		            case SORT_ASC:
		                asort($sortable_array);
		                break;
		            case SORT_DESC:
		                arsort($sortable_array);
		                break;
		        }

		        foreach ($sortable_array as $k => $v) {
		            $new_array[$k] = $array[$k];
		        }
		    }

		    return $new_array;
		}

		function GetEventTypes($appKey, $sessionToken) {
			$jsonResponse = CallAPI($appKey, $sessionToken, 'listEventTypes', '{"filter":{}}');

			return $jsonResponse;
		}

		function GetEvents($appKey, $sessionToken, $eventID) {
			$params = '{"filter":{"eventTypeIds":["' . $eventID . '"],
              "marketStartTime":{"from":"' . date('c') . '"}}}';
			$jsonResponse = CallAPI($appKey, $sessionToken, 'listEvents', $params);

			return $jsonResponse;
		}

		function ExtractEventTypeId($allEventTypes, $eventTypeName) {
		    foreach ($allEventTypes as $eventType) {
		        if ($eventType->eventType->name == $eventTypeName) {
		            return $eventType->eventType->id;
		        }
		    }
		}

		function GetEventList($allEvents, $eventTypeID) {
			$count = 0;
			$events = [];
			foreach ($allEvents as $count => $event) {
				$events[$count] = array('time' => FormatTime($event->event->openDate), 'type' => $eventTypeID, 'name' => $event->event->name);
				$count++;

				if($count >= 10) {
					break;
				}
			}

			return $events;
		}

		function CallAPI($appKey, $sessionToken, $operation, $params) {
		    $ch = curl_init();

		    curl_setopt($ch, CURLOPT_URL, "https://api.betfair.com/exchange/betting/rest/v1/" . $operation . "/");
		    curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    	'Connection: keep-alive',
		        'X-Application: ' . $appKey,
		        'X-Authentication: ' . $sessionToken,
		        'Accept: application/json',
		        'Content-Type: application/json'
		    ));

		    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

		    debug('Post Data: ' . $params);
		    $response = json_decode(curl_exec($ch));
		    debug('Response: ' . json_encode($response));

		    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		    curl_close($ch);

		    if ($http_status == 200) {
		        return $response;
		    } else {
		        echo 'Call to api-ng failed: ' . "\n";
		        echo  'Response: ' . json_encode($response) . "\n";
		        echo 'HTTP CODE: ' . $http_status;
		        exit(-1);
		    }
		}

		function debug($debugString) {
		    global $DEBUG;
		    if ($DEBUG)
		        echo $debugString . "\n\n";
		}
	?>

	<div id="Container">
		<div id="Content">
			<div id="Times" class="Info">
				<h1 id="Time1"><?php echo $orderedEvents[0]['time']; ?></h1>
				<h1 id="Time2"><?php echo $orderedEvents[1]['time']; ?></h1>
				<h1 id="Time3"><?php echo $orderedEvents[2]['time']; ?></h1>
				<h1 id="Time4"><?php echo $orderedEvents[3]['time']; ?></h1>
				<h1 id="Time5"><?php echo $orderedEvents[4]['time']; ?></h1>
			</div>
			<div id="Types" class="Info">
				<h1 id="Type1"><?php echo $orderedEvents[0]['type']; ?></h1>
				<h1 id="Type2"><?php echo $orderedEvents[1]['type']; ?></h1>
				<h1 id="Type3"><?php echo $orderedEvents[2]['type']; ?></h1>
				<h1 id="Type4"><?php echo $orderedEvents[3]['type']; ?></h1>
				<h1 id="Type5"><?php echo $orderedEvents[4]['type']; ?></h1>
			</div>
			<div id="Names" class="Info">
				<h1 id="Name1"><?php echo $orderedEvents[0]['name']; ?></h1>
				<h1 id="Name2"><?php echo $orderedEvents[1]['name']; ?></h1>
				<h1 id="Name3"><?php echo $orderedEvents[2]['name']; ?></h1>
				<h1 id="Name4"><?php echo $orderedEvents[3]['name']; ?></h1>
				<h1 id="Name5"><?php echo $orderedEvents[4]['name']; ?></h1>
			</div>
		</div>
	</div>

	<script>
		TriggerCountdowns();

		function TriggerCountdowns() {
			TriggerCountdown(1);
			TriggerCountdown(2);
			TriggerCountdown(3);
			TriggerCountdown(4);
			TriggerCountdown(5);
		}

		function TriggerCountdown(timeId) {
			// Set the date we're counting down to
			var countDownDate = new Date(document.getElementById("Time" + timeId).innerHTML).getTime();

			// Update the count down every 1 second
			var x = setInterval(function() {

			  // Get todays date and time
			  var now = new Date().getTime();

			  // Find the distance between now an the count down date
			  var distance = countDownDate - now;

			  // Time calculations for days, hours, minutes and seconds
			  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
			  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			  var seconds = Math.floor((distance % (1000 * 60)) / 1000);

			  // Display the result in the element with id="demo"
			  document.getElementById("Time" + timeId).innerHTML = days + "d " + hours + "h "
			  + minutes + "m " + seconds + "s ";

			  // If the count down is finished, write some text 
			  if (distance < 0) {
			    clearInterval(x);
			    document.getElementById("Time" + timeId).innerHTML = "EVENT COMPLETE";
			  }
			}, 1000);
		}
	</script>
</body>
</html>