<?php
	$eventID = 0;

	function SetEventID($newID) {

	}

	function GetEventID() {

	}

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
			$events[$count] = array('time' => FormatTime($event->event->openDate), 'type' => $eventTypeID, 'name' => $event->event->name, 'id' => $event->event->id);
			$count++;

			if($count >= 50) {
				break;
			}
		}

		return $events;
	}

	function GetEventDetails($appKey, $sessionToken, $eventID) {
		$params = '{"filter":{"eventIds":["' . $eventID . '"],
          "marketStartTime":{"from":"' . date('c') . '"}}}';
		$jsonResponse = CallAPI($appKey, $sessionToken, 'listEvents', $params);

		foreach ($jsonResponse as $count => $event) {
			if(isset($event->event->venue)) {
				$jsonResponse[$count] = array('name' => $event->event->name, 'venue' => $event->event->venue, 'country' => $event->event->countryCode);
			} else {
				$jsonResponse[$count] = array('name' => $event->event->name, 'venue' => "N/A", 'country' => $event->event->countryCode);
			}
		}

		return $jsonResponse;
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