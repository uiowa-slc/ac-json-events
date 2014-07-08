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
	public function EventList($feedParams = "?days=200&pp=50&distinct=true"){
		$feedURL = LOCALIST_FEED_URL.'events/'.$feedParams;
		$eventsList = new ArrayList();
		$rawFeed = file_get_contents($feedURL);
		$eventsDecoded = json_decode($rawFeed, TRUE);
		$eventsArray = $eventsDecoded['events'];

		foreach($eventsArray as $event) {
			$localistEvent = new LocalistEvent();
			$eventsList->push($localistEvent->parseEvent($event['event']));
		}

		return $eventsList;  		

	}

	public function SingleEvent($id){
		$feedParams = "events/".$id;
		$feedURL = LOCALIST_FEED_URL.$feedParams;
		$feed = new ArrayList();
		$rawFeed = file_get_contents($feedURL);
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
		'monthjson' => 'monthjson'
	);

	public function event($request){
		$eventID =  addslashes($this->urlParams['eventID']);
		$event = $this->SingleEvent($eventID);
		return $event->renderWith(array('LocalistEvent', 'Page'));
	}

	public function show($request){
		$startDate = addslashes($this->urlParams['startDate']);
		$endDate = addslashes($this->urlParams['endDate']);

		$dateRange = "date range will be here";


		$events = $this->EventList('?start='.$startDate.'&end='.$endDate.'&pp=50&distinct=true');

		$Data = array (
			"EventList" => $events,
			"DateRange" => $dateRange,
		);
		return $this->customise($Data)->renderWith(array('LocalistCalendar', 'Page'));


	}



}
?>