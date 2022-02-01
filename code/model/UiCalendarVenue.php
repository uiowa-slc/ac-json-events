<?php

use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
class UiCalendarVenue extends DataObject {

	private static $db = array(
		"Title"		=> "Varchar(255)",
		"Content"	=> "HTMLText",
		"ImageURL"	=> "Text",
		"UiCalendarLink" => "Text",
		"WebsiteLink" => "Text",
		"Latitude"	=> "Text",
		"Longitute"	=> "Text",
		"Address"	=> "Text"
	);

	public function parseVenue($venueDecoded){
		if(isset($venueDecoded['place'])){
			$venueDecoded = $venueDecoded['place'];
			$this->ID = $venueDecoded['id'];
			$this->Title = $venueDecoded['name'];
			// $this->Content = $venueDecoded['description_text'];
			//$this->ImageURL = $venueDecoded['photo_url'];
			// $this->UiCalendarLink = $venueDecoded['localist_url'];
			if(isset($venueDecoded['venue_url'])){
				$this->WebsiteLink = $venueDecoded['venue_url'];
			}
			
			$this->Latitude = $venueDecoded['geo']['latitude'];
			$this->Longitude = $venueDecoded['geo']['longitude'];
			$this->Address = $venueDecoded['geo']['street'].', '.$venueDecoded['geo']['city'].', '.$venueDecoded['geo']['state'].' '.$venueDecoded['geo']['zip'];

			return $this;
		}
	}

	
	public function Events() {
		$calendar = UiCalendar::getOrCreate();
		$events = $calendar->EventList();
		$eventsAtPlaceList = new ArrayList();

		foreach($events as $event) {
			if($event->Venue){
				if($event->Venue->Title == $this->Title){
					$eventsAtPlaceList->push($event);
				}				
			}

		}		

		return $eventsAtPlaceList;   
	}

	/**
	 * Returns a link to the venue. NOTE: We're just going to link to maps.uiowa.edu until venue ID is sent through the events feed properly. 
	 * @return type
	 */
	public function Link(){

		if($this->ID != 0) {
			$calendar = UiCalendar::getOrCreate();

			if($calendar->IsInDB()){
				$link = $calendar->getAbsoluteLiveLink(false).'venue/'.$this->ID;
			}else{
				$link = $this->UiCalendarLink;
			}
			
			return $link;
		}else{
			if($this->WebsiteLink){
				return $this->WebsiteLink;
			}
			return false;
		}
	}

	/**
	 * Returns a formatted link for directions.
	 * @return type
	 */
	public function DirectionsLink(){
		return "http://maps.google.com/?q=".$this->Address;
	}
}
