<?php

class LocalistPageExtension extends DataExtension {

	public function LocalistCalendar() {
		return LocalistCalendar::getOrCreate();
	}

	public function getJson($feedURL) {
		print_r($feedURL);
		$cache = new SimpleCache();
		if ($rawFeed = $cache->get_data($feedURL, $feedURL)) {
			$eventsDecoded = json_decode($rawFeed, TRUE);
		} else {
			$rawFeed = $cache->do_curl($feedURL);
			$cache->set_cache($feedURL, $rawFeed);
			$eventsDecoded = json_decode($rawFeed, TRUE);
		}

		if (!empty($eventsDecoded)) {
			return $eventsDecoded;
		} else {
			return false;
		}
	}
}