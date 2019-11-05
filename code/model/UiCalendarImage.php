<?php

use SilverStripe\ORM\DataObject;
class UiCalendarImage extends DataObject {

	private static $db = array(
		"URL" => "Text",
		"ThumbURL" => "Text", 
		"RectangleURL" => "Text",
		"Width" => "Int",
		"Height" => "Int",
		"Ratio" => "Float"
	);
	//Experimentally cache images and get ratio/width:
	private static $cache = false;

	public function getByID($id) {

		if (!isset($id) || $id == 0) {
			return false;
		}

		$cache = new SimpleCache();

		$feedParams = 'photos/'.$id;
		$feedURL    = UICALENDAR_FEED_URL.$feedParams;

		//print_r($feedURL);

		$rawFeed       = $cache->get_data($feedURL, $feedURL);
		$imagesDecoded = json_decode($rawFeed, TRUE);

		$image = $imagesDecoded['photo'];
		if (isset($image)) {
			$localistImage = new UiCalendarImage();
			return $localistImage->parse($image);
		}
		return false;
	}

	/* Link function might change to an internal link eventually, currently links to the
	localist caliendar filter */

	/**
	 * Convert an event in an array format (from UiCalendar JSON Feed) to a UiCalendarEvent
	 * @param array $rawEvent
	 * @return UiCalendarEvent
	 */
	public function parse($rawImage) {

		$this->URL = $rawImage['original_image'];
		$this->ThumbURL = $rawImage['large_image'];
		$this->RectangleURL = $rawImage['events_site_featured_image'];


		$cacheCheck = $this->config()->get('cache');

		if($cacheCheck){
		
			$size = $this->getimgsize($rawImage['original_image']);

			if($size){
			 $this->Width   = $size[0];
			 $this->Height  = $size[1];
			 $this->Ratio = $size[0] / $size[1];
			}

			$this->write();
		}

		return $this;
	}

	public function getAbsoluteURL() {
		return $this->URL;
	}

	public function getOrientation() {
		$width  = $this->Width;
		$height = $this->Height;
		if ($width > $height) {
			return "Landscape";
		} elseif ($height > $width) {
			return "Portrait";
		} else {
			return "Square";
		}
	}


/** 
 * Retrieve remote image dimensions 
 * - getimagesize alternative 
 */
 
/**
 * Get Image Size 
 * 
 * @param string $url 
 * @param string $referer 
 * @return array 
 */
private function getimgsize( $url, $referer = '' ) {
	
    // Set headers    
    $headers = array( 'Range: bytes=0-131072' );    
    if ( !empty( $referer ) ) { array_push( $headers, 'Referer: ' . $referer ); }

  // Get remote image
  $ch = curl_init();
  curl_setopt( $ch, CURLOPT_URL, $url );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
  curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
  $data = curl_exec( $ch );
  $http_status = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
  $curl_errno = curl_errno( $ch );
  curl_close( $ch );
    
  // Get network stauts
  if ( $http_status != 200 ) {
    echo 'HTTP Status[' . $http_status . '] Errno [' . $curl_errno . ']';
    return [0,0];
  }

  // Process image
  $image = imagecreatefromstring( $data );
  $dims = [ imagesx( $image ), imagesy( $image ) ];
  imagedestroy($image);

  return $dims;
}


}
