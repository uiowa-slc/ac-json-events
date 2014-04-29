<?php
class JSONDisplayExtension extends DataExtension{

	public function AfterClassFeed($feedURL="http://afterclass.uiowa.edu/events/feed/json") {
		
		$outfeed = new ArrayList();
		$JSON_string = file_get_contents($feedURL);
		$feed_array = json_decode($JSON_string, TRUE);
		 
		foreach($feed_array['events'] as $item) {
					 	
		 	$id = new Text('ID');
		 	$id = $item['id'];
		 	
		 	$name = new Text('name');
		 	$name->setValue($item['name']);

		 	$link = new Text('Link');
		 	$link->setValue($item['link']);
		 	
		 	$more_info_link = new Text('more_info_link');
		 	$more_info_link->setValue($item['more_info_link']);
		 	
		 	$imageURL = new Text('ImageURL');
			$imageURL->setValue($item['image']);	
	
			$cancel_note = new Text('cancel_note');
			$cancel_note->setValue($item['cancel_note']);
		 	
			$nextDateTime = new SS_Datetime('NextDateTime');
			$nextDateTime->setValue(strtotime($item['dates'][0]['start_date'].' '.$item['dates'][0]['start_time']));
			
			$dateTimeCount = new Int('DateTimeCount');
			$dateTimeCount->setValue(count($item['dates']));

			$cost = new Text('Cost');
			$cost->setValue($item['price']);
				
			$location = new Text('Location');
			$location->setValue($item['location']);
	
			$venue = new Text('Venue');
			if($item['venues']) {
				$venue->setValue($item['venues'][0]['name']);
			}

			$sponsors = new Text('Sponsors');
			if($item['sponsors']) {
				$sponsors->setValue($item['sponsors'][0]['name']);
			}

			$event_types = new Text('Event Types');
			if($item['event_types']) {
				$event_types->setValue($item['event_types'][0]['name']);					
			}
		
			$outfeed->push(new ArrayData(array(
			'ID'			=> $id,
		    'Title'         => $name,
		    'Link' => $link,
		    'MoreInfoLink' => $more_info_link,
		    'ImageURL' => $imageURL,
		    'CancelNote' => $cancel_note,
		    'NextDateTime'		=> $nextDateTime,
		    'DateTimeCount' => $dateTimeCount,
		    'Cost'		=> $cost,
		    'Location'	=> $location,
		    'Venue' => $venue,
		    'Sponsors' => $sponsors,
		    'EventTypes' => $event_types
		    )));
		}		
		return $outfeed;   
	}
}