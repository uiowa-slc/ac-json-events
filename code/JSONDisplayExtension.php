<?php
class JSONDisplayExtension extends DataExtension{
	
	/*
	private function getEventInstance($eventID){
	
	}
	*/
	private function getDates($rawEvent){
		$eventInstances = $rawEvent['event_instances'];
		$eventInstancesArray = new ArrayList();

		foreach($eventInstances as $i => $eventInstance){

			$dateTime = new LocalistDatetime();
			$dateTime->setValue($eventInstances[$i]['event_instance']['start']);

			$eventInstancesArray->push($dateTime);
		}

		return $eventInstancesArray;
		
	}	
	private function getNextDateTime($rawEvent){
		$event_instances = $rawEvent['event_instances'];
		return(strtotime($event_instances[0]['event_instance']['start']));
		
	}

	private function getDateLink($NextDateTimeVar){	
		$datestring = date("Y/n/j", strtotime($NextDateTimeVar));
		$urlparts = array(
			LOCALIST_BASE,
			"calendar/day/",
			$datestring
			);
		//print_r(implode($urlparts));
		return (implode($urlparts));
	}
	
	private function getVenue($venueID){
		$feedURL = LOCALIST_FEED_URL.'places/'.$venueID;
		$rawFeed = file_get_contents($feedURL);

		$venue['places'] = json_decode($rawFeed, TRUE); 
		return $venue['places'];

	}

	private function parseEvent($rawEvent){

	 	$id = new Text('ID');
	 	$id->setValue($rawEvent['id']);
	 	
	 	$title = new Text('Title');
	 	$title->setValue($rawEvent['title']);

	 	$link = new Text('Localist_url');
	 	$link->setValue($rawEvent['localist_url']);
	 	
	 	$more_info_link = new Text('Info_link');
	 	$more_info_link->setValue($rawEvent['url']);

	 	$facebook_event_link = new Text('facebook_event_id');
	 	$facebook_event_link->setValue($rawEvent['facebook_id']);

	 	$imageURL = new Text('ImageURL');
		$imageURL->setValue($rawEvent['photo_url']);	

	 	/*$thumbnailURL = new Text('ImageURL');
		$thumbnailURL->setValue($rawEvent['thumbnail_url']);*/
		/*
		//no field for a cancel_note in Localist api. Perhaps use a custom field?
		$cancel_note = new Text('cancel_note');
		$cancel_note->setValue($rawEvent['cancel_note']);
	 	*/
	 	/*
	 	
	 	// Currently no NextDateTime Field. Could use function to return something from event_instances
		$nextDateTime = new SS_Datetime('NextDateTime');
		$nextDateTime->setValue(strtotime($rawEvent['event_instances'][0]['start']));
		$nextDateTime->setValue(strtotime($rawEvent['dates'][0]['start_date'].' '.$rawEvent['dates'][0]['start_time']));
		
		//wip -jonathan
		//foreach($rawEvent['event_instances'] as $instance {
		//	if (strtotime(substr($instance['event_instance']['start'], 0, )))
		//}
		*/

		$dates = new ArrayList();
		$dates = $this->getDates($rawEvent);

		$nextDateTime = new SS_Datetime('NextDateTime');
		$time = time();
		$getNextDateTime = $dates[0];
		print_r($getNextDateTime);
		$nextDateTime->setValue($getNextDateTime);
		
		/*
		foreach($event_instances as $instance) {
				foreach($instance as $stance) {
					//print_r (strtotime(substr($eventInstance['event_instance']['start'], 0, 10)));
					//if (strtotime(substr($stance['event_instance']['start'], 0, 10)) >= $time) {
						$nextDateTime->setValue($stance['event_instance']['start']);
					//	break;
					//} else {
					//	print_r ('nothing found');
				}	
		}
		*/
		
		$moreDateTime = new Int('DateTimeCount');
		$event_instances = $rawEvent['event_instances'];
		$upcomingDateCount = 0;
		foreach ($event_instances as $upcoming_dates) {
			$upcomingDateCount++;
		}
		$upcomingDateCount--;
		$moreDateTime->setValue($upcomingDateCount);
		
		$dateLink = new Text('DateLink');
		$getDateLink = $this->getDateLink($getNextDateTime);
		$dateLink->setValue($getDateLink);
		
		$cost = new Text('Cost');
		if ($rawEvent['free']) {
			$cost->setValue('Free');
		} else {
			$cost->setValue($rawEvent['ticket_cost']);
		}
		
		$location = new Text('Location');
		if ($rawEvent['room_number']) {
			$room = $rawEvent['room_number'];
			$location->setValue($rawEvent['location'], $room);
		} else {
			$location->setValue($rawEvent['location']);
		}

		$venue = $this->getVenue($rawEvent['venue_id']);
		
		$venueTitle = new Text('VenueTitle');
		if($rawEvent['venue_id']) {
			$venueTitle->setValue($venue['place']['name']);
		}

		$venueLink = new Text('VenueLink');
		if($rawEvent['venue_url']) {
			$venueLink->setValue($rawEvent['venue_url']);
		}
		/*
		// Localist provides a 'sponsored' key, but it's a boolean. so...not what we're looking for here
		$sponsors = new Text('Sponsors');
		if($rawEvent['sponsors']) {
			$sponsors->setValue($rawEvent['sponsors'][0]['name']);
		}
		*/

		$eventTypes = new Text('Event Types');
		if($rawEvent['filters']['event_types']) {
			$eventTypeNames = array_map(function($item) { return $item['name']; }, $rawEvent['filters']['event_types']);
			//$eventTypeNames = array_column($rawEvent['event_types'], 'name');
			$eventTypes->setValue(implode(', ', $eventTypeNames));
		}

		$parsedEvent = new ArrayData(array(
			'ID'				=> $id,
		    'Title'           	=> $title,
		    'Link' 				=> $link,
		    'FacebookEventLink' => $facebook_event_link,
		    'MoreInfoLink' 		=> $more_info_link,
		    'ImageURL'			=> $imageURL,
		    'Dates' 			=> $dates,
		    'DateLink'			=> $dateLink,
		    //'CancelNote' 		=> $cancel_note,
		    'NextDateTime'		=> $nextDateTime,
		    'MoreDateTime' 	    => $moreDateTime,
		    'Cost'				=> $cost,
		    'Location'			=> $location,
		    'VenueTitle' 		=> $venueTitle,
		    'VenueLink' 		=> $venueLink,
		    //'Sponsors' 		=> $sponsors,
		    'EventTypes' 		=> $eventTypes
	    ));

		return $parsedEvent;
	}
	
	public function ActiveVenues() {
		
	}
		
	public function AfterClassEvents($feedURL = "events/?days=200&pp=50&distinct=true") {
		$feedURL = LOCALIST_FEED_URL.$feedURL;
		$eventsList = new ArrayList();
		$rawFeed = file_get_contents($feedURL);
		$eventsDecoded = json_decode($rawFeed, TRUE);
		$eventsArray = $eventsDecoded['events'];
		foreach($eventsArray as $event) {
			$eventsList->push($this->parseEvent($event['event']));
		}		
		return $eventsList;   
	}
	
	public function AfterClassEvent($id){

		$feedURL = 'http://localhost:8888/localist-api-examples/events.json';
		$feed = new ArrayList();
		$rawFeed = file_get_contents($feedURL);
		$eventsDecoded = json_decode($rawFeed, TRUE);
		$eventsList = $eventsDecoded['events'];
		if(isset($eventsList)){
			//echo('hello, world');
			//print_r ($eventsList);
			foreach($eventsList as $event) {
				//print_r ($event);
				if($event['event']['id'] == $id){
					return $this->parseEvent($event);
				}
			}
		}
		return false;
	}
	
}