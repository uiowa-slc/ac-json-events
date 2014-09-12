<?php

class LocalistDatetime extends DataObject {

	private static $db = array (		
		'StartDateTime' => 'SS_Datetime',
		'EndDateTime' => 'SS_Datetime',	
	);

	public function getEndTime(){
		if(!empty($this->EndDateTime)){
			$endDate = $this->EndDateTime->Date();
			$startDate = $this->StartDateTime->Date();

			if($endDate == $startDate){
				///print_r($this->EndDateTime);
				return $this->EndDateTime;
			}
		}
	}

	public function getEndDate(){
		//print_r($this->getField('EndDateTime')->getValue());
		$endDate = $this->getField('EndDateTime')->Date();
		$startDate = $this->StartDateTime->Date();

		if($endDate == $startDate){
			return false;
		}else{
			return $this->getField('EndDateTime');
		}
	}


	/* Link function might change to an internal link eventually, currently links to the
	localist caliendar filter */

	public function Link(){
		$datestring = date("Y-m-d", strtotime($this->StartDateTime->value));
		$urlparts = array(
			"events/show/",
			$datestring
			);
		//print_r(implode($urlparts));
		return (implode($urlparts));

	}

}