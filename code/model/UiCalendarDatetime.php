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

	// public function getStartDateTime() {
	// 	return $this->obj('StartDateTime');
	// }

	// public function getStartTime(){
	// 	$time = $this->obj('StartDateTime');
	// 	return $time;
	// }

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
	// public function getEndTime() {
	// 	if (!empty($this->EndDateTime)) {
	// 		$endDate = $this->EndDateTime->Date();
	// 		$startDate = $this->StartDateTime->Date();

	// 		if ($endDate == $startDate) {
	// 			///print_r($this->EndDateTime);
	// 			return $this->EndDateTime;
	// 		}
	// 	}
	// }

	// public function getEndDate() {
	// 	//print_r($this->getField('EndDateTime')->getValue());
	// 	$endDate = $this->getField('EndDateTime')->Date();
	// 	$startDate = $this->StartDateTime->Date();

	// 	if ($endDate == $startDate) {
	// 		return false;
	// 	} else {
	// 		return $this->getField('EndDateTime');
	// 	}
	// }

	/* Link function might change to an internal link eventually, currently links to the
	localist caliendar filter */

	public function Link() {
		$calendarLink = UiCalendar::getOrCreate()->Link();
		$datestring = $this->StartDateTime->Format("Y-MM-dd");
		return $calendarLink . 'show/' . $datestring;
	}

}