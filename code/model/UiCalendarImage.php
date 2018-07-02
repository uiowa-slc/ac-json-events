<?php

use SilverStripe\ORM\DataObject;
class UiCalendarImage extends DataObject {

	private static $db = array("Caption" => "Text", "URL" => "Text", "Credit" => "Text", );

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
		$this->ID      = $rawImage['id'];
		$this->Caption = $rawImage['caption'];
		$this->URL     = $rawImage['photo_url'];
		$this->Credit  = $rawImage['credit'];
		$this->Width   = $rawImage['width'];
		$this->Height  = $rawImage['height'];
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
}
