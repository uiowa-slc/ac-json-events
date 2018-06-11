<?php

class PrimeUiCalendarTask extends BuildTask{

	protected $title = 'Prime UiCalendar Cache';
	protected $description = 'Updates UiCalendar cache';

	protected $enabled = true;

	function run($request){
		echo '<h2>Finding all calendars....</h2>';

		//Prime calendars that exist in the db and their events.
		$calendars = UiCalendar::get();
		foreach($calendars as $calendar){
			echo '<p>Priming: '.$calendar->Title.'... <br />';

			$events = $calendar->EventList();

			foreach($events as $event){
				$primedEvent = $calendar->SingleEvent($event->ID);
			}

			echo $events->Count().' primed events in db calendar.';
		}

		//Prime event blocks (primarily on division project sites)
		if(class_exists('UpcomingEventsBlock')){
			echo '<h2>Finding all upcoming events blocks....</h2>';
			$blocks = UpcomingEventsBlock::get();

			foreach($blocks as $block){
				echo '<p>Priming block: '.$block->Title.'... <br />';
				$blockCalendar = $block->Calendar();
				if($blockCalendar){
					$blockPrimedEvents = $block->EventList();
				}
			}

			echo $blockPrimedEvents->Count().' primed events in block.';

		}
		
	}



}