<?php
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\FieldType\DBDatetime;

class UiCalendar_Controller extends PageController {

	private static $allowed_actions = array(
		'event',
		'show',
		'monthjson',
		'tag',
		'type',
		'venue',
		'search',
		'interest',
		'canceled',
	);

	/** URL handlers / routes
	 */
	private static $url_handlers = array(
		'event/$eventID' => 'event',
		'show/$startDate/$endDate' => 'show',
		'monthjson/$ID' => 'monthjson',
		'tag/$tag' => 'tag',
		'interest/$interest' => 'interest',
		'type/$type' => 'type',
		'venue/$venue' => 'venue',
		'search' => 'search',
		'canceled' => 'canceled',
	);

	public function index(HTTPRequest $r) {
		$startDate = new DBDatetime();
		$endDate = new DBDatetime();

		$startDate->setValue(date('Y-m-d'));
		$endDate->setValue(strtotime($startDate . " +200 days"));

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

            if(!isset($event)){
                 return $this->httpError(404);
            }

            if(isset($event->Dates)){

                if(!$event->Dates->First()){
                    //no upcoming dates, throw 404
                    if($this->RedirectExpiredEventsToWebsite && $event->MoreInfoLink){
                        $this->redirect($event->MoreInfoLink);
                    }else{
                        return $this->httpError(404);
                    }

                    //

                }
            }
			if ($this->isInDB() && $event) {
				return $this->customise($event)->renderWith(array('UiCalendarEvent', 'Page'));
			}
		} else {
            // event id is not numeric, throw 404
            return $this->httpError(404);

		}
		//echo "hello";
		//$this->Redirect(UICALENDAR_BASE.'event/'.$eventID);
		//return $this->httpError( 404, 'The requested event can\'t be found in the events.uiowa.edu upcoming events list.');
		return $this->httpError(404);
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
			$events = $this->getWeekendEvents();
			$filterHeader = 'This weekend:';
			break;
		case 'today':
			$events = $this->getTodayEvents();
			$filterHeader = 'Today:';
			break;
		case 'month':
			$events = $this->getMonthEvents();
			$filterHeader = 'This month:';
			break;
		default:

			if (!(isset($this->urlParams['startDate']) && $this->isDate($this->urlParams['startDate']))) {
				return $this->httpError(404);
			}

			$startDate = new DBDatetime();
			$startDate->setValue(addslashes($this->urlParams['startDate']));

			$endDate = new DBDatetime();

			if (isset($this->urlParams['endDate'])) {
				$endDate->setValue(addslashes($this->urlParams['endDate']));
			} elseif (isset($this->urlParams['startDate'])) {
				$endDate = $startDate;
			}

			$filterHeader = $startDate->format('eeee, MMMM d');

			if ($endDate->getValue() && ($endDate->getValue() != $startDate->getValue())) {
				$filterHeader .= ' to ' . $endDate->format('eeee, MMMM d');
			}

			$events = $this->EventList(null, $startDate->format('Y-MM-dd'), $endDate->format('Y-MM-dd'));

		}

		$Data = array(
			'FilterDate' => $dateFilter,
			'FilterEventList' => $events,
			'FilterHeader' => $filterHeader,
		);
		return $this->customise($Data)->renderWith(array('UiCalendar', 'Page'));

	}
	private function isDate($value) {
		if (!$value) {
			return false;
		}

		try {
			new \DateTime($value);
			return true;
		} catch (\Exception $e) {
			return false;
		}
	}
	/**
	 * Controller Function that renders a filtered Event List by a UiCalendar tag or keyword.
	 * @param SS_HTTPRequest $request
	 * @return Controller
	 */
	public function tag($request) {
		$tagID = addslashes($this->urlParams['tag']);
		$tag = $this->getKeywordByID($tagID);

		$events = $this->EventList('year', null, null, null, $tagID);
		$filterHeader = 'Events tagged as "' . $tag->Title . '":';

		$Data = array(
			'Title' => $tag->Title . ' | ' . $this->Title,
			'FilterID' => $tag->ID,
			'FilterEventList' => $events,
			'FilterHeader' => $filterHeader,
		);

		return $this->customise($Data)->renderWith(array('UiCalendar', 'Page'));
	}
	public function interest($request) {
		$interestID = addslashes($this->urlParams['interest']);
		$interest = $this->getGeneralInterestByID($interestID);
		//echo "interest: <br />";
		//print_r($interest->ID);
		//echo "<br />";
		$events = $this->EventList('year', null, null, null, null, null, null, null, null, 100, $interest->ID);

		$filterHeader = 'Events tagged as "' . $interest->Title . '":';

		$Data = array(
			'Title' => $interest->Title . ' | ' . $this->Title,
			'FilterID' => $interest->ID,
			'FilterEventList' => $events,
			'FilterHeader' => $filterHeader,
		);

		return $this->customise($Data)->renderWith(array('UiCalendar', 'Page'));
	}
	public function type($request) {
		$typeID = addslashes($this->urlParams['type']);
		$type = $this->getTypeByID($typeID);
		//echo "type: <br />";
		//print_r($type->ID);
		//echo "<br />";
		$events = $this->EventList('year', null, null, null, null, $type->ID);

		$filterHeader = 'Events tagged as "' . $type->Title . '":';

		$Data = array(
			'Title' => $type->Title . ' | ' . $this->Title,
			'FilterID' => $type->ID,
			'FilterEventList' => $events,
			'FilterHeader' => $filterHeader,
		);

		return $this->customise($Data)->renderWith(array('UiCalendar', 'Page'));
	}

	public function venue($request) {
		$venueID = addslashes($this->urlParams['venue']);
		$venue = $this->getVenueByID($venueID);

		if (isset($venue)) {
			$events = $this->EventList('year', null, null, $venue->ID);

			$filterHeader = 'Events listed at ' . $venue->Title . ':';

			$Data = array(
				'Title' => $venue->Title . ' | ' . $this->Title,
				'Venue' => $venue,
				'FilterEventList' => $events,
				'FilterHeader' => $filterHeader,
			);

			return $this->customise($Data)->renderWith(array('UiCalendarVenue', 'UiCalendar', 'Page'));
		} else {
			return $this->httpError(404, 'The requested venue can\'t be found in the events.uiowa.edu upcoming events list.');
		}
	}
	public function canceled() {
		$events = $this->EventList(

			$days = 'year',
			$startDate = null,
			$endDate = null,
			$venue = null,
			$keyword = null,
			$type = null,
			$distinct = 'true',
			$enableFilter = true,
			$searchTerm = null,
			$perPage = 100,
			$interest = null,
			//Show canceled events even if calendar has them disabled, used in UiCalendarController for the canceled route
			$forceShowCanceled = true,
			$canceledOnly = true
		);

		$filterHeader = 'Events currently marked as canceled or rescheduled';
		$Data = array(
			'Title' => 'Canceled or rescheduled events',
			'FilterEventList' => $events,
			'FilterHeader' => $filterHeader,
		);
		return $this->customise($Data)->renderWith(array('UiCalendar', 'Page'));
	}
	public function search($request) {
		$term = $request->getVar('term');
		//print_r('term: '.$term);
		$events = $this->EventList('year', null, null, null, null, null, 'true', false, $term);

		$data = array(
			"Results" => $events,
			"Term" => $term,
		);

		return $this->customise($data)->renderWith(array('UiCalendar_search', 'Page'));
	}

	public function monthjson($r) {
		if (!$r->param('ID')) {
			return false;
		}

		$this->startDate = sfDate::getInstance(CalendarUtil::get_date_from_string($r->param('ID')));
		$this->endDate = sfDate::getInstance($this->startDate)->finalDayOfMonth();

		$json = array();
		$counter = clone $this->startDate;
		while ($counter->get() <= $this->endDate->get()) {
			$d = $counter->format('Y-m-d');
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

}
