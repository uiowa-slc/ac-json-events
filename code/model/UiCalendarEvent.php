<?php

use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\Core\Config\Config;
use SilverStripe\CMS\Model\SiteTree;

class UiCalendarEvent extends Page {
    /**
     * @config
     */

    //Performance issues if you enable this. Only enable on sites that NEED nice URLs.
	private static $use_nice_links = false;

	private static $hide_ancestor = 'UiCalendarEvent';

	/**
	 * Convert an event in an array format (from UiCalendar JSON Feed) to a UiCalendarEvent
	 * @param array $rawEvent
	 * @return UiCalendarEvent
	 */
	public function parseEvent($rawEvent) {

		$image = new UiCalendarImage();
		$this->Venue = $this->getVenueFromRaw($rawEvent);
		//print_r($rawEvent['photo_url']);
		 
		if (isset($rawEvent['media'][0]['original_image'])) {
			$image->URL = $rawEvent['media'][0]['original_image'];
			$image->ThumbURL = $rawEvent['media'][0]['large_image'];
			$image->RectangleURL = $rawEvent['media'][0]['events_site_featured_image'];
		} else {
			$themeDir = $this->ThemeDir();

			if ($this->Venue && $this->Venue->ImageURL != '') {

				$image->URL = $this->Venue->ImageURL;

			} else {
				$image->URL = null;

			}
		}

		$this->Dates = new ArrayList();

		$this->ID = $rawEvent['id'];
		$this->Title = $rawEvent['title'];
		$this->EventTitle = $rawEvent['title'];
		$this->URLSegment = $rawEvent['urlname'];
		$this->Location = $this->ParseLocation($rawEvent['room_number']);
		$this->Dates = $this->getUpcomingDatesFromRaw($rawEvent);

		$firstDateTime = new DBDatetime();
		$firstDateTimeObj = $this->Dates->First();

		if (isset($firstDateTimeObj->StartDateTime)) {
			$firstDateTime->setValue($firstDateTimeObj->obj('StartDateTime')->getValue());

			$this->FirstStartDateTime = $firstDateTime;
		}

		if(isset($rawEvent['description']))
			$this->Content = $rawEvent['description'];
		if(isset($rawEvent['description_text']))
		$this->SummaryContent = $rawEvent['description_text'];
		$this->Tags = $this->getTagsFromRaw($rawEvent);
		$this->Types = $this->getTypesFromRaw($rawEvent);
		$this->Interests = $this->getInterestsFromRaw($rawEvent);
		$this->Image = $image;
		$this->UiCalendarLink = $rawEvent['events_site_url'];
		$this->AfterClassLink = AFTERCLASS_BASE . 'event/' . $this->ID;
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

	public function isLateNight(){
		return $this->HasInterest("Late Night Programs");
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

			// $dateTime->StartDateTime = new DBDatetime();
			// $dateTime->EndDateTime = new DBDatetime();

			$dateTime->setStartDateTime($eventInstances[$i]['event_instance']['start']);
            //print_r($dateTime);
			//print_r('end date: '.$dateTime->EndDateTime);
			// if (isset($eventInstances[$i]['event_instance']['end'])) {
			// 	$dateTime->setStartDateTime($eventInstances[$i]['event_instance']['end']);
			// }
			//print_r($dateTime->StartDateTime);

			if ((!$dateTime->obj('StartDateTime')->InPast()) || $dateTime->obj('StartDateTime')->IsToday()) {
				$eventInstancesArray->push($dateTime);
			}


		}

		return $eventInstancesArray;
	}

	/**
	 * Generate a list of tags from an event array from UiCalendar
	 * @param array $rawEvent
	 * @return ArrayList
	 */
	private function getTagsFromRaw($rawEvent) {

		if (isset($rawEvent['keywords'])) {
			$tagsRaw = $rawEvent['keywords'];
			$tags = new ArrayList();
			foreach ($tagsRaw as $tagRaw) {
				$tag = new UiCalendarTag();
				$tag->ID = $tagRaw['id'];
				$tag->Title = $tagRaw['name'];
				$tags->push($tag);
			}
			return $tags;
		}
		return false;
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
	 * Generate a list of types from an event array from UiCalendar
	 * @param array $rawEvent
	 * @return ArrayList
	 */
	private function getInterestsFromRaw($rawEvent) {

		if (isset($rawEvent['filters']['event_general_interest'])) {
			$typesRaw = $rawEvent['filters']['event_general_interest'];
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


			if(isset($rawEvent['location_name'])){
				$venue = new UiCalendarVenue();

				//Temporararily use the title as a unique ID until we get venue ID from event list:

				if(isset($rawEvent['location_id'])){
					$venue->ID = $rawEvent['location_id'];
				}else{
					$venue->ID = SiteTree::generateURLSegment($rawEvent['location_name']);
				}

				//Temporarily replacing link function with a simple venue_url until we get venue ID from the event list.
				// if(isset($rawEvent['venue_url'])){
				// 	$venue->Link = $rawEvent['venue_url'];
				// }
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
	public function Link($action = null) {
		$calendar = UiCalendar::get()->First();
		$niceLinks = Config::inst()->get('UiCalendarEvent', 'use_nice_links');
		if($niceLinks){
			$urlSeg = $this->URLSegment;
		}else{
			$urlSeg = $this->ID;
		}
		if($calendar){
			$link = $calendar->getAbsoluteLiveLink(false) . 'event/' . $urlSeg;
			return $link;
		}

		return $this->AfterClassLink;

	}

	/**
	 * Generate a link to download the event to a calendar using the event's URL segment.ics
	 * @return string
	 */
	public function CalendarLink() {
		$link = 'https://events.uiowa.edu/singleEvent/'.$this->ID;
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
			//print_r($curEventTypes);
			$randEventType = $curEventTypes[array_rand($curEventTypes->toArray())];
			//$randEventType = $curEventTypes[array_splice($randEventTypes, 1)];
			//print_r($randEventType->Title);

			$relatedEvents = $calendar->EventList(
				$days = 'threemonths',
				$startDate = null,
				$endDate = null,
				$venue = null,
				$keyword = null,
				$type = $randEventType->ID
			);

			// print_r($relatedEvents);

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
	 * Generate a list events similar to the current event. Randomly selects based on tags they have in common.
	 * @param int $limit
	 * @return int
	 */
	public function LocationRelatedEvents() {
		$calendar = UiCalendar::getOrCreate();

		if ($this->Venue) {

			$venue = $this->Venue;

			$relatedEvents = $calendar->EventList(
				$days = 'threemonths',
				$startDate = null,
				$endDate = null,
				$venue = $venue->ID,
				$keyword = null,
				$type = null
			);

			// print_r($relatedEvents);

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

	public function HasType($typeName){
		$eventTypes = $this->Types;
		foreach($eventTypes as $type){
			if($type->Title == $typeName){
				return true;
			}
		}

		return false;
	}
	public function HasInterest($typeName){
		$eventTypes = $this->Interests;

		foreach($eventTypes as $type){
			if($type->Title == $typeName){
				return true;
			}
		}

		return false;
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