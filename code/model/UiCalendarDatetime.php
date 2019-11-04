<?php

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\FieldType\DBBoolean;


class UiCalendarDatetime extends DataObject {

	private $db = [
		'StartDateTime' => DBDatetime::class,
		'EndDateTime' => DBDatetime::class,
		'AllDay' => DBBoolean::class
	];

	public function setStartDateTime($startDateTime){
		$obj = new DBDatetime();
		$obj->setValue($startDateTime);
		$this->StartDateTime = $obj;
	}
	public function setEndDateTime($endDateTime){
		$obj = new DBDatetime();
		$obj->setValue($endDateTime);
		$this->EndDateTime = $obj;
	}
	
	/* Link function might change to an internal link eventually, currently links to the
	localist caliendar filter */

	public function Link() {
		$calendarLink = UiCalendar::getOrCreate()->Link();
		$datestring = $this->StartDateTime->Format("Y-MM-dd");
		return $calendarLink . 'show/' . $datestring;
	}

}