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

		echo "1. Get all Event Types....\n";
		$allEventTypes = GetEventTypes("4i5aIrwSTbg9Fo3h", "WcMwN+CwkZNTOF44gyjMhxVfvXs0caMM30jPE6PQIi8=");
		echo json_encode($allEventTypes);

		function GetEventTypes($appKey, $sessionToken) {
			$jsonResponse = CallAPI($appKey, $sessionToken, 'listEventTypes', '{"filter":{}}');

			return $jsonResponse;
		}

		function CallAPI($appKey, $sessionToken, $operation, $params) {
		    $ch = curl_init();

		    curl_setopt($ch, CURLOPT_URL, "https://api.betfair.com/exchange/betting/rest/v1/listEventTypes/");
		    curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
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

	<!--<div id="Container">
		<div id="Content">
			<div id="Times" class="Info">
				<h1 id="Time1">Times</h1>
				<h1 id="Time2">Times</h1>
				<h1 id="Time3">Times</h1>
				<h1 id="Time4">Times</h1>
				<h1 id="Time5">Times</h1>
			</div>
			<div id="Types" class="Info">
				<h1 id="Type1">Types</h1>
				<h1 id="Type2">Types</h1>
				<h1 id="Type3">Types</h1>
				<h1 id="Type4">Types</h1>
				<h1 id="Type5">Types</h1>
			</div>
			<div id="Names" class="Info">
				<h1 id="Name1">Names</h1>
				<h1 id="Name2">Names</h1>
				<h1 id="Name3">Names</h1>
				<h1 id="Name4">Names</h1>
				<h1 id="Name5">Names</h1>
			</div>
		</div>
		<h1 id="output">12345</h1>
	</div>-->
</body>
</html>