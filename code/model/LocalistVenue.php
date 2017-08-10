<?php
class LocalistVenue extends DataObject {

	private static $db = array(
		"Title"		=> "Varchar(255)",
		"Content"	=> "HTMLText",
		"ImageURL"	=> "Text",
		"LocalistLink" => "Text",
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
			$this->Content = $venueDecoded['description_text'];
			$this->ImageURL = $venueDecoded['photo_url'];
			$this->LocalistLink = $venueDecoded['localist_url'];
			$this->WebsiteLink = $venueDecoded['url'];
			$this->Latitude = $venueDecoded['geo']['latitude'];
			$this->Longitude = $venueDecoded['geo']['longitude'];
			$this->Address = $venueDecoded['address'];

			return $this;
		}
	}

	
	public function Events() {
		$calendar = LocalistCalendar::getOrCreate();
		$events = $calendar->EventList();
		$eventsAtPlaceList = new ArrayList();

		foreach($events as $event) {
			if($event->Venue->Title == $this->Title){
				$eventsAtPlaceList->push($event);
			}
		}		

		return $eventsAtPlaceList;   
	}

	/**
	 * Returns a link to the venue.
	 * @return type
	 */
	public function Link(){
		if($this->ID != 0) {
			$calendar = LocalistCalendar::getOrCreate();
			$link = $calendar->Link().'venue/'.$this->ID;
			return $link;
		}else{
			return false;
		}
	}

	/**
	 * Returns a formatted link for directions.
	 * @return type
	 */
	public function DirectionsLink(){
		return "http://maps.apple.com/?q=".$this->Address;
	}
}
