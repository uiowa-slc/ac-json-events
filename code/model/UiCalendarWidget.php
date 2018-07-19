<?php

use SilverStripe\Core\Convert;
use SilverStripe\View\Requirements;
use SilverStripe\View\ViewableData;

class UiCalendarWidget extends ViewableData {

	protected $calendar;

	protected $selectionStart;

	protected $selectionEnd;

	protected $options = array();

	public function __construct(UiCalendar $calendar) {
		$this->calendar = $calendar;
	}

	public function setOption($k, $v) {
		$this->options[$k] = $v;
	}

	public function getDataAttributes() {
		$attributes           = "";
		$this->options['url'] = $this->calendar->Link();

		foreach ($this->options as $opt => $value) {
			$attributes .= sprintf('data-%s="%s" ', $opt, Convert::raw2att($value));
		}
		return $attributes;
	}

	public function setSelectionStart($date) {
		$this->selectionStart = $date;
	}

	public function setSelectionEnd($date) {
		$this->selectionEnd = $date;
	}

	public function forTemplate() {

		return '<div class="calendar-widget" '.$this->getDataAttributes().'></div>';
	}
}
