<?php

class LocalistPageExtension extends DataExtension{
	
	public function LocalistCalendar() {
		return LocalistCalendar::get()->First();
	}

}