<?php

class LocalistEvent extends DataObject {

	private static $db = array(
		"Title" => "Varchar(255)",
		"Content" => "HTMLText",
		"URLSegment" => "Varchar(255)",
		"LocalistLink" => "Text",
		"MoreInfoLink" => "Text",
		"FacebookEventLink" => "Text",
		"ImageURL" => "Text",
		"Cost" => "Text",
		"Location" => "Text",
		"VenueID" => "Int",
		"VenueTitle" => "Varchar(255)",
		"VenueLink" => "Text",

	);

	private function getUpcomingDatesFromRaw($rawEvent){

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

	private function getTagsFromRaw($rawEvent){
		$tagsRaw = $rawEvent['keywords'];
		$tagsRaw = array_merge($tagsRaw, $rawEvent['tags']);

		$tags = new ArrayList();

		foreach($tagsRaw as $tagRaw){
			$tag = new LocalistTag();
			$tag->Title = $tagRaw;

			$tags->push($tag);
		}

		return $tags;
	}

	private function getTypesFromRaw($rawEvent){
		$typesRaw = $rawEvent['filters']['event_types'];

		if(isset($typesRaw)){
			$types = new ArrayList();

			foreach($typesRaw as $typeRaw){
				$type = new LocalistEventType();
				$type->ID = $typeRaw['id'];
				$type->Title = $typeRaw['name'];

				$types->push($type);

			}
			return $types;
		}

		return false;


	}

	public function getVenueFromID($venueID){
		$feedURL = LOCALIST_FEED_URL.'places/'.$venueID;
		$cache = new SimpleCache();

		$rawVenue = $cache->get_data($feedURL, $feedURL);
		$venueDecoded = json_decode($rawVenue, TRUE);

		$venue = new LocalistVenue();
		return $venue->parseVenue($venueDecoded);

	}

	public function parseEvent($rawEvent){

		$this->ID = $rawEvent['id'];
		$this->Title = $rawEvent['title'];
		$this->URLSegment = $rawEvent['urlname'];
		$this->Cost = $rawEvent['ticket_cost'];
		$this->Location = $rawEvent['room_number'];
		$this->Dates = $this->getUpcomingDatesFromRaw($rawEvent);
		$this->Venue = $this->getVenueFromID($rawEvent['venue_id']);
		$this->Content = $rawEvent['description_text'];
		$this->Tags = $this->getTagsFromRaw($rawEvent);
		$this->Types = $this->getTypesFromRaw($rawEvent);
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
		$link = $calendar->Link().'event/'.$this->URLSegment;
		return $link;
	}

}