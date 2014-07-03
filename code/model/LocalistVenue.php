<?php
class LocalistVenue extends DataObject {

	private static $db = array(
		"Title" => "Varchar(255)",
		"Content" => "HTMLText",
		"ImageURL" => "Text",
		"LocalistLink" => "Text",
		"WebsiteLink" => "Text",
		
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

			return $this;
		}

	}

	public function EventList(){

	}
	/*public function Link(){
		$calendar = LocalistCalendar::get()->First();
		$link = $calendar->Link().'event/'.$this->ID;
		return $link;
	}*/

}
