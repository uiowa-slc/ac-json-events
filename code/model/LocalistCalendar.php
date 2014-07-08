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

		$cache = new SimpleCache();
		$feedURL = LOCALIST_FEED_URL.'events/'.$feedParams;
		$eventsList = new ArrayList();

		$rawFeed = $cache->get_data("event", $feedURL);
		$eventsDecoded = json_decode($rawFeed, TRUE);
		$eventsArray = $eventsDecoded['events'];

		foreach($eventsArray as $event) {
			$localistEvent = new LocalistEvent();
			$eventsList->push($localistEvent->parseEvent($event['event']));
		}

		return $eventsList;  		

	}

	public function SingleEvent($id){

		$cache = new SimpleCache();
		$feedParams = "events/".$id;
		$feedURL = LOCALIST_FEED_URL.$feedParams;

		$rawFeed = $cache->get_data("single-".$id, $feedURL);
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

	public function event($request){
		$eventID =  addslashes($this->urlParams['eventID']);
		$event = $this->SingleEvent($eventID);
		return $event->renderWith(array('LocalistEvent', 'Page'));
	}

	public function show($request){
		$startDate = new SS_Datetime();
		$startDate->setValue(addslashes($this->urlParams['startDate']));

		$endDate = new SS_Datetime();
		$endDate->setValue(addslashes($this->urlParams['endDate']));

		$events = $this->EventList('?start='.$startDate.'&end='.$endDate.'&pp=50&distinct=true');

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