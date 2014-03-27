<?php
class JSONFeedExtension extends DataExtension{

	public function JSONRSSDisplay($numItems = 30, $feedURL="http://afterclass.uiowa.edu/events/feed/json") {
		
		$outfeed = new ArrayList();
		
		$JSON_string = file_get_contents($feedURL);
		$feed_array = json_decode($JSON_string, TRUE);
		
		//print_r($feed_array);
		
		//$dump = var_dump($feed_array);	
		//echo $dump;
		 
		foreach($feed_array['events'] as $item) {
					 	
		 	$id = new Text('ID');
		 	$id = $item['id'];
		 	
		 	$name = new Text('name');
		 	$name->setValue($item['name']);
		 	
		 	$more_info_link = new Text('more_info_link');
		 	$more_info_link->setValue($item['more_info_link']);
		 	
		 	$imageURL = new Text('ImageURL');
			$imageURL->setValue($item['image']);	
	
			$cancel_note = new Text('cancel_note');
			$cancel_note->setValue($item['cancel_note']);
		 	
			$dates = new Date('Dates');
			$dates->setValue($item['dates'][0]);
			
			$cost = new Text('Cost');
			$cost->setValue($item['price']);
				
			$location = new Text('Location');
			$location->setValue($item['location']);
	
			$venues = new Text('Venues');
			if($item['venues']) {
				$venues->setValue($item['venues'][0]['name']);
			}else{
				$venues->setValue('No venues listed.');
			}
			
			$sponsors = new Text('Sponsors');
			if($item['sponsors']) {
				$sponsors->setValue($item['sponsors'][0]['name']);
			}else{
				$sponsors->setValue('No sponsors listed.');
			}
			
			$event_types = new Text('Event Types');
			if($item['event_types']) {
				$event_types->setValue($item['event_types'][0]['name']);					
			}else{
				$event_types->setValue('No event types listed.');	
			}
							
			$outfeed->push(new ArrayData(array(
			'ID'			=> $id,
		    'Title'         => $name,
		    'MoreInfoLink' => $more_info_link,
		    'ImageURL' => $imageURL,
		    'CancelNote' => $cancel_note,
		    'Dates'		=> $dates,
		    'Cost'		=> $cost,
		    'Location'	=> $location,
		    'Venues' => $venues,
		    'Sponsors' => $sponsors,
		    'EventTypes' => $event_types
		   
		    
		    )));
		    
		    //print_r($outfeed);		    
		}	
		
		return $outfeed;    
	}
}