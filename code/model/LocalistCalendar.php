<?php
class LocalistCalendar extends Page {

	private static $db = array(
		
	);

	private static $has_one = array(

	);
	
	private static $allowed_children = array('');
	
	function getCMSFields() {
		$fields = parent::getCMSFields();
		return $fields;
	}
	public function CalendarWidget() {
	 	$calendar = CalendarWidget::create($this);
	 	$controller = Controller::curr();
	 	if($controller->class == "Calendar_Controller" || is_subclass_of($controller, "Calendar_Controller")) {
	 		if($controller->getView() != "default") {	 			
				if($startDate = $controller->getStartDate()) {
					$calendar->setOption('start', $startDate->format('Y-m-d'));
				}
				if($endDate = $controller->getEndDate()) {
					$calendar->setOption('end', $endDate->format('Y-m-d'));
				}
			}
		}
		return $calendar;
	}

	public function VenueList() {
		$activeEvents = $this->EventList();
		$venuesList = new ArrayList();

		foreach($activeEvents as $key => $parsedEvent){
			//print_r($parsedEvent);
			//$assVenue = new LocalistVenue();
			//$assVenue->ID = $parsedEvent->VenueID;
			//$assVenue = $parsedEvent->VenueID;
			//$activeVenues->push($assVenue);
			$venuesList->push($parsedEvent->Venue);
		}	
		return $venuesList;
		//print_r($venuesList);
		//print_r($activeVenues);
		//$venuesList->removeDuplicates();
		//print_r($activeVenues);

		/*
		foreach($venuesList as $key => $uniqueVenue){
			print_r($uniqueVenue);
			$venueID = $uniqueVenue->ID;
			//print_r($venueID);
			//$venueURL = LOCALIST_FEED_URL.'places/'.$venueID;
			//print_r($venueURL);
			//$rawVenue = file_get_contents($venueURL);
			//$venueDecoded = json_decode($rawVenue, TRUE);
			//$localistVenue = new LocalistVenue();
			//print_r($venueDecoded);
			$venuesList->push($uniqueVenue);
		

		}			
*/	

	}

	public function EventList($days = "200", $startDate = null, $endDate = null, $venue = null){
		$feedParams = "?";
		$feedParams .= "days=".$days;

		$startDateSS = new SS_Datetime();
		$endDateSS = new SS_Datetime();

	
		if(isset($startDate)){
			$startDateSS->setValue($startDate);
			$feedParams .= "&start=".$startDate->format('Y-m-d');
		}
		if(isset($endDate)){
			$endDateSS->setValue($endDate);
			$feedParams .= "&end=".$endDate->format('Y-m-d');
		}

		if(isset($venue)){
			$feedParams .= "&venue_id=".$venue;
		}
		$feedParams .= "&pp=50&distinct=true";

		$cache = new SimpleCache();
		$feedURL = LOCALIST_FEED_URL.'events'.$feedParams;

		//print_r($feedURL.'<br />');

		$eventsList = new ArrayList();

		$rawFeed = $cache->get_data("EventList-".$feedParams, $feedURL);
		$eventsDecoded = json_decode($rawFeed, TRUE);
		$eventsArray = $eventsDecoded['events'];

		foreach($eventsArray as $event) {
			if(isset($event)){
				$localistEvent = new LocalistEvent();
				$eventsList->push($localistEvent->parseEvent($event['event']));
			}
		}

		return $eventsList;  		

	}

	public function SingleEvent($id){

		$cache = new SimpleCache();

		$feedParams = "events/".$id;
		$feedURL = LOCALIST_FEED_URL.$feedParams;

		$rawFeed = $cache->get_data("SingleEvent-".$id, $feedURL);
		$eventsDecoded = json_decode($rawFeed, TRUE);

		$event = $eventsDecoded['event'];
		if(isset($event)){
			$localistEvent = new LocalistEvent();
			return $localistEvent->parseEvent($event);	
		}
		return false;
	}


}
class LocalistCalendar_Controller extends Page_Controller {

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
	private static $allowed_actions = array (
		'event',
		'show',
		'monthjson'
	);

	private static $url_handlers = array(
		'event/$eventID' => 'event',
		'show/$startDate/$endDate' => 'show',
		'monthjson/$ID' => 'monthjson'
	);

	public function event($request) {
		$eventID =  addslashes($this->urlParams['eventID']);
		$event = $this->SingleEvent($eventID);
		return $event->renderWith(array('LocalistEvent', 'Page'));
	}

	public function show($request){
		$startDate = new SS_Datetime();
		$startDate->setValue(addslashes($this->urlParams['startDate']));

		$endDate = new SS_Datetime();
		$endDate->setValue(addslashes($this->urlParams['endDate']));

		$events = $this->EventList(null, $startDate, $endDate);

		$Data = array (
			"EventList" => $events,
			"StartDate" => $startDate,
			"EndDate" => $endDate
		);
		return $this->customise($Data)->renderWith(array('LocalistCalendar', 'Page'));


	}

	public function monthjson($r) {
		if(!$r->param('ID')) return false;
		$this->startDate = sfDate::getInstance(CalendarUtil::get_date_from_string($r->param('ID')));
		$this->endDate = sfDate::getInstance($this->startDate)->finalDayOfMonth();
		
		$json = array ();
		$counter = clone $this->startDate;		
		while($counter->get() <= $this->endDate->get()) {
			$d = $counter->format('Y-m-d');
			$json[$d] = array (
				'events' => array ()
			);
			$counter->tomorrow();
		}		
		$list = $this->EventList();
		foreach($list as $e) {
			//print_r($e->Dates);
			foreach($e->Dates as $date) {
				if(isset($json[$date->Format('Y-m-d')])) {
					$json[$date->Format('Y-m-d')]['events'][] = $e->getTitle();
				}
			}
		}
		return Convert::array2json($json);
	}

}
?>