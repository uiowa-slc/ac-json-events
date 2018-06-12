<?php

class UiCalendarEvent extends Page {
    /**
     * @config
     */

    //Performance issues if you enable this. Only enable on sites that NEED nice URLs.
	private static $use_nice_links = false;

	/**
	 * Convert an event in an array format (from UiCalendar JSON Feed) to a UiCalendarEvent
	 * @param array $rawEvent
	 * @return UiCalendarEvent
	 */
	public function parseEvent($rawEvent) {

		$image = new UiCalendarImage();
		$this->Venue = $this->getVenueFromRaw($rawEvent);
		//print_r($rawEvent['photo_url']);
		// print_r($rawEvent);
		if (isset($rawEvent['media'][0]['original_image'])) {
			$image->URL = $rawEvent['media'][0]['original_image'];
		} else {
			$themeDir = $this->ThemeDir();

			if ($this->Venue && $this->Venue->ImageURL != '') {

				$image->URL = $this->Venue->ImageURL;

			} else {
				$image->URL = $themeDir . '/images/UiCalendarEventPlaceholder.jpg';

			}
		}
		;
		$this->Dates = new ArrayList();

		$this->ID = $rawEvent['id'];
		$this->Title = $rawEvent['title'];
		$this->EventTitle = $rawEvent['title'];
		$this->URLSegment = $rawEvent['urlname'];
		// $this->Thumbnail = $rawEvent['photo_url'];
		//$this->Featured = $rawEvent['featured'];
		//$this->Cost = $rawEvent['ticket_cost'];
		$this->Location = $this->ParseLocation($rawEvent['room_number']);
		$this->Dates = $this->getUpcomingDatesFromRaw($rawEvent);

		$firstDateTime = new SS_Datetime();
		$firstDateTimeObj = $this->Dates->First();

		if (isset($firstDateTimeObj->StartDateTime)) {
			$firstDateTime->setValue($firstDateTimeObj->StartDateTime->getValue());

			$this->FirstStartDateTime = $firstDateTime;
		}

		// I recommend changing Content to $rawEvent['descritption_text'];
		if(isset($rawEvent['description']))
			$this->Content = $rawEvent['description'];
		if(isset($rawEvent['description_text']))
		$this->SummaryContent = $rawEvent['description_text'];
		//$this->Tags = $this->getTagsFromRaw($rawEvent);
		$this->Types = $this->getTypesFromRaw($rawEvent);
		$this->Image = $image;
		$this->UiCalendarLink = UICALENDAR_BASE.$rawEvent['urlname'];
		$this->AfterClassLink = AFTERCLASS_BASE . 'event/' . $this->URLSegment;
		$this->MoreInfoLink = $rawEvent['url'];
		//$this->FacebookEventLink = $rawEvent['facebook_id'];
		$this->ContactName = (isset($rawEvent['contact_name']) ? $rawEvent['contact_name'] : '');
		$this->ContactEmail = (isset($rawEvent['contact_email']) ? $rawEvent['contact_email'] : '');
		// $this->Sponsor = (isset($rawEvent['custom_fields']['sponsor']) ? $rawEvent['custom_fields']['sponsor'] : '');

		if (isset($venue['place']['name'])) {
			$this->VenueTitle = $venue['place']['name'];
		}

		// Debug::show($this->Image);
		return $this;

	}

	//Weird hack to get around the default DataObject getTitle
	public function getTitle() {
		return $this->EventTitle;
	}

	private function parseLocation($location) {
		if (is_numeric($location)) {
			return "Rm " . $location;
		} else {
			return $location;
		}
	}
	/**
	 * Get a list of upcoming dates for a single event by checking an individual event
	 * for instances from UiCalendar
	 * @param array $rawEvent
	 * @return ArrayList
	 */
	private function getUpcomingDatesFromRaw($rawEvent) {
		$eventInstances = $rawEvent['event_instances'];

		$eventInstancesArray = new ArrayList();

		foreach ($eventInstances as $i => $eventInstance) {
			$dateTime = new UiCalendarDatetime();

			//$allDayBoolean = new Boolean();

			if($eventInstances[$i]['event_instance']['all_day'] == 1){

				$dateTime->AllDay = 1;
			}else{
				$dateTime->AllDay = 0;
			}

			$dateTime->StartDateTime = new SS_Datetime();
			$dateTime->EndDateTime = new SS_Datetime();

			$dateTime->StartDateTime->setValue($eventInstances[$i]['event_instance']['start']);
			//print_r('end date: '.$dateTime->EndDateTime);
			if (isset($eventInstances[$i]['event_instance']['end'])) {
				$dateTime->EndDateTime->setValue($eventInstances[$i]['event_instance']['end']);
			}

			if ((!$dateTime->StartDateTime->InPast()) || $dateTime->StartDateTime->IsToday()) {
				$eventInstancesArray->push($dateTime);
			}

			//Debug::show($dateTime);
		}
		//print_r($eventInstancesArray);
		return $eventInstancesArray;
	}

	/**
	 * Generate a list of tags from an event array from UiCalendar
	 * @param array $rawEvent
	 * @return ArrayList
	 */
	private function getTagsFromRaw($rawEvent) {
		$tagsRaw = $rawEvent['tags'];
		$tags = new ArrayList();

		foreach ($tagsRaw as $tagRaw) {
			$tag = new UiCalendarTag();
			$tag->Title = $tagRaw;

			$tags->push($tag);
		}

		return $tags;
	}

	/**
	 * Generate a list of types from an event array from UiCalendar
	 * @param array $rawEvent
	 * @return ArrayList
	 */
	private function getTypesFromRaw($rawEvent) {

		if (isset($rawEvent['filters']['event_types'])) {
			$typesRaw = $rawEvent['filters']['event_types'];
			$types = new ArrayList();
			foreach ($typesRaw as $typeRaw) {
				$type = new UiCalendarEventType();
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
	 * @return UiCalendarVenue
	 */
	private function getVenueFromRaw($rawEvent) {
		if (isset($rawEvent['venue_id'])) {
			$id = $rawEvent['venue_id'];
			return $this->getVenueFromID($id);
		} else {
			// print_r($rawEvent);
			
			if(isset($rawEvent['location_name'])){
				$venue = new UiCalendarVenue();
				$venue->Title = $rawEvent['location_name'];
				$venue->Latitude = $rawEvent['geo']['latitude'];
				$venue->Longitude = $rawEvent['geo']['longitude'];
				if (isset($rawEvent['geo']['street'])) {
					$venue->Address = $rawEvent['geo']['street'] . ', ' . $rawEvent['geo']['city'] . ', ' . $rawEvent['geo']['state'] . ' ' . $rawEvent['geo']['zip'];
				}
				return $venue;
				}
			
		}
	}

	/**
	 * Retrieve a single venue from the UiCalendar Venue API by an ID number
	 * @param int $venueID
	 * @return UiCalendarVenue
	 */
	public function getVenueFromID($venueID) {
		$feedURL = UICALENDAR_FEED_URL . 'places/' . $venueID;
		$cache = new SimpleCache();

		$rawVenue = $cache->get_data($feedURL, $feedURL);
		$venueDecoded = json_decode($rawVenue, TRUE);

		$venue = new UiCalendarVenue();
		return $venue->parseVenue($venueDecoded);
	}

	/**
	 * Generate a link to the event using the event's URL segment
	 * @return string
	 */
	public function Link() {
		$calendar = UiCalendar::get()->First();
		$niceLinks = Config::inst()->get('UiCalendarEvent', 'use_nice_links');
		if($niceLinks){
			$urlSeg = $this->URLSegment;
		}else{
			$urlSeg = $this->ID;
		}
		if($calendar){
			$link = $calendar->Link() . 'event/' . $urlSeg;
			return $link;
		}
		
		return $this->AfterClassLink;
		
	}

	/**
	 * Generate a link to download the event to a calendar using the event's URL segment.ics
	 * @return string
	 */
	public function CalendarLink() {
		$link = $this->UiCalendarLink . '.ics';
		return $link;
	} //test

	/**
	 * Generate a list events similar to the current event. Randomly selects based on tags they have in common.
	 * @param int $limit
	 * @return int
	 */
	public function RelatedEvents() {
		$calendar = UiCalendar::getOrCreate();

		if ($this->Types && $this->Types->First()) {
			$curEventTypes = $this->Types;
			$randEventType = $curEventTypes[array_rand($curEventTypes->toArray())];
			//$randEventType = $curEventTypes[array_splice($randEventTypes, 1)];
			//print_r($randEventType->Title);

			$relatedEvents = $calendar->EventList(
				$days = '90',
				$startDate = null,
				$endDate = null,
				$venue = null,
				$keyword = null,
				$type = $randEventType->ID
			);

			if (isset($relatedEvents) && $relatedEvents->First()) {
				$relatedEvents = $relatedEvents->exclude('ID', $this->ID);
				return $relatedEvents;
			} else {
				return false;
			}

		} else {
			return false;
		}
	}

	/**
	 * Returns a parsed facebook event link based on the event's Facebook Event ID.
	 * @return string
	 */
	public function ParsedFacebookEventLink() {
		$eventID = $this->FacebookEventLink;
		$facebookUrlPrefix = 'https://facebook.com/events/';
		$facebookUrl = $facebookUrlPrefix . $eventID;
		return $facebookUrl;
	}

	/**
	 * Function that helps us inject Google Maps API call in Page.ss at the bottom of the doc.
	 * @return boolean
	 */
	public function UsesGoogleMaps() {
		return true;
	}

}

class UiCalendarEvent_Controller extends Page_Controller {


}