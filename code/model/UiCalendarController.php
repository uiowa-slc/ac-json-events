<?php
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\FieldType\DBVarchar;
use SilverStripe\Core\Convert;


class UiCalendar_Controller extends PageController {


	/**
	 * An array of actions that can be accessed via a request. Each array element should be an action name, and the
	 * permissions or conditions required to allow the user to access it.
	 *
	 * <code>
	 * array (
	 *     'action', // anyone can access this action
	 *     'action' => true, // same as above
	 *     'action' => 'ADMIN', // you must have ADMIN permissions to access this action
	 *     'action' => '->checkAction' // you can only access this action if $this->checkAction() returns true
	 * );
	 * </code>
	 *
	 * @var array
	 */
	private static $allowed_actions = array(
		'event',
		'show',
		'monthjson',
		'tag',
		'type',
		'venue',
		'search',
		'interest',
		//legacy feed actions
		'feed',
		'prime'
	);

	/** URL handlers / routes
	 */
	private static $url_handlers = array(
		'event/$eventID'           => 'event',
		'show/$startDate/$endDate' => 'show',
		'monthjson/$ID'            => 'monthjson',
		'tag/$tag'                 => 'tag',
		'interest/$interest'       => 'interest',
		'type/$type'               => 'type',
		'venue/$venue'             => 'venue',
		'search'                   => 'search',
		//legacy feed urls:

		'feed/$Type' => 'Feed',
	);

	public function index(HTTPRequest $r) {

		$startDate = new DBDatetime();
		$endDate   = new DBDatetime();

		$startDate->setValue(date('Y-m-d'));
		$endDate->setValue(strtotime($startDate." +200 days"));

		$Data = array(

		);

		return $this->customise($Data)->renderWith(array('UiCalendar', 'Page'));

	}

	/**
	 * Controller function that renders a single event through a $url_handlers route.
	 * @param SS_HTTPRequest $request
	 * @return Controller
	 */
	public function event($request) {
		$eventID = addslashes($this->urlParams['eventID']);

		/* If we're using an event ID as a key. */
		if (is_numeric($eventID)) {
			$event = $this->SingleEvent($eventID);
			if($this->isInDB() && $event){
				return $this->customise($event)->renderWith(array('UiCalendarEvent', 'Page'));	
			}
		} else {

			/* Getting an event based on the url slug **EXPERIMENTAL ** */
			$events = $this->EventList();
			foreach ($events as $key => $e) {
				if ($e->URLSegment == $eventID) {
					//print_r($e->URLSegment);
					$singleEvent = $this->SingleEvent($e->ID);
					if ($singleEvent) {
						return $this->customise($singleEvent)->renderWith(array('UiCalendarEvent', 'Page'));
					}
				}
			}

		}
		//echo "hello";
		//$this->Redirect(UICALENDAR_BASE.'event/'.$eventID);
		//return $this->httpError( 404, 'The requested event can\'t be found in the events.uiowa.edu upcoming events list.');
		return $this->httpError( 404);
	}

	/**
	 * Controller function that filters the calendar by a start+end date or a human-readable string like 'weekend'
	 * @param SS_HTTPRequest $request
	 * @return Controller
	 */
	public function show($request) {

		$dateFilter = addslashes($this->urlParams['startDate']);

		switch ($dateFilter) {
			case 'weekend':
				$events       = $this->getWeekendEvents();
				$filterHeader = 'This weekend:';
				break;
			case 'today':
				$events       = $this->getTodayEvents();
				$filterHeader = 'Today:';
				break;
			case 'month':
				$events       = $this->getMonthEvents();
				$filterHeader = 'This month:';
				break;
			default:
				$startDate = new DBDatetime();
				$startDate->setValue(addslashes($this->urlParams['startDate']));

				$endDate = new DBDatetime();

				if(isset($this->urlParams['endDate'])){
					$endDate->setValue(addslashes($this->urlParams['endDate']));
				}elseif(isset($this->urlParams['startDate'])){
					$endDate = $startDate;
				}
				
				$filterHeader = $startDate->format('eeee, MMMM d');

				if ($endDate->getValue() && ($endDate->getValue() != $startDate->getValue())) {
					$filterHeader .= ' to '.$endDate->format('eeee, MMMM d');
				}

				$events = $this->EventList(null, $startDate->format('Y-MM-dd'), $endDate->format('Y-MM-dd'));

		}

		$Data = array(
			'FilterEventList'    => $events,
			'FilterHeader' => $filterHeader,
		);
		return $this->customise($Data)->renderWith(array('UiCalendar', 'Page'));

	}

	/**
	 * Controller Function that renders a filtered Event List by a UiCalendar tag or keyword.
	 * @param SS_HTTPRequest $request
	 * @return Controller
	 */
	public function tag($request) {
		$tagName      = addslashes($this->urlParams['tag']);
		$tagName = urldecode($tagName);
		$events       = $this->EventList('year', null, null, null, $tagName);
		$filterHeader = 'Events tagged as "'.$tagName.'":';

		$Data = array(
			'Title'        => $tagName.' | '.$this->Title,
			'FilterEventList'    => $events,
			'FilterHeader' => $filterHeader,
		);

		return $this->customise($Data)->renderWith(array('UiCalendar', 'Page'));
	}
	public function interest($request) {
		$interestID = addslashes($this->urlParams['interest']);
		$interest   = $this->getGeneralInterestByID($interestID);
		//echo "interest: <br />";
		//print_r($interest->ID);
		//echo "<br />";
		$events = $this->EventList('year', null, null, null, null, null, null, null, null, 100, $interest->ID);

		$filterHeader = 'Events tagged as "'.$interest->Title.'":';

		$Data = array(
			'Title'        => $interest->Title.' | '.$this->Title,
			'FilterEventList'    => $events,
			'FilterHeader' => $filterHeader,
		);

		return $this->customise($Data)->renderWith(array('UiCalendar', 'Page'));
	}
	public function type($request) {
		$typeID = addslashes($this->urlParams['type']);
		$type   = $this->getTypeByID($typeID);
		//echo "type: <br />";
		//print_r($type->ID);
		//echo "<br />";
		$events = $this->EventList('year', null, null, null, null, $type->ID);

		$filterHeader = 'Events tagged as "'.$type->Title.'":';

		$Data = array(
			'Title'        => $type->Title.' | '.$this->Title,
			'FilterEventList'    => $events,
			'FilterHeader' => $filterHeader,
		);

		return $this->customise($Data)->renderWith(array('UiCalendar', 'Page'));
	}

	public function venue($request) {
		$venueID = addslashes($this->urlParams['venue']);
		$venue   = $this->getVenueByID($venueID);

		if (isset($venue)) {
			$events = $this->EventList('year', null, null, $venue->ID);

			$filterHeader = 'Events listed at '.$venue->Title.':';

			$Data = array(
				'Title'        => $venue->Title.' | '.$this->Title,
				'Venue'        => $venue,
				'FilterEventList'    => $events,
				'FilterHeader' => $filterHeader,
			);

			return $this->customise($Data)->renderWith(array('UiCalendarVenue', 'UiCalendar', 'Page'));
		} else {
			return $this->httpError(404, 'The requested venue can\'t be found in the events.uiowa.edu upcoming events list.');
		}
	}

	public function search($request) {
		$term = $request->getVar('term');
		//print_r('term: '.$term);
		$events = $this->EventList('year', null, null, null, null, null, 'true', false, $term);

		$data = array(
			"Results" => $events,
			"Term"    => $term,
		);

		return $this->customise($data)->renderWith(array('UiCalendar_search', 'Page'));

	}

	public function monthjson($r) {
		if (!$r->param('ID')) {
			return false;
		}

		$this->startDate = sfDate::getInstance(CalendarUtil::get_date_from_string($r->param('ID')));
		$this->endDate   = sfDate::getInstance($this->startDate)->finalDayOfMonth();

		$json    = array();
		$counter = clone$this->startDate;
		while ($counter->get() <= $this->endDate->get()) {
			$d        = $counter->format('Y-m-d');
			$json[$d] = array(
				'events' => array(),
			);
			$counter->tomorrow();
		}
		$list = $this->EventList();

		foreach ($list as $e) {
			//print_r($e->Dates);
			foreach ($e->Dates as $eventDate) {
				// print_r($eventDate->StartDateTime->Format('YYYY-MM-dd').'<br />');
				if (isset($json[$eventDate->StartDateTime->Format('YYYY-MM-dd')])) {
					$json[$eventDate->StartDateTime->Format('YYYY-MM-dd')]['events'][] = $e->getTitle();
				}
			}
		}
		$this->getResponse()->addHeader('Content-type', 'application/json');
		return Convert::array2json($json);
	}

	/*****************************/
	/* RSS And JSON Feed Methods */
	/*****************************/
	public function Feed() {
		$feedType = addslashes($this->urlParams['Type']);
		//If we have Category in the URL params, get events from a category only
		if (array_key_exists('Category', $this->urlParams)) {
			$categoryTitle = $this->urlParams['Category'];
			$category      = Category::get()->filter(array('Title' => $categoryTitle))->First();
			$events        = $category->Events();
			//else get all events
		} else {

			$events = $this->EventList('year', null, null, null, null, null, 'false');
		}
		//Determine which feed we're going to output
		switch ($feedType) {
			case "json":
				return $this->generateJsonFeed($events);
				break;
			default:
				return $this->generateJsonFeed($events);
				break;
		}
	}
	public function getCategoriesJsonFeed($categories) {
		if (!isset($categories)) {
			$categories = Category::get();
		}
		$data = array();
		foreach ($categories as $catNum => $category) {
			$data["categories"][$catNum]['id']                  = $category->ID;
			$data["categories"][$catNum]['title']               = $category->Title;
			$data["categories"][$catNum]['kind']                = $category->ClassName;
			$data["categories"][$catNum]['has_upcoming_events'] = $category->Events()->exists();
			$data["categories"][$catNum]['feed_url']            = $category->jsonFeedLink();
			$data["categories"][$catNum]['address']             = $category->Address;
			$data["categories"][$catNum]['info']                = $category->Information;
			$data["categories"][$catNum]["contact_email"]       = $category->Email;
			$data["categories"][$catNum]["contact_phone"]       = $category->Phone;
			$data["categories"][$catNum]["website_link"]        = $category->WebsiteURL;
			$data["categories"][$catNum]["latitude"]            = $category->Lat;
			$data["categories"][$catNum]["longitude"]           = $category->Lng;
		}
		return json_encode($data);
	}
	public function generateJsonFeed($events) {
		if (!isset($events)) {
			$events = $this->EventList('year', null, null, null, null, null, 'false');
		}
		$data = array();
		foreach ($events as $eventNum => $event) {
			/* Get Dates in  an array for later */
			$datesArray = array();
			$dates      = $event->Dates;
			foreach ($dates as $dateNum => $date) {
				$datesArray[$dateNum]["start_date"] = $date->StartDateTime->Format('Y-m-d');
				$datesArray[$dateNum]["start_time"] = $date->StartDateTime->Format('H:i:s');
				if (!empty($date->EndDateTime)) {
					$datesArray[$dateNum]["end_date"] = $date->EndDateTime->Format('Y-m-d');
					$datesArray[$dateNum]["end_time"] = $date->EndDateTime->Format('H:i:s');
				}
				$datesArray[$dateNum]["all_day"] = $date->AllDay;
			}
			$venuesArray = array();
			$venues      = $event->Venue;
			foreach ($venues as $venueNum => $venue) {
				$venuesArray[$venueNum]["id"]            = $venue->ID;
				$venuesArray[$venueNum]["name"]          = $venue->AltTitle?$venue->AltTitle:$venue->Title;
				$venuesArray[$venueNum]["address"]       = $venue->Address;
				$venuesArray[$venueNum]["info"]          = $venue->Information;
				$venuesArray[$venueNum]["contact_email"] = $venue->Email;
				$venuesArray[$venueNum]["contact_phone"] = $venue->Phone;
				$venuesArray[$venueNum]["website_link"]  = $venue->WebsiteURL;
				$venuesArray[$venueNum]["latitude"]      = $venue->Lat;
				$venuesArray[$venueNum]["longitude"]     = $venue->Lng;
			}
			$eventTypesArray = array();
			$eventTypes      = $event->Types;
			if (!empty($eventTypes)) {
				foreach ($eventTypes as $eventTypeNum => $eventType) {
					$eventTypesArray[$eventTypeNum]["id"]   = $eventType->ID;
					$eventTypesArray[$eventTypeNum]["name"] = $eventType->Title;
					$eventTypesArray[$eventTypeNum]["info"] = $eventType->Information;
				}
			}
			$sponsorsArray                    = array();
			$sponsorsArray[0]["id"]           = '';
			$sponsorsArray[0]["name"]         = $event->Sponsor;
			$sponsorsArray[0]["info"]         = '';
			$sponsorsArray[0]["website_link"] = '';

			$data["events"][$eventNum]["id"]                  = $event->ID;
			$data["events"][$eventNum]["name"]                = $event->Title;
			$data["events"][$eventNum]["link"]                = $event->UiCalendarLink;
			$data["events"][$eventNum]["more_info_link"]      = $event->MoreInfoLink;
			$data["events"][$eventNum]["facebook_event_link"] = $event->FacebookEventLink;

			if (isset($event->Image)) {
				$data["events"][$eventNum]["image"] = $event->Image->URL;
			}
			//$data["events"][$eventNum]["description"] = $event->Content;
			$data["events"][$eventNum]["cancel_note"] = $event->CancelReason;
			$data["events"][$eventNum]["dates"]       = $datesArray;
			$data["events"][$eventNum]["price"]       = $event->Cost;
			$data["events"][$eventNum]["location"]    = $event->Location;
			$data["events"][$eventNum]["venues"]      = $venuesArray;
			$data["events"][$eventNum]["sponsors"]    = $sponsorsArray;
			$data["events"][$eventNum]["event_types"] = $eventTypesArray;
			unset($datesArray);
		}
		return json_encode($data);
	}

}