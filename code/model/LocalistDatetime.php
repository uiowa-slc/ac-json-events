<?php

class LocalistDatetime extends SS_Datetime {


	/* Link function might change to an internal link eventually, currently links to the
	localist caliendar filter */

	public function Link(){
		$datestring = date("Y/n/j", strtotime($this->value));
		$urlparts = array(
			LOCALIST_BASE,
			"calendar/day/",
			$datestring
			);
		//print_r(implode($urlparts));
		return (implode($urlparts));

	}

}