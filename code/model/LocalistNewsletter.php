<?php
class LocalistNewsletter extends Page {

	private static $db = array(

		'FeaturedEvent' => 'Int',

		'Category1Title' => 'Varchar(255)',
		'Category1Event1' => 'Int',
		'Category1Event2' => 'Int',

		'Category2Title' => 'Varchar(255)',
		'Category2Event1' => 'Int',
		'Category2Event2' => 'Int',

		'Category3Title' => 'Varchar(255)',
		'Category3Event1' => 'Int',
		'Category3Event2' => 'Int',

		'Category4Title' => 'Varchar(255)',
		'Category4Event1' => 'Int',
		'Category4Event2' => 'Int',

		'MoreEventsTitle' => 'Varchar(255)',
		'NonFeaturedEvent1' => 'Int',
		'NonFeaturedEvent2' => 'Int',
		'NonFeaturedEvent3' => 'Int',
		'NonFeaturedEvent4' => 'Int',
		'NonFeaturedEvent5' => 'Int',
		'NonFeaturedEvent6' => 'Int',
		'NonFeaturedEvent7' => 'Int',
		'NonFeaturedEvent8' => 'Int',
		'NonFeaturedEvent9' => 'Int',
		'NonFeaturedEvent10' => 'Int',

	);

	private static $has_one = array(

	);

	private static $defaults = array (
		'MoreEventsTitle' => 'More Events'
	);

	private static $allowed_children = array( '' );
	private static $icon = 'ac-json-events/images/calendar-file.png';

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$calendar = LocalistCalendar::get()->First();
		$fields->removeByName('Content');
		$events = $calendar->EventList();

		if ($events->First()) {
			$eventsArray = $events->map();

			//Featured Event:

			$featuredEventField = new DropdownField( "FeaturedEvent1ID", "Featured Event", $eventsArray );
			$featuredEventField->setEmptyString( '(No Event)' );
			$fields->addFieldToTab( 'Root.Main', $featuredEventField );

			$fields->addFieldToTab('Root.Main', new LabelField('CatLabel', 'Display up to eight events with photos, two per column:'));

			//Category 1:
			$fields->addFieldToTab('Root.Main', new TextField('Category1Title', 'Category 1 Title (Optional)'));

			$cat1event1Field = new DropdownField( "Category1Event1", "Event 1", $eventsArray );
			$cat1event1Field->setEmptyString( '(No Event)' );

			$cat1event2Field = new DropdownField( "Category1Event2", "Event 2", $eventsArray );
			$cat1event2Field->setEmptyString( '(No Event)' );

			$fields->addFieldToTab('Root.Main', $cat1event1Field);
			$fields->addFieldToTab('Root.Main', $cat1event2Field);

			//Category 2:
			$fields->addFieldToTab('Root.Main', new TextField('Category2Title', 'Category 2 Title (Optional)'));

			$cat2event1Field = new DropdownField( "Category2Event1", "Event 3", $eventsArray );
			$cat2event1Field->setEmptyString( '(No Event)' );

			$cat2event2Field = new DropdownField( "Category2Event2", "Event 4", $eventsArray );
			$cat2event2Field->setEmptyString( '(No Event)' );

			$fields->addFieldToTab('Root.Main', $cat2event1Field);
			$fields->addFieldToTab('Root.Main', $cat2event2Field);

			//Category 3:
			$fields->addFieldToTab('Root.Main', new TextField('Category3Title', 'Category 3 Title (Optional)'));

			$cat3event1Field = new DropdownField( "Category3Event1", "Event 5", $eventsArray );
			$cat3event1Field->setEmptyString( '(No Event)' );

			$cat3event2Field = new DropdownField( "Category3Event2", "Event 6", $eventsArray );
			$cat3event2Field->setEmptyString( '(No Event)' );

			$fields->addFieldToTab('Root.Main', $cat3event1Field);
			$fields->addFieldToTab('Root.Main', $cat3event2Field);

			//Category 4:
			$fields->addFieldToTab('Root.Main', new TextField('Category4Title', 'Category 4 Title (Optional)'));

			$cat4event1Field = new DropdownField( "Category4Event1", "Event 7", $eventsArray );
			$cat4event1Field->setEmptyString( '(No Event)' );

			$cat4event2Field = new DropdownField( "Category4Event2", "Event 8", $eventsArray );
			$cat4event2Field->setEmptyString( '(No Event)' );

			$fields->addFieldToTab('Root.Main', $cat4event1Field);
			$fields->addFieldToTab('Root.Main', $cat4event2Field);

			//More Events Section 
			$fields->addFieldToTab('Root.Main', new LabelField('MoreEventsLabel', 'Display up to ten events without photos:'));

			$fields->addFieldToTab('Root.Main', new TextField('MoreEventsTitle', 'More Events Title'));

			$nonFeaturedEvent1Field = new DropdownField( "NonFeaturedEvent1", "Non Featured Event 1", $eventsArray );
			$nonFeaturedEvent1Field->setEmptyString( '(No Event)' );
			$fields->addFieldToTab('Root.Main', $nonFeaturedEvent1Field);

			$nonFeaturedEvent2Field = new DropdownField( "NonFeaturedEvent2", "Non Featured Event 2", $eventsArray );
			$nonFeaturedEvent2Field->setEmptyString( '(No Event)' );
			$fields->addFieldToTab('Root.Main', $nonFeaturedEvent2Field);

			$nonFeaturedEvent3Field = new DropdownField( "NonFeaturedEvent3", "Non Featured Event 3", $eventsArray );
			$nonFeaturedEvent3Field->setEmptyString( '(No Event)' );
			$fields->addFieldToTab('Root.Main', $nonFeaturedEvent3Field);

			$nonFeaturedEvent4Field = new DropdownField( "NonFeaturedEvent4", "Non Featured Event 4", $eventsArray );
			$nonFeaturedEvent4Field->setEmptyString( '(No Event)' );
			$fields->addFieldToTab('Root.Main', $nonFeaturedEvent4Field);

			$nonFeaturedEvent5Field = new DropdownField( "NonFeaturedEvent5", "Non Featured Event 5", $eventsArray );
			$nonFeaturedEvent5Field->setEmptyString( '(No Event)' );
			$fields->addFieldToTab('Root.Main', $nonFeaturedEvent5Field);

			$nonFeaturedEvent6Field = new DropdownField( "NonFeaturedEvent6", "Non Featured Event 6", $eventsArray );
			$nonFeaturedEvent6Field->setEmptyString( '(No Event)' );
			$fields->addFieldToTab('Root.Main', $nonFeaturedEvent6Field);

			$nonFeaturedEvent7Field = new DropdownField( "NonFeaturedEvent7", "Non Featured Event 7", $eventsArray );
			$nonFeaturedEvent7Field->setEmptyString( '(No Event)' );
			$fields->addFieldToTab('Root.Main', $nonFeaturedEvent7Field);

			$nonFeaturedEvent8Field = new DropdownField( "NonFeaturedEvent8", "Non Featured Event 8", $eventsArray );
			$nonFeaturedEvent8Field->setEmptyString( '(No Event)' );
			$fields->addFieldToTab('Root.Main', $nonFeaturedEvent8Field);

			$nonFeaturedEvent8Field = new DropdownField( "NonFeaturedEvent8", "Non Featured Event 8", $eventsArray );
			$nonFeaturedEvent8Field->setEmptyString( '(No Event)' );
			$fields->addFieldToTab('Root.Main', $nonFeaturedEvent8Field);

			$nonFeaturedEvent9Field = new DropdownField( "NonFeaturedEvent9", "Non Featured Event 9", $eventsArray );
			$nonFeaturedEvent9Field->setEmptyString( '(No Event)' );
			$fields->addFieldToTab('Root.Main', $nonFeaturedEvent9Field);

			$nonFeaturedEvent10Field = new DropdownField( "NonFeaturedEvent10", "Non Featured Event 10", $eventsArray );
			$nonFeaturedEvent10Field->setEmptyString( '(No Event)' );
			$fields->addFieldToTab('Root.Main', $nonFeaturedEvent10Field);

		}


		return $fields;
	}

	

}
class LocalistNewsletter_Controller extends Page_Controller {

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
		'tag',
		'type',
		'venue',
		'search',

		//legacy feed actions
		'feed'
	);


	/** URL handlers / routes  
	 */
	private static $url_handlers = array(
		'event/$eventID' => 'event',
		'show/$startDate/$endDate' => 'show',
		'monthjson/$ID' => 'monthjson',
		'tag/$tag' => 'tag',
		'type/$type' => 'type',
		'venue/$venue' => 'venue',
		'search' => 'search',

		//legacy feed urls:

		'feed/$Type' => 'Feed',
	);

	/**
	 * Controller function that renders a single event through a $url_handlers route.
	 * @param SS_HTTPRequest $request 
	 * @return Controller
	 */
	public function event( $request ) {
		$eventID = addslashes( $this->urlParams['eventID'] );

		/* If we're using an event ID as a key. */
		if ( is_numeric( $eventID ) ) {
			$event = $this->SingleEvent( $eventID );
			return $event->renderWith( array( 'LocalistEvent', 'Page' ) );
		}else {

			/* Getting an event based on the url slug **EXPERIMENTAL ** */
			$events = $this->EventList();
			foreach ( $events as $key => $e ) {
				if ( $e->URLSegment == $eventID ) {
					//print_r($e->URLSegment);
					$singleEvent = $this->SingleEvent($e->ID);
					return $this->customise( $singleEvent )->renderWith( array( 'LocalistEvent', 'Page' ) );;
				}
			}

		
				
		}
		//echo "hello";
		$this->Redirect(LOCALIST_BASE.'event/'.$eventID);
		//return $this->httpError( 404, 'The requested event can\'t be found in the events.uiowa.edu upcoming events list.');

	}

	/**
	 * Controller function that filters the calendar by a start+end date or a human-readable string like 'weekend'
	 * @param SS_HTTPRequest $request 
	 * @return Controller
	 */
	public function show( $request ) {

		$dateFilter = addslashes( $this->urlParams['startDate'] );

		switch ( $dateFilter ) {
		case 'weekend':
			$events = $this->getWeekendEvents();
			$filterHeader = 'Events happening this weekend:';
			break;
		case 'today':
			$events = $this->getTodayEvents();
			$filterHeader = 'Events happening today:';
			break;
		case 'month':
			$events = $this->getMonthEvents();
			$filterHeader = 'Events happening this month:';
			break;
		default:
			$startDate = new SS_Datetime();
			$startDate->setValue( addslashes( $this->urlParams['startDate'] ) );

			$endDate = new SS_Datetime();
			$endDate->setValue( addslashes( $this->urlParams['endDate'] ) );
			$filterHeader = 'Events happening on ';
			$filterHeader .= $startDate->format( 'l, F j' );

			if ( $endDate->getValue() ) {
				$filterHeader .= ' to '.$endDate->format( 'l, F j' );
			}


			$events = $this->EventList( null, $startDate->format( 'l, F j' ), $endDate->format( 'l, F j' ) );

		}

		$Data = array (
			'EventList' => $events,
			'FilterHeader' => $filterHeader,
		);
		return $this->customise( $Data )->renderWith( array( 'LocalistCalendar', 'Page' ) );

	}
	
	/**
	 * Controller Function that renders a filtered Event List by a Localist tag or keyword.
	 * @param SS_HTTPRequest $request 
	 * @return Controller
	 */
	public function tag( $request ) {
		$tagName = addslashes( $this->urlParams['tag'] );
		$events = $this->EventList( 200, null, null, null, rawurlencode($tagName) );
		$filterHeader = 'Events tagged as "'.$tagName.'":';

		$Data = array (
			'Title' => $tagName.' | '.$this->Title,
			'EventList' => $events,
			'FilterHeader' => $filterHeader,
		);

		return $this->customise( $Data )->renderWith( array( 'LocalistCalendar', 'Page' ) );
	}

	public function type( $request ) {
		$typeID = addslashes( $this->urlParams['type'] );
		$type = $this->getTypeByID( $typeID );

		$events = $this->EventList( 200, null, null, null, null, $type->ID );

		$filterHeader = 'Events categorized as "'.$type->Title.'":';

		$Data = array (
			'Title' => $type->Title.' | '.$this->Title,
			'EventList' => $events,
			'FilterHeader' => $filterHeader,
		);

		return $this->customise( $Data )->renderWith( array( 'LocalistCalendar', 'Page' ) );
	}

	public function venue( $request ) {
		$venueID = addslashes( $this->urlParams['venue'] );
		$venue = $this->getVenueByID( $venueID );

		if(isset($venue)){
			$events = $this->EventList( 200, null, null, $venue->ID );

			$filterHeader = 'Events listed at '.$venue->Title.':';

			$Data = array (
				'Title' => $venue->Title.' | '.$this->Title,
				'Venue' => $venue,
				'EventList' => $events,
				'FilterHeader' => $filterHeader,
			);

			return $this->customise( $Data )->renderWith( array( 'LocalistVenue', 'LocalistCalendar', 'Page' ) );
		}else{
			return $this->httpError( 404, 'The requested venue can\'t be found in the events.uiowa.edu upcoming events list.');
		}
	}

	public function search($request){
		$term = $request->getVar('term');
		//print_r('term: '.$term);
		$events = $this->EventList('200', null, null, null, null,null, 'true', false, $term);

		$data = array( 
			"Results" => $events,
			"Term" => $term
		);

		return $this->customise( $data )->renderWith( array( 'LocalistCalendar_search', 'Page' ) );

	}

	public function monthjson( $r ) {
		if ( !$r->param( 'ID' ) ) return false;
		$this->startDate = sfDate::getInstance( CalendarUtil::get_date_from_string( $r->param( 'ID' ) ) );
		$this->endDate = sfDate::getInstance( $this->startDate )->finalDayOfMonth();

		$json = array ();
		$counter = clone $this->startDate;
		while ( $counter->get() <= $this->endDate->get() ) {
			$d = $counter->format( 'Y-m-d' );
			$json[$d] = array (
				'events' => array ()
			);
			$counter->tomorrow();
		}
		$list = $this->EventList();
		foreach ( $list as $e ) {
			//print_r($e->Dates);
			foreach ( $e->Dates as $date ) {
				if ( isset( $json[$date->StartDateTime->Format( 'Y-m-d' )] ) ) {
					$json[$date->StartDateTime->Format( 'Y-m-d' )]['events'][] = $e->getTitle();
				}
			}
		}
		return Convert::array2json( $json );
	}

	//Legacy Json functions, to be deleted sometime.

/*****************************/
	/* RSS And JSON Feed Methods */
	/*****************************/	

 	public function Feed(){
 		$feedType = addslashes($this->urlParams['Type']);

 		//If we have Category in the URL params, get events from a category only
 		if(array_key_exists('Category', $this->urlParams)){
 			$categoryTitle = $this->urlParams['Category'];
 			$category = Category::get()->filter(array('Title' => $categoryTitle))->First();

 			$events = $category->Events();
 		//else get all events	
 		}else{
 			
 			$events = $this->EventList(200, null, null, null, null, null, 'false');
 		}
 		//Determine which feed we're going to output
 		switch($feedType){
 			case "json":
 				return $this->generateJsonFeed($events);
 				break;
 			default:
 				return $this->generateJsonFeed($events);
 				break;
 		}

 	}
 	public function getCategoriesJsonFeed($categories){
 		if(!isset($categories)){
 			$categories = Category::get();
 		}
 		$data = array();
 		foreach($categories as $catNum => $category){
 			$data["categories"][$catNum]['id'] = $category->ID;
 			$data["categories"][$catNum]['title'] = $category->Title;
 			$data["categories"][$catNum]['kind'] = $category->ClassName;
 			$data["categories"][$catNum]['has_upcoming_events'] = $category->Events()->exists();
 			$data["categories"][$catNum]['feed_url'] = $category->jsonFeedLink();
 			$data["categories"][$catNum]['address'] = $category->Address;
 			$data["categories"][$catNum]['info'] = $category->Information;
 			$data["categories"][$catNum]["contact_email"] = $category->Email;
 			$data["categories"][$catNum]["contact_phone"] = $category->Phone;
 			$data["categories"][$catNum]["website_link"] = $category->WebsiteURL;
 			$data["categories"][$catNum]["latitude"] = $category->Lat;
 			$data["categories"][$catNum]["longitude"] = $category->Lng;			
 		}
	 return json_encode($data);
 	}

 	public function generateJsonFeed($events){
 		if(!isset($events)){
 			$events = $this->EventList(200, null, null, null, null, null, 'false');
 		}
 		$data = array();

 		foreach($events as $eventNum => $event){

 			/* Get Dates in  an array for later */
 			$datesArray = array();
 			$dates = $event->Dates;

 			foreach($dates as $dateNum => $date){
 				$datesArray[$dateNum]["start_date"] = $date->StartDateTime->Format('Y-m-d');
 				$datesArray[$dateNum]["start_time"] = $date->StartDateTime->Format('H:i:s');
 				if(!empty($date->EndDateTime)){
	 				$datesArray[$dateNum]["end_date"] = $date->EndDateTime->Format('Y-m-d');
	 				$datesArray[$dateNum]["end_time"] = $date->EndDateTime->Format('H:i:s');
	 			}
 				$datesArray[$dateNum]["all_day"] = $date->AllDay;
 			}

 			$venuesArray = array();
 			$venues = $event->Venue;

 			foreach($venues as $venueNum => $venue){
 				$venuesArray[$venueNum]["id"] = $venue->ID;
 				$venuesArray[$venueNum]["name"] = $venue->AltTitle ? $venue->AltTitle : $venue->Title;
 				$venuesArray[$venueNum]["address"] = $venue->Address;
 				$venuesArray[$venueNum]["info"] = $venue->Information;
 				$venuesArray[$venueNum]["contact_email"] = $venue->Email;
 				$venuesArray[$venueNum]["contact_phone"] = $venue->Phone;
 				$venuesArray[$venueNum]["website_link"] = $venue->WebsiteURL;
 				$venuesArray[$venueNum]["latitude"] = $venue->Lat;
 				$venuesArray[$venueNum]["longitude"] = $venue->Lng;
 			}

 			$eventTypesArray = array();
 			$eventTypes = $event->Types;

 			if(!empty($eventTypes)){
	 			foreach($eventTypes as $eventTypeNum => $eventType){
	 				$eventTypesArray[$eventTypeNum]["id"] = $eventType->ID;
	 				$eventTypesArray[$eventTypeNum]["name"] = $eventType->Title;
	 				$eventTypesArray[$eventTypeNum]["info"] = $eventType->Information;
	 			}
 			}

  			$sponsorsArray = array();
			$sponsorsArray[0]["id"] = '';
			$sponsorsArray[0]["name"] = $event->Sponsor;
			$sponsorsArray[0]["info"] = '';
			$sponsorsArray[0]["website_link"] = '';
 			
 			$data["events"][$eventNum]["id"] = $event->ID;
 			$data["events"][$eventNum]["name"] = $event->Title;
 			$data["events"][$eventNum]["link"] = $event->LocalistLink;
 			$data["events"][$eventNum]["more_info_link"] = $event->MoreInfoLink;
 			$data["events"][$eventNum]["facebook_event_link"] = $event->FacebookEventLink;
 			
 			if(isset($event->Image)){
 				$data["events"][$eventNum]["image"] = $event->Image->URL;
 			}
 			//$data["events"][$eventNum]["description"] = $event->Content;
 			$data["events"][$eventNum]["cancel_note"] = $event->CancelReason;
 			$data["events"][$eventNum]["dates"] = $datesArray;
 			$data["events"][$eventNum]["price"] = $event->Cost;
 			$data["events"][$eventNum]["location"] = $event->Location;
 			$data["events"][$eventNum]["venues"] = $venuesArray;
 			$data["events"][$eventNum]["sponsors"] = $sponsorsArray;
 			$data["events"][$eventNum]["event_types"] = $eventTypesArray;
 			unset($datesArray);
 		}

 		return json_encode($data);
 	}

}
?>
