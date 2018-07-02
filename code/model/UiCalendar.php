<?php

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\LabelField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\FieldType\DBDatetime;

use SilverStripe\Core\Convert;
use SilverStripe\ORM\FieldType\DBVarchar;

class UiCalendar extends Page {

	private static $db = array(
		'EventTypeFilterID'       => 'Int',
		'DepartmentFilterID'      => 'Int',
		'VenueFilterID'           => 'Int',
		'GeneralInterestFilterID' => 'Int',

		'SearchTerm' => 'Text',

		'FeaturedEvent1ID' => 'Int',
		'FeaturedEvent2ID' => 'Int',
		'FeaturedEvent3ID' => 'Int',
		'FeaturedEvent4ID' => 'Int',

	);

	private static $has_one = array(

	);

	private static $icon = 'ac-json-events/images/calendar-file.png';

	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$types      = $this->TypeList();
		$typesArray = $types->map();

		$departments      = $this->DepartmentList();
		$departmentsArray = $departments->map();

		$venues      = $this->VenuesList();
		$venuesArray = $venues->map();

		$genInterests      = $this->GeneralInterestList();
		$genInterestsArray = $genInterests->map();

		//print_r($genInterestsArray);

		$typeListBoxField = new DropdownField('EventTypeFilterID', 'Filter the calendar by this UiCalendar event type:', $typesArray);
		$typeListBoxField->setEmptyString('(No Filter)');

		$departmentDropDownField = new DropdownField('DepartmentFilterID', 'Filter the calendar by this UiCalendar department', $departmentsArray);
		$departmentDropDownField->setEmptyString('(No Filter)');

		$venueDropDownField = new DropdownField('VenueFilterID', 'Filter the calendar by this UiCalendar Venue', $venuesArray);
		$venueDropDownField->setEmptyString('(No Filter)');

		$genInterestDropDownField = new DropdownField('GeneralInterestFilterID', 'Filter the calendar by this UiCalendar General Interest', $genInterestsArray);
		$genInterestDropDownField->setEmptyString('(No Filter)');

		$fields->addFieldToTab('Root.Main', $typeListBoxField, 'Content');
		$fields->addFieldToTab(' Root.Main', $departmentDropDownField, 'Content');
		$fields->addFieldToTab(' Root.Main', $venueDropDownField, 'Content');
		$fields->addFieldToTab(' Root.Main', $genInterestDropDownField, 'Content');
		
		$fields->removeByName('Metadata');

		$events = $this->EventList();

		if ($events->First()) {
			$eventsArray = $events->map();

			$fields->addFieldToTab(' Root.Main', new LabelField('FeaturedEventFieldLabel', 'If no featured events are selected below, events marked at "Featured" in UiCalendar will be used.'));

			$featuredEvent1Field = new DropdownField("FeaturedEvent1ID", "Featured Event 1", $eventsArray);
			$featuredEvent1Field->setEmptyString('(No Event)');
			$fields->addFieldToTab('Root.Main', $featuredEvent1Field);

			$featuredEvent2Field = new DropdownField("FeaturedEvent2ID", "Featured Event 2", $eventsArray);
			$featuredEvent2Field->setEmptyString('(No Event)');
			$fields->addFieldToTab('Root.Main', $featuredEvent2Field);

			$featuredEvent3Field = new DropdownField("FeaturedEvent3ID", "Featured Event 3", $eventsArray);
			$featuredEvent3Field->setEmptyString('(No Event)');
			$fields->addFieldToTab('Root.Main', $featuredEvent3Field);

			$featuredEvent4Field = new DropdownField("FeaturedEvent4ID", "Featured Event 4", $eventsArray);
			$featuredEvent4Field->setEmptyString('(No Event)');
			$fields->addFieldToTab('Root.Main', $featuredEvent4Field);

		}

		return $fields;
	}

	public static function getOrCreate(){
		$calendar = UiCalendar::get()->First();

		if($calendar){
			return $calendar;
		}
		return UiCalendar::create();
	}

	public function Link($action = null){
		if($this->isInDB()){
			//return full calendar link if it exists in db
			return parent::Link();
		}

		if ($this->EventTypeFilterID != 0) {

			$primaryFilterTypeID = $this->EventTypeFilterID;
		}
		if ($this->DepartmentFilterID != 0) {
			$departmentFilterID = $this->DepartmentFilterID;
		}
		if ($this->VenueFilterID != 0) {
			$venueFilterID = $this->VenueFilterID;
		}
		if ($this->GeneralInterestFilterID != 0) {
			$genInterestFilterID = $this->GeneralInterestFilterID;
		}			

		if (isset($primaryFilterTypeID)) {
			$type = $this->getTypeByID($primaryFilterTypeID);
			//print_r($primaryFilterTypeID);
			return $type->UiCalendarLink;
		}
		if (isset($departmentFilterID)) {
			$type = $this->getTypeByID($departmentFilterID);
			return $type->Link();
		}
		if (isset($venueFilterID)) {
			$venue = $this->getVenueByID($venueFilterID);
			return $venue->Link();
		}
		if (isset($genInterestFilterID )) {
			$type = $this->getGeneralInterestByID($genInterestFilterID);
			//print_r($genInterestFilterID);
			return $type->Link();
		}

	}
	/**
	 * Generates an ArrayList of Featured Events by using the calendar's FeaturedEvent IDs.
	 * TODO: Check for the existence of the events in the API first before pushing them to the
	 * ArrayList.
	 * TODO: Make sure the event has upcoming dates before pushing it into the ArrayList
	 * @return ArrayList
	 */
	public function FeaturedEvents() {

		$events         = $this->EventList();
		$featuredEvents = new ArrayList();

		//Get featured events from SilverStripe if there are any.

		if ($this->FeaturedEvent1ID != 0) {
			if ($this->SingleEvent($this->FeaturedEvent1ID)) {
				$featuredEvents->push($this->SingleEvent($this->FeaturedEvent1ID));
			}
		}
		if ($this->FeaturedEvent2ID != 0) {
			if ($this->SingleEvent($this->FeaturedEvent2ID)) {
				$featuredEvents->push($this->SingleEvent($this->FeaturedEvent2ID));
			}
		}
		if ($this->FeaturedEvent3ID != 0) {
			if ($this->SingleEvent($this->FeaturedEvent3ID)) {
				$featuredEvents->push($this->SingleEvent($this->FeaturedEvent3ID));
			}
		}
		if ($this->FeaturedEvent4ID != 0) {
			if ($this->SingleEvent($this->FeaturedEvent4ID)) {
				$featuredEvents->push($this->SingleEvent($this->FeaturedEvent4ID));
			}
		}

		//If there aren't any featured events selected in SilverStripe, fall back on events marked as featured in UiCalendar.
		if ((isset($featuredEvents)) && ($featuredEvents->count() > 0)) {
			return $featuredEvents;
		} else if (isset($events)) {
			foreach ($events as $event) {
				if ($event->Featured == 'true') {
					$featuredEvents->push($event);
				}
			}
		}

		if ($featuredEvents->First()) {
			return $featuredEvents;
		} else {
			return false;
		}

	}

	/**
	 * Returns a Calendar Widget for the template.
	 * @return CalendarWidget
	 */

	public function CalendarWidget() {
		$calendar = UiCalendarWidget::create($this);
		return $calendar;
	}

	/**
	 * Returns an ArrayList of Trending Tags sorted by popularity.
	 * @return ArrayList
	 */
	public function TrendingTags() {
		// $events       = $this->EventList();
		// $tags         = array();
		// $localistTags = new ArrayList();

		// if (isset($events) && $events->First()) {
		// 	foreach ($events as $event) {

		// 		foreach ($event->Tags as $eventTag) {
		// 			if (isset($tags[$eventTag->Title])) {
		// 				$tags[$eventTag->Title] = $tags[$eventTag->Title]+1;
		// 			} else {
		// 				$tags[$eventTag->Title] = 0;
		// 			}
		// 		}
		// 	}

		// 	arsort($tags);

		// 	foreach ($tags as $key => $tag) {
		// 		$localistTag        = new UiCalendarTag();
		// 		$localistTag->Title = $key;
		// 		$localistTags->push($localistTag);

		// 	}

		// 	return $localistTags;
		// } else {
		// 	return false;
		// }

	}

	/**
	 * Returns an ArrayList of Trending Types sorted by popularity.
	 * @return ArrayList
	 */
	public function TrendingTypes() {
		$events = $this->EventList();
		$types  = array();

		if (isset($events) && $events->First()) {
			$localistEventTypes = new ArrayList();
			foreach ($events as $event) {
				if ($event->Types && $event->Types->First()) {
					foreach ($event->Types as $eventType) {
						if (isset($types[$eventType->ID])) {
							$types[$eventType->ID] = $types[$eventType->ID]+1;
						} else {
							$types[$eventType->ID] = 0;
						}
					}
				}
			}

			arsort($types);
			foreach ($types as $key => $type) {

				$localistEventType = $this->getTypeByID($key);
				$localistEventTypes->push($localistEventType);
			}

			return $localistEventTypes;
		} else {
			return false;
		}
	}

	public function requestAllPages($feedURL, $resourceName) {
		$page         = 1;
		$pp           = 100;
		$info         = $this->getJson($feedURL.'?pp='.$pp.'&page='.$page);
		$fullPageList = $info;

		if (isset($info['page']['total'])) {
			$numOfPages = $info['page']['total'];
			for ($page; $page <= $numOfPages; $page++) {
				$thisPage = $this->getJson($feedURL.'?pp='.$pp.'&page='.$page);
				foreach ($thisPage[$resourceName] as $key => $value) {
					array_push($fullPageList[$resourceName], $thisPage[$resourceName][$key]);
				}
				if ($page > 999) {
					//failsafe for infinite loops
					break;
				}
			}
		} else {
			return $fullPageList;
		}

		return $fullPageList;
	}

	/**
	 * Returns an ArrayList of all venues that are coming through our main EventList function
	 * @return ArrayList
	 */
	public function ActiveVenueList() {
		$activeEvents = $this->EventList();
		$venuesList   = new ArrayList();

		foreach ($activeEvents as $key => $parsedEvent) {

			if ($parsedEvent->Venue->ID != 0) {
				$venuesList->push($parsedEvent->Venue);
			}
		}

		$venuesList->removeDuplicates();

		return $venuesList;
	}

	public function VenuesList() {
		$feedURL      = UICALENDAR_FEED_URL.'/views/places_api.json?display_id=places';
		$venuesList   = new ArrayList();
		// print_r($feedURL);
		$venuesDecoded = $this->getJson($feedURL);
		//$venuesDecoded = $this->requestAllPages($feedURL, $resourceName);
		$venuesArray   = $venuesDecoded;
		//print_r($venuesArray);

		if (isset($venuesArray)) {
			foreach ($venuesArray['places'] as $venue) {

				$UiVenue = new UiCalendarVenue();
				$UiVenue  = $UiVenue->parseVenue($venue);
				$venuesList->push($UiVenue);
			}
		}

		return $venuesList;
	}

	/**
	 * Returns an ArrayList of all UiCalendarEventTypes based on the events coming through EventList()
	 * @return ArrayList
	 */
	public function TypeList() {

		$resourceName = 'event_types';
		$feedURL      = UICALENDAR_FEED_URL.'/views/filters_api.json?display_id=filters';

		$typesList = new ArrayList();

		$typesDecoded = $this->getJson($feedURL);
		//$typesDecoded = $this->requestAllPages($feedURL, $resourceName);
		$typesArray = $typesDecoded[$resourceName];
		//print_r($typesArray);
		if (isset($typesArray)) {
			foreach ($typesArray as $type) {
				$localistType = new UiCalendarEventType();
				$localistType = $localistType->parseType($type);
				$typesList->push($localistType);
			}
		}

		return $typesList;
	}

	public function DepartmentList() {
		$cache   = new SimpleCache();
		$feedURL      = UICALENDAR_FEED_URL.'/views/filters_api.json?display_id=filters';

		$departmentsList = new ArrayList();

		$rawFeed            = $cache->get_data($feedURL, $feedURL);
		$departmentsDecoded = json_decode($rawFeed, TRUE);

		if (isset($departmentsDecoded['departments'])) {
			$departmentsArray = $departmentsDecoded['departments'];
		}

		if (isset($departmentsArray)) {
			foreach ($departmentsArray as $department) {
				$localistDepartment = new UiCalendarEventType();
				$localistDepartment = $localistDepartment->parseType($department);
				$departmentsList->push($localistDepartment);
			}
		}

		return $departmentsList;

	}

	public function GeneralInterestList() {

		$cache   = new SimpleCache();
		$feedURL      = UICALENDAR_FEED_URL.'/views/filters_api.json?display_id=filters';

		$genInterestsList = new ArrayList();

		$rawFeed             = $cache->get_data($feedURL, $feedURL);
		$genInterestsDecoded = json_decode($rawFeed, TRUE);

		if (isset($genInterestsDecoded['event_general_interest'])) {
			$genInterestsArray = $genInterestsDecoded['event_general_interest'];
		}

		//print_r($genInterestsArray);
		if (isset($genInterestsArray)) {
			foreach ($genInterestsArray as $genInterest) {
				$localistGenInterest = new UiCalendarEventType();
				$localistGenInterest = $localistGenInterest->parseType($genInterest);
				$genInterestsList->push($localistGenInterest);
			}
		}

		//print_r($genInterestsList);

		return $genInterestsList;

	}

	/**
	 * Finds a specific event type by checking the master TypeList() and matching the ID against
	 * all types.
	 * TODO: More effecient way to do this? Through the API?
	 * @param int $id
	 * @return UiCalendarEventType
	 */
	public function getTypeByID($id) {
		$types = $this->TypeList();

		foreach ($types as $type) {
			if ($type->ID == $id) {
				return $type;
			}
		}

		return false;
	}
	public function getGeneralInterestByID($id) {
		$types = $this->GeneralInterestList();

		foreach ($types as $type) {
			if ($type->ID == $id) {
				return $type;
			}
		}

		return false;
	}
	public function getTagByID($id) {
		$types = $this->TagList();

		foreach ($types as $type) {
			if ($type->ID == $id) {
				return $type;
			}
		}

		return false;
	}
	public function getVenueByID($id) {
		$venues = $this->ActiveVenueList();

		foreach ($venues as $venue) {
			if (isset($venue)) {
				if ($venue->ID == $id) {
					return $venue;
				}
			}
		}
		return false;

	}

	public function getTodayEvents() {
		$start = sfDate::getInstance();

		$events = $this->EventList('year', $start->format('Y-m-d'), $start->add(1)->format('Y-m-d'));
		return $events;
	}

	public function getWeekendEvents() {
		$start = sfDate::getInstance();

		if ($start->format('w') == sfTime::SATURDAY) {
			$start->yesterday();
		} elseif ($start->format('w') != sfTime::FRIDAY) {
			$start->nextDay(sfTime::FRIDAY);
		}
		$end = sfDate::getInstance($start)->nextDay(sfTime::SUNDAY);

		$startDate = $start->format('Y-m-d');
		$endDate   = $end->format('Y-m-d');

		$events = $this->EventList('year', $startDate, $endDate);
		return $events;
	}

	public function getMonthEvents() {
		$startDate = sfDate::getInstance()->firstDayOfMonth()->format('Y-m-d');
		$endDate   = sfDate::getInstance($this->startDate)->finalDayOfMonth()->format('Y-m-d');

		$events = $this->EventList('year', $startDate, $endDate);
		return $events;
	}

	/**
	 * Produces a list of Events based on a number of factors, used in templates
	 * and as a helper function in this class and others.
	 *
	 * @param int $days
	 * @param string $startDate
	 * @param string $endDate
	 * @param int $venue
	 * @param string $keyword
	 * @param int $type
	 * @param boolean $distinct
	 * @return ArrayList
	 */

	public function EventList(
		$days = 'threemonths',
		$startDate = null,
		$endDate = null,
		$venue = null,
		$keyword = null,
		$type = null,
		$distinct = 'true',
		$enableFilter = true,
		$searchTerm = null,
		$perPage = 100
	) {
		if ($enableFilter) {
			if ($this->EventTypeFilterID != 0) {
				$primaryFilterTypeID = $this->EventTypeFilterID;
			}
			if ($this->DepartmentFilterID != 0) {
				$departmentFilterID = $this->DepartmentFilterID;
			}
			if ($this->VenueFilterID != 0) {
				$venueFilterID = $this->VenueFilterID;
			}
			if ($this->GeneralInterestFilterID != 0) {
				$genInterestFilterID = $this->GeneralInterestFilterID;
			}
			if ($this->SearchTerm != '') {

				$searchTerm = $this->SearchTerm;
			}
		}

		$feedParams = '';

		// if (isset($searchTerm)) {
		// 	$feedParams = '/search';
		// }

		if(isset($days)){
			$feedParams .= '/views/events_api.json?display_id='.$days;
		}else{
			$feedParams .= '/views/events_api.json?display_id=events';
		}
		

		$startDateSS = new DBDatetime();
		$endDateSS   = new DBDatetime();

		if (isset($startDate)) {
			$startDateSS->setValue($startDate);
			$feedParams .= '&filters[startdate][value][date]='.$startDateSS->format('m-d-Y');
		}
		if (isset($endDate)) {
			$endDateSS->setValue($endDate);
			$feedParams .= '&filters[enddate][value][date]='.$endDateSS->format('m-d-Y');
		}

		if (isset($venue)) {
			$feedParams .= '&filters[place]='.$venue;
		}

		if (!isset($searchTerm)) {
			if (isset($keyword)) {
				$feedParams .= '&filters[keywords]'.$keyword;
			}
		}
		if (isset($type)) {
			$feedParams .= '&filters[types]='.$type;
		}

		if (isset($primaryFilterTypeID)) {
			$feedParams .= '&filters[types]='.$primaryFilterTypeID;
		}

		if (isset($departmentFilterID)) {
			$feedParams .= '&filters[department]='.$departmentFilterID;
		}
		if (isset($genInterestFilterID)) {
			$feedParams .= '&filters[interests]='.$genInterestFilterID;
		}

		if (isset($venueFilterID)) {
			$feedParams .= '&filters[venue]='.$venueFilterID;
		}
		if (isset($searchTerm)) {
			$feedParams .= '&filters[keywords]='.$searchTerm;
		}
		if (isset($perPage)) {
			$feedParams .= '&items_per_page='.$perPage;
		}
		// $feedParams .= '&match=all&distinct='.$distinct;
		$feedURL = UICALENDAR_FEED_URL.$feedParams;
	//print_r($feedURL.'<br />');
		//$feedURL = urlencode($feedURL);
		

		$eventsList    = new ArrayList();
		$eventsDecoded = $this->getJson($feedURL);

		if (isset($eventsDecoded['events'])) {
			$eventsArray = $eventsDecoded['events'];
			foreach ($eventsArray as $event) {
				if (isset($event)) {
					$localistEvent = new UiCalendarEvent();

					$eventsList->push($localistEvent->parseEvent($event['event']));
				}
			}
			return $eventsList;
		}

	}
	public function EventListLimited($number = 3){
		return $this->EventList(
			$days = 'threemonths',
			$startDate = null,
			$endDate = null,
			$venue = null,
			$keyword = null,
			$type = null,
			$distinct = 'true',
			$enableFilter = true,
			$searchTerm = null,
			$perPage = $number
		);
	}
	public function EventListByDate($date) {
		$start  = sfDate::getInstance($date);
		$events = $this->EventList(null, $start->format('Y-m-d'), $start->add(1)->format('Y-m-d'));
		return $events;
	}
	/**
	 * Returns an ArrayList of events filtered by specified tag
	 * @param string $tag
	 * @return ArrayList
	 */

	public function EventListByTag($tag) {
		$tagFiltered = urlencode($tag);
		$events      = $this->EventList(
			$days = 'year',
			$startDate = null,
			$endDate = null,
			$venue = null,
			$keyword = $tagFiltered,
			$type = null,
			$distinct = 'true',
			$enableFilter = true,
			$searchTerm = null
		);
		return $events;

	}
	/**
	 * Returns an ArrayList of randomized events 
	 * @param none
	 * @return Randomized ArrayList
	 */

	public function EventListRandom() {

		//$randomEvents = new ArrayList();
		$events = $this->EventList();
		$eventsArray = $events->toArray();

		shuffle($eventsArray);

		$eventsArrayList = new ArrayList($eventsArray);

		//Debug::show($eventsArray);

		return $eventsArrayList;

	}

	/**
	 * Returns an ArrayList of events filtered by specified search term
	 * @param string $term
	 * @return ArrayList
	 */

	public function EventListBySearchTerm($term) {
		$termFiltered = urlencode($term);

		$events = $this->EventList(
			$days = null,
			$startDate = null,
			$endDate = null,
			$venue = null,
			$keyword = null,
			$type = null,
			$distinct = 'true',
			$enableFilter = true,
			$searchTerm = $term
		);
		return $events;

	}
	/**
	 * Gets a single event from the UiCalendar Feed based on ID.
	 * @param int $id
	 * @return UiCalendarEvent
	 */

	public function SingleEvent($id, $mustBeUpcoming = true) {
		if (!isset($id) || $id == 0) {
			return false;
		}

		$feedParams = '/node/'.$id.'.json';
		$feedURL    = UICALENDAR_FEED_URL.$feedParams;
		
		$eventsDecoded = $this->getJson($feedURL);
		//print_r($eventsDecoded);
		$event         = $eventsDecoded;
		if (isset($event)) {
			$localistEvent = new UiCalendarEvent();
			$parsedEvent   = $localistEvent->parseEvent($event);
			//If we're only looking for an event with upcoming dates, check the event's Dates and return the event if there are any.
			if ($mustBeUpcoming) {
				//print_r($parsedEvent);
				if ($parsedEvent->Dates->count() > 0) {

					return $parsedEvent;
				} else {
					return false;
				}
			} else {
				return $parsedEvent;
			}
		}
		return false;
	}

}
