<?php

use SilverStripe\ORM\DataExtension;
use quamsta\ApiCacher\FeedHelper;

class UiCalendarPageExtension extends DataExtension {

	public function UiCalendar() {
		return UiCalendar::getOrCreate();

	}

	public function getJson($feedURL) {
        $eventsDecoded = array();
        //Temporary shim to allow for old method of fetching json
        if(class_exists('quamsta\ApiCacher\FeedHelper')){

            $eventsDecoded = FeedHelper::getJson($feedURL);

        }else{

            $cache = new SimpleCache();
            if ($rawFeed = $cache->get_data($feedURL, $feedURL)) {
                $eventsDecoded = json_decode($rawFeed, TRUE);
            } else {
                $rawFeed = $cache->do_curl($feedURL);
                $cache->set_cache($feedURL, $rawFeed);
                $eventsDecoded = json_decode($rawFeed, TRUE);
            }

        }

        if (!empty($eventsDecoded)) {

            return $eventsDecoded;
        } else {

            return false;
        }
    }

}
