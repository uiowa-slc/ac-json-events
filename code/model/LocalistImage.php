<?php
class LocalistImage extends DataObject {

	private static $db = array("Caption" => "Text", "URL" => "Text", "Credit" => "Text", );

	public function getByID($id) {

		if (!isset($id) || $id == 0) {
			return false;
		}

		$cache = new SimpleCache();

		$feedParams = 'photos/'.$id;
		$feedURL    = LOCALIST_FEED_URL.$feedParams;

		//print_r($feedURL);

		$rawFeed       = $cache->get_data($feedURL, $feedURL);
		$imagesDecoded = json_decode($rawFeed, TRUE);

		$image = $imagesDecoded['photo'];
		if (isset($image)) {
			$localistImage = new LocalistImage();
			return $localistImage->parse($image);
		}
		return false;
	}

	/* Link function might change to an internal link eventually, currently links to the
	localist caliendar filter */

	/**
	 * Convert an event in an array format (from Localist JSON Feed) to a LocalistEvent
	 * @param array $rawEvent
	 * @return LocalistEvent
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
		return $this->getURL();
	}

	public function getURL() {
		$cloudFrontDomain = 'https://d3e1o4bcbhmj8g.cloudfront.net';
		if (isset($this->URL)) {
			$localistServerURL = $this->getField('URL');
			$parsedURL = parse_url($localistServerURL);
			$cloudFrontURL = $cloudFrontDomain.$parsedURL['path'];
			return $cloudFrontURL;
		} else {
			$themeFolder = 'themes/'.SSViewer::current_theme();
			return $themeFolder.'/images/LocalistEventPlaceholder.jpg';
		}
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
