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

	public function TrendingTags(){
		$events = $this->EventList();
		$tags = array();
		$localistTags = new ArrayList();
	
		foreach($events as $event){

			foreach($event->Tags as $eventTag){
				if(isset($tags[$eventTag->Title])){
					$tags[$eventTag->Title] = $tags[$eventTag->Title] + 1;
				}else{
					$tags[$eventTag->Title] = 0;
				}
			}


		}

		arsort($tags);

		foreach($tags as $key => $tag){
			$localistTag = new LocalistTag();
			$localistTag->Title = $key;
			$localistTags->push($localistTag);

		}

		return $localistTags;
	}

	public function VenueList() {
		$activeEvents = $this->EventList();
		$venuesList = new ArrayList();

		foreach($activeEvents as $key => $parsedEvent){
			$venuesList->push($parsedEvent->Venue);
		}	
		return $venuesList;

	}
	public function getTodayEvents(){
		$startDate = sfDate::getInstance()->format('Y-m-d');
		$endDate = sfDate::getInstance()->format('Y-m-d');
		$events = $this->EventList(null, $startDate, $endDate);
		return $events;
	}

	public function getWeekendEvents(){
		$startDate = sfDate::getInstance()->firstDayOfWeek()->format('Y-m-d');
		$endDate = sfDate::getInstance()->finalDayOfWeek()->format('Y-m-d');
		$events = $this->EventList(null, $startDate, $endDate);
		return $events;
	}

	public function getMonthEvents(){
		$startDate = sfDate::getInstance()->firstDayOfMonth()->format('Y-m-d');
		$endDate = sfDate::getInstance($this->startDate)->finalDayOfMonth()->format('Y-m-d');

		$events = $this->EventList(200, $startDate, $endDate);
		return $events;
	}

	public function EventList($days = "200", $startDate = null, $endDate = null, $venue = null,$keyword = null){
		$feedParams = "?";
		$feedParams .= "days=".$days;

		$startDateSS = new SS_Datetime();
		$endDateSS = new SS_Datetime();
	
		if(isset($startDate)){
			$startDateSS->setValue($startDate);
			$feedParams .= "&start=".$startDateSS->format('Y-m-d');
		}
		if(isset($endDate)){
			$endDateSS->setValue($endDate);
			$feedParams .= "&end=".$endDateSS->format('Y-m-d');
		}

		if(isset($venue)){
			$feedParams .= "&venue_id=".$venue;
		}

		if(isset($keyword)){
			$feedParams .= "&keyword=".$keyword;
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
		'monthjson',
		'tag'
	);

	private static $url_handlers = array(
		'event/$eventID' => 'event',
		'show/$startDate/$endDate' => 'show',
		'monthjson/$ID' => 'monthjson',
		'tag/$tag' => 'tag'
	);

	public function event($request) {
		$eventID = addslashes($this->urlParams['eventID']);

		/* If we're using an event ID as a key. */
		if(is_numeric($eventID)){
			$event = $this->SingleEvent($eventID);
			return $event->renderWith(array('LocalistEvent', 'Page'));
		}else{
			/* Getting an event based on the url slug **EXPERIMENTAL ** */
			$events = $this->EventList();
			foreach($events as $key => $e){
				if($e->URLSegment == $eventID){
					return $this->customise($e)->renderWith(array('LocalistEvent', 'Page'));;
				}
			}
		}
		
	}

	public function show($request){

		$dateFilter = addslashes($this->urlParams['startDate']);

		switch($dateFilter) {
			case "weekend":
				$events = $this->getWeekendEvents();
				$filterHeader = "Events happening this weekend";
				break;
			case "today":
				$events = $this->getTodayEvents();
				$filterHeader = "Events happening today";
				break;
			case "month":
				$events = $this->getMonthEvents();
				$filterHeader = "Events happening this month";
				break;
			default:
				$startDate = new SS_Datetime();
				$startDate->setValue(addslashes($this->urlParams['startDate']));

				$endDate = new SS_Datetime();
				$endDate->setValue(addslashes($this->urlParams['endDate']));
				$filterHeader = "Events happening on ";
				$filterHeader .= $startDate->format('l, F j');

				if($endDate->getValue()){
					$filterHeader .= " to ".$endDate->format('l, F j');
				}


				$events = $this->EventList(null, $startDate->format('l, F j'), $endDate->format('l, F j'));
			
		}

		$Data = array (
			"EventList" => $events,
			"FilterHeader" => $filterHeader,
		);
		return $this->customise($Data)->renderWith(array('LocalistCalendar', 'Page'));

	}

	public function tag($request){
		$tagName = addslashes($this->urlParams['tag']);
		$events = $this->EventList(200,null,null,null, $tagName);
		$filterHeader = "Events tagged as '".$tagName."'"; 

		$Data = array (
			"EventList" => $events,
			"FilterHeader" => $filterHeader,
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