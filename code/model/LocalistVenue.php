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
			$this->PageList = Page::get();
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
		$calendar = LocalistCalendar::get()->First();
		$events = $calendar->EventList("events/?days=200&pp=50&distinct=true&venue_id=".$this->ID);
		$eventsAtPlaceList = new ArrayList();

		foreach($events as $event) {
			$eventsAtPlaceList->push($event);
		}		

		return $eventsAtPlaceList;   
	}
	/*public function Link(){
		$calendar = LocalistCalendar::get()->First();
		$link = $calendar->Link().'event/'.$this->ID;
		return $link;
	}*/

}
