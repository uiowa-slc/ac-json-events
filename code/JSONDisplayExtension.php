<?php
class JSONDisplayExtension extends DataExtension{

	private function parseEvent($rawEvent){

	 	$id = new Text('ID');
	 	$id = $rawEvent['id'];
	 	
	 	$name = new Text('name');
	 	$name->setValue($rawEvent['name']);

	 	$link = new Text('Link');
	 	$link->setValue($rawEvent['link']);
	 	
	 	$more_info_link = new Text('more_info_link');
	 	$more_info_link->setValue($rawEvent['more_info_link']);
	 	
	 	$imageURL = new Text('ImageURL');
		$imageURL->setValue($rawEvent['image']);	

		$cancel_note = new Text('cancel_note');
		$cancel_note->setValue($rawEvent['cancel_note']);
	 	
		$nextDateTime = new SS_Datetime('NextDateTime');
		$nextDateTime->setValue(strtotime($rawEvent['dates'][0]['start_date'].' '.$rawEvent['dates'][0]['start_time']));
		
		$dateTimeCount = new Int('DateTimeCount');
		$dateTimeCount->setValue(count($rawEvent['dates']));

		$cost = new Text('Cost');
		$cost->setValue($rawEvent['price']);
			
		$location = new Text('Location');
		$location->setValue($rawEvent['location']);

		$venue = new Text('Venue');
		if($rawEvent['venues']) {
			$venue->setValue($rawEvent['venues'][0]['name']);
		}

		$sponsors = new Text('Sponsors');
		if($rawEvent['sponsors']) {
			$sponsors->setValue($rawEvent['sponsors'][0]['name']);
		}

		$event_types = new Text('Event Types');
		if($rawEvent['event_types']) {
			$event_types->setValue($rawEvent['event_types'][0]['name']);					
		}

		$parsedEvent = new ArrayData(array(
		'ID'			=> $id,
	    'Title'         => $name,
	    'Link' => $link,
	    'MoreInfoLink' => $more_info_link,
	    'ImageURL' => $imageURL,
	    'CancelNote' => $cancel_note,
	    'NextDateTime'		=> $nextDateTime,
	    'DateTimeCount' => $dateTimeCount,
	    'Cost'		=> $cost,
	    'Location'	=> $location,
	    'Venue' => $venue,
	    'Sponsors' => $sponsors,
	    'EventTypes' => $event_types
	    ));
		return $parsedEvent;
	}

	public function AfterClassEvent($id){

		$feedURL = 'http://afterclass.uiowa.edu/events/feed/';
		$feed = new ArrayList();
		$rawFeed = file_get_contents($feedURL);

		$eventsDecoded = json_decode($rawFeed, TRUE);
		 
		foreach($eventsDecoded['events'] as $event) {
			if($event['id'] == $id){
				return $this->parseEvent($event);
			}
		}
		return false;
	}

	public function AfterClassEvents($feedURL="http://afterclass.uiowa.edu/events/feed/json") {
		
		$eventsList = new ArrayList();
		$rawFeed = file_get_contents($feedURL);
		$eventsDecoded = json_decode($rawFeed, TRUE);
		 
		foreach($eventsDecoded['events'] as $event) {
			$eventsList->push($this->parseEvent($event));
		}		
		return $eventsList;   
	}
}