<?php

class LocalistEvent extends DataObject {

	private static $db = array(
		"Title" => "Varchar(255)",
		"Content" => "HTMLText",
		"LocalistLink" => "Text",
		"MoreInfoLink" => "Text",
		"FacebookEventLink" => "Text",
		"ImageURL" => "Text",
		"Cost" => "Text",
		"Location" => "Text",
		"VenueID" => "Int",
		"VenueTitle" => "Varchar(255)",
		"VenueLink" => "Text"
	);

	public function getUpcomingDatesFromRaw($rawEvent){
		$eventInstances = $rawEvent['event_instances'];
		$eventInstancesArray = new ArrayList();

		foreach($eventInstances as $i => $eventInstance){

			$dateTime = new LocalistDatetime();
			$dateTime->setValue($eventInstances[$i]['event_instance']['start']);

			if(!$dateTime->InPast()){
				$eventInstancesArray->push($dateTime);
			}
		}

		return $eventInstancesArray;
		
	}

	public function getVenueFromID($venueID){
		$feedURL = LOCALIST_FEED_URL.'places/'.$venueID;
		$cache = new SimpleCache();

		$rawVenue = $cache->get_data("Venue-".$venueID, $feedURL);
		$venueDecoded = json_decode($rawVenue, TRUE);

		$venue = new LocalistVenue();
		return $venue->parseVenue($venueDecoded);

	}

	public function parseEvent($rawEvent){

		$this->ID = $rawEvent['id'];
		$this->Title = $rawEvent['title'];
		$this->Cost = $rawEvent['ticket_cost'];
		$this->Location = $rawEvent['room_number'];
		$this->Dates = $this->getUpcomingDatesFromRaw($rawEvent);
		$this->Venue = $this->getVenueFromID($rawEvent['venue_id']);
		$this->Content = $rawEvent['description_text'];
		$this->ImageURL = $rawEvent['photo_url'];
		$this->LocalistLink = $rawEvent['localist_url'];
		$this->MoreInfoLink = $rawEvent['url'];
		$this->FacebookEventLink = $rawEvent['facebook_id'];

		if(isset($venue['place']['name'])){
			$this->VenueTitle = $venue['place']['name'];
		}

		return $this;

	}

	public function Link(){
		$calendar = LocalistCalendar::get()->First();
		$link = $calendar->Link().'event/'.$this->ID;
		return $link;
	}

}