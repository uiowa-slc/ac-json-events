<?php
class JSONFeedExtension extends DataExtension{

	public function JSONRSSDisplay($numItems = 30, $feedURL="http://afterclass.uiowa.edu/events/feed/json") {
		$outfeed = new Array();
		
		$feed_file = file_get_contents($feedURL);
		$feed_array = json_decode($feed_file, true);
		
		//need to pull apart $feed_string to get iterable list.
		
		
		foreach($items as $item) {
		 	//do we need the simplepie rss2 namespace? test it. --actually... it isn't needed? idk why.
		 	//$custom_fields = $item->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20,'custom_fields');
		 	$custom_fields = $item->get_item_tags(null,'custom_fields');
			
			
			// Cast the Date
			$date = new Date('Date');
			$date->setValue($item->get_date());
			
			$dates = new Text('Dates');
			$dates->setValue($item->get_item_tags(null,'dates'));
			$dates->setValue($dates->value[0]['data']);
			
			$cost = new Text('Cost');
			$cost->setValue($item->get_item_tags(null,'cost'));
			$cost->setValue($cost->value[0]['data']);
			
			$location = new Text('Location');
			$location->setValue($item->get_item_tags(null,'location'));
			$location->setValue($location->value[0]['data']);
	
			$image = new Text('ImageURL');
			$image->setValue($item->get_item_tags(null,'image'));
			$image->setValue($image->value[0]['data']);	
	
			$smallimage = new Text('Smallimage');
			$smallimage->setValue($item->get_item_tags(null,'smallimage'));
			$smallimage->setValue($smallimage->value[0]['data']);
			
			
			// Cast the Title
			$title = new Text('Title');
			$title->setValue(html_entity_decode($item->get_title()));
			
			$author = new Text('Author');
			$item_author = $item->get_author();
			
			if(isset($item_author)){
				$author->setValue($item_author->get_name());}
			
			// Cast the description and strip
			$desc = new HTMLText('Description');
			$desc->setValue(strip_tags($item->get_description()));
							
			if($thumbnail_enclosure = $item->get_enclosure()){
				//Thumbnail
				$thumbnail_url = new Text('ImageURL');
				$thumbnail_url->setValue($thumbnail_enclosure->link);
			}else{
				$thumbnail_url = null;
			
			}
			
			
			
			
			$output->push(new ArrayData(array(
		    'Title'         => $title,
		    'Author'       =>$author,
		    'Date'         => $date,
		    'Dates'		=> $dates,
		    'Cost'		=> $cost,
		    'Location'	=> $location,
		    'Link'         => $item->get_link(),
		    'Description'   => $desc,
		    'Cost' => $cost,
		    "Location" => $location,
		    'Smallimage' => $smallimage,
		    "ImageURL" => $image
		    
		    )));
	}
}

