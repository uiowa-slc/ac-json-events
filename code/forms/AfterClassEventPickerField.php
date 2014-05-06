<?php

class AfterClassEventPickerField extends DropdownField {
	private $feedBase = 'http://afterclass.uiowa.edu';
	//private $feedBase = 'http://baltar.imu.uiowa.edu:8888/after-class';

	private static $default_category = 0;

	protected $extraClasses = array('dropdown');


	public function __construct($name, $title = null, $source = null, $value = "", $form=null, $category = 0) {

		//If this page has a display category, limit picker to events from that category only. Otherwise all events are in the picker.
		if($category != 0){
			$feedURL = $this->feedBase.'/events/categories/'.$category.'/feed/json';
		}else{
			$feedURL = $this->feedBase.'/events/feed/';
		}

		$rawFeed = file_get_contents($feedURL);
		$eventsArray = json_decode($rawFeed, TRUE);

		if(!empty($eventsArray)){
			foreach($eventsArray['events'] as $key => $event){
				$source[$event['id']] = $event['name'];
			}
		}

		parent::__construct($name, ($title===null) ? $name : $title, $source, $value, $form);
		$this->setEmptyString('(No Event)');
	}

	public function Field($properties = array()) {
		return parent::Field();
	}
}
