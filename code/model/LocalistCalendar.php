<?php
class LocalistCalendar extends Page {

	private static $db = array(
		
	);

	private static $has_one = array(

	);
	
	private static $allowed_children = array('');
	
	function getCMSFields() {
		$fields = parent::getCMSFields();
		//$fields->addFieldToTab("Root.Main", new AfterClassCategoryDropdownField('DisplayCategory', 'Display the following category of AC events on this page'));
		return $fields;
	}

	public function getEventList(){
		$feedURL = LOCALIST_FEED_URL.$feedURL;
		$eventsList = new ArrayList();
		$rawFeed = file_get_contents($feedURL);
		$eventsDecoded = json_decode($rawFeed, TRUE);
		$eventsArray = $eventsDecoded['events'];
		foreach($eventsArray as $event) {
			$eventsList->push($this->parseEvent($event['event']));
		}		
		return $eventsList;  		

	}

	public function singleEvent($id){
		$feedParams = "events/".$id;
		$feedURL = LOCALIST_FEED_URL.$feedParams;
		$feed = new ArrayList();
		$rawFeed = file_get_contents($feedURL);
		$eventsDecoded = json_decode($rawFeed, TRUE);

		$event = $eventsDecoded['event'];
		if(isset($event)){
			return $this->parseEvent($event);	
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
		'date'
	);

	private static $url_handlers = array(
		'event/$eventID' => 'event',
		'date/$startDate/$endDate' => "date",
	);

	public function event($request){
		$eventID =  addslashes($this->urlParams['eventID']);
		$event = $this->AfterClassEvent($eventID);
		return $event->renderWith(array('LocalistEvent', 'Page'));
	}



}
?>