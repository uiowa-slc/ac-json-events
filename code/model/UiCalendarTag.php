<?php

use SilverStripe\ORM\DataObject;
class UiCalendarTag extends DataObject {

	private static $db = array(
		"Title"		=> "Varchar(255)"
	);

	public function parseTag($rawType){
		$localistType = new UiCalendarTag();
		$localistType->ID = $rawType['id'];

        if($rawType['name'] == 'IP'){
            $localistType->Title = 'International Programs';
        }else{
            $localistType->Title = $rawType['name'];
        }

		//$localistType->UiCalendarLink = $rawType['localist_url'];
		// $localistType->UiCalendarLink = UICALENDAR_BASE.'search/events/?keywords='.$localistType->ID;
		return $localistType;

	}

    public function Link($action = null) {
        $calendar = UiCalendar::getOrCreate();

        $urlSeg = $this->ID;

        if($calendar->IsInDB()){
            $link = $calendar->getAbsoluteLiveLink(false) . 'tag/' . $urlSeg;
            return $link;
        }

        return 'https://events.uiowa.edu';

    }

	
/*public function Events() {
		$calendar = UiCalendar::getOrCreate();
		$events = $calendar->EventList(200, $startDate = NULL, $endDate = NULL, $venue = $this->ID);
		$eventsAtPlaceList = new ArrayList();

		foreach($events as $event) {
			$eventsAtPlaceList->push($event);
		}		

		return $eventsAtPlaceList;   
	}*/
	/*public function Link(){
		$calendar = UiCalendar::getOrCreate();
		$link = $calendar->Link().'event/'.$this->ID;
		return $link;
	}*/

}
