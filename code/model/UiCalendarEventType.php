<?php

use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
class UiCalendarEventType extends DataObject {

	private static $db = array(
		"Title"		=> "Varchar(255)"
	);

	public function parseType($rawType){
		$localistType = new UiCalendarEventType();
		$localistType->ID = $rawType['id'];
		$localistType->Title = $rawType['name'];
		$localistType->UiCalendarLink = UICALENDAR_BASE.'search/events/?event_types='.$localistType->ID;
		return $localistType;
	}

	public function Link($action = null) {
        $calendar = UiCalendar::getOrCreate();

        $urlSeg = $this->ID;

        if($calendar->IsInDB()){
            $link = $calendar->getAbsoluteLiveLink(false) . 'type/' . $urlSeg;
            return $link;
        }

        return 'https://events.uiowa.edu';

    }

	public function EventList() {
		//echo "type: <br />";
		//print_r($this->ID);
		//echo "<br />";

		$calendar = UiCalendar::getOrCreate();

		//print_r($calendar);
		$events = $calendar->EventList(200, $startDate = NULL, $endDate = NULL, $venue = null, $keyword = null, $type = $this->ID);
		//print_r($events);

		$eventsAtTypeList = new ArrayList();
		//print_r($events);
		if(!isset($events)){
			return false;
		}

		foreach($events as $event) {
			$eventsAtTypeList->push($event);
		}		
 
		return $events;   
	}

}
