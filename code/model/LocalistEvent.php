<?php

class LocalistEvent extends DataObject {

	/**
	 * Convert an event in an array format (from Localist JSON Feed) to a LocalistEvent
	 * @param array $rawEvent 
	 * @return LocalistEvent
	 */
	public function parseEvent($rawEvent){

		$image = new LocalistImage();

		if(isset($rawEvent['photo_id'])){
			$image = $image->getByID($rawEvent['photo_id']);
		}else{
			$themeDir = $this->ThemeDir();
			$image = new LocalistImage();
			$image->URL = $themeDir.'/images/LocalistEventPlaceholder.jpg';
		}
		
		$this->ID = $rawEvent['id'];
		$this->Title = $rawEvent['title'];
		$this->EventTitle = $rawEvent['title'];
		$this->URLSegment = $rawEvent['urlname'];
		$this->Featured = $rawEvent['featured'];
		$this->Cost = $rawEvent['ticket_cost'];
		$this->Location = $rawEvent['room_number'];
		$this->Dates = $this->getUpcomingDatesFromRaw($rawEvent);
		$this->Venue = $this->getVenueFromRaw($rawEvent);
		$this->Content = $rawEvent['description'];
		$this->Tags = $this->getTagsFromRaw($rawEvent);
		$this->Types = $this->getTypesFromRaw($rawEvent);
		$this->Image = $image;
		$this->LocalistLink = $rawEvent['localist_url'];
		$this->MoreInfoLink = $rawEvent['url'];
		$this->FacebookEventLink = $rawEvent['facebook_id'];
		$this->ContactName = (isset($rawEvent['custom_fields']['contact_name']) ? $rawEvent['custom_fields']['contact_name'] : '');
		$this->ContactEmail = (isset($rawEvent['custom_fields']['contact_email']) ? $rawEvent['custom_fields']['contact_email'] : '');

		if(isset($venue['place']['name'])){
			$this->VenueTitle = $venue['place']['name'];
		}
		
		return $this;

	}

	//Weird hack to get around the default DataObject getTitle
	public function getTitle(){
		return $this->EventTitle;
	}
	/**
	 * Get a list of upcoming dates for a single event by checking an individual event
	 * for instances from Localist
	 * @param array $rawEvent 
	 * @return ArrayList
	 */
	private function getUpcomingDatesFromRaw($rawEvent){
		$eventInstances = $rawEvent['event_instances'];
		$eventInstancesArray = new ArrayList();

		foreach($eventInstances as $i => $eventInstance){
			$dateTime = new LocalistDatetime();
			$dateTime->setValue($eventInstances[$i]['event_instance']['start']);
			if(!$dateTime->InPast() || $dateTime->IsToday() ){
				$eventInstancesArray->push($dateTime);
			}
		}
		return $eventInstancesArray;
	}

	/**
	 * Generate a list of tags from an event array from Localist
	 * @param array $rawEvent 
	 * @return ArrayList
	 */
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

	/**
	 * Generate a list of types from an event array from Localist
	 * @param array $rawEvent 
	 * @return ArrayList
	 */
	private function getTypesFromRaw($rawEvent){
		
		if(isset($rawEvent['filters']['event_types'])){
			$typesRaw = $rawEvent['filters']['event_types'];
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

	/**
	 * Get a Venue from a raw event array from a JSON feed. If there's a venue id, we get a venue based on that id,
	 * otherwise we retrieve the geo and location info from the event itself.
	 * @param array $rawEvent 
	 * @return LocalistVenue
	 */
	private function getVenueFromRaw($rawEvent){
		if(isset($rawEvent['venue_id'])){
			$id = $rawEvent['venue_id'];
			return $this->getVenueFromID($id);
		}else{
			$venue = new LocalistVenue();
			$venue->Title = $rawEvent['location'];
			$venue->Latitude = $rawEvent['geo']['latitude'];
			$venue->Longitude = $rawEvent['geo']['longitude'];
			$venue->Address = $rawEvent['geo']['street'].', '.$rawEvent['geo']['city'].', '.$rawEvent['geo']['state'].' '.$rawEvent['geo']['zip'];
			return $venue;
		}
	}

	/**
	 * Retrieve a single venue from the Localist Venue API by an ID number
	 * @param int $venueID 
	 * @return LocalistVenue
	 */
	public function getVenueFromID($venueID){
		$feedURL = LOCALIST_FEED_URL.'places/'.$venueID;
		$cache = new SimpleCache();

		$rawVenue = $cache->get_data($feedURL, $feedURL);
		$venueDecoded = json_decode($rawVenue, TRUE);

		$venue = new LocalistVenue();
		return $venue->parseVenue($venueDecoded);
	}

	
	/**
	 * Generate a link to the event using the event's URL segment
	 * @return string
	 */
	public function Link(){
		$calendar = LocalistCalendar::get()->First();
		$link = $calendar->Link().'event/'.$this->URLSegment;
		return $link;
	}
	
	/**
	 * Generate a list events similar to the current event. Randomly selects based on tags they have in common.
	 * @param int $limit 
	 * @return int
	 */
	public function RelatedEvents($limit = 6){
		$calendar = LocalistCalendar::get()->First();

		if($this->Types && $this->Types->First()){
			$curEventTypes = $this->Types;
			$randEventType = $curEventTypes[array_rand($curEventTypes->toArray())];
			//print_r($randEventType->Title);
			$relatedEvents = $calendar->EventList( $days = '200', $startDate = null, $endDate = null, $venue = null, $keyword = null, $type = $randEventType->ID);
			
			return $relatedEvents->Limit($limit);
		}else{
			return false;
		}
	}

	/**
	 * Returns a parsed facebook event link based on the event's Facebook Event ID.
	 * @return string
	 */
	public function ParsedFacebookEventLink(){
		$eventID = $this->FacebookEventLink;
		$facebookUrlPrefix = 'https://facebook.com/events/';
		$facebookUrl = $facebookUrlPrefix.$eventID;
		return $facebookUrl;
	}

	/**
	 * Function that helps us inject Google Maps API call in Page.ss at the bottom of the doc.
	 * @return boolean
	 */
	public function UsesGoogleMaps(){
		return true;
	}



}