<?php
class LocalistNewsletter extends Page {

	private static $db = array(

		'FeaturedEventID' => 'Int',
		'FeaturedEventSummary' => 'HTMLText',

		'Category1Title' => 'Varchar(255)',
		'Category1Event1ID' => 'Int',
		'Category1Event2ID' => 'Int',

		'Category2Title' => 'Varchar(255)',
		'Category2Event1ID' => 'Int',
		'Category2Event2ID' => 'Int',

		'Category3Title' => 'Varchar(255)',
		'Category3Event1ID' => 'Int',
		'Category3Event2ID' => 'Int',

		'Category4Title' => 'Varchar(255)',
		'Category4Event1ID' => 'Int',
		'Category4Event2ID' => 'Int',

		'MoreEventsTitle' => 'Varchar(255)',
		'NonFeaturedRow1Event1ID' => 'Int',
		'NonFeaturedRow1Event2ID' => 'Int',
		'NonFeaturedRow2Event1ID' => 'Int',
		'NonFeaturedRow2Event2ID' => 'Int',
		'NonFeaturedRow3Event1ID' => 'Int',
		'NonFeaturedRow3Event2ID' => 'Int',
		'NonFeaturedRow4Event1ID' => 'Int',
		'NonFeaturedRow4Event2ID' => 'Int',
		'NonFeaturedRow5Event1ID' => 'Int',
		'NonFeaturedRow5Event2ID' => 'Int',

	);

	private static $has_one = array(

	);

	private static $defaults = array (
		'MoreEventsTitle' => 'More Events'
	);

	private static $allowed_children = array( '' );
	private static $icon = 'ac-json-events/images/calendar-file.png';

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$calendar = LocalistCalendar::get()->First();
		$fields->removeByName('Content');
		$fields->removeByName('Metadata');
		$events = $calendar->EventList();

		if ($events->First()) {
			$eventsArray = $events->map();

			//Featured Event:

			$featuredEventField = new DropdownField( "FeaturedEventID", "Featured Event", $eventsArray );
			$featuredEventField->setEmptyString( '(No Event)' );
			$fields->addFieldToTab( 'Root.Main', $featuredEventField );

			$fields->addFieldToTab('Root.Main', new HTMLEditorField('FeaturedEventSummary', 'Featured Event Content <strong>(required)</strong>'));
			$fields->addFieldToTab('Root.Main', new LabelField('CatLabel', 'Display up to eight events with photos, two per column:'));


			//Category 1:
			$fields->addFieldToTab('Root.Main', new TextField('Category1Title', 'Category 1 Title (Optional)'));

			$cat1event1Field = new DropdownField( "Category1Event1ID", "Event 1", $eventsArray );
			$cat1event1Field->setEmptyString( '(No Event)' );

			$cat1event2Field = new DropdownField( "Category1Event2ID", "Event 2", $eventsArray );
			$cat1event2Field->setEmptyString( '(No Event)' );

			$fields->addFieldToTab('Root.Main', $cat1event1Field);
			$fields->addFieldToTab('Root.Main', $cat1event2Field);

			//Category 2:
			$fields->addFieldToTab('Root.Main', new TextField('Category2Title', 'Category 2 Title (Optional)'));

			$cat2event1Field = new DropdownField( "Category2Event1ID", "Event 3", $eventsArray );
			$cat2event1Field->setEmptyString( '(No Event)' );

			$cat2event2Field = new DropdownField( "Category2Event2ID", "Event 4", $eventsArray );
			$cat2event2Field->setEmptyString( '(No Event)' );

			$fields->addFieldToTab('Root.Main', $cat2event1Field);
			$fields->addFieldToTab('Root.Main', $cat2event2Field);

			//Category 3:
			$fields->addFieldToTab('Root.Main', new TextField('Category3Title', 'Category 3 Title (Optional)'));

			$cat3event1Field = new DropdownField( "Category3Event1ID", "Event 5", $eventsArray );
			$cat3event1Field->setEmptyString( '(No Event)' );

			$cat3event2Field = new DropdownField( "Category3Event2ID", "Event 6", $eventsArray );
			$cat3event2Field->setEmptyString( '(No Event)' );

			$fields->addFieldToTab('Root.Main', $cat3event1Field);
			$fields->addFieldToTab('Root.Main', $cat3event2Field);

			//Category 4:
			$fields->addFieldToTab('Root.Main', new TextField('Category4Title', 'Category 4 Title (Optional)'));

			$cat4event1Field = new DropdownField( "Category4Event1ID", "Event 7", $eventsArray );
			$cat4event1Field->setEmptyString( '(No Event)' );

			$cat4event2Field = new DropdownField( "Category4Event2ID", "Event 8", $eventsArray );
			$cat4event2Field->setEmptyString( '(No Event)' );

			$fields->addFieldToTab('Root.Main', $cat4event1Field);
			$fields->addFieldToTab('Root.Main', $cat4event2Field);

			//More Events Section 
			$fields->addFieldToTab('Root.Main', new LabelField('MoreEventsLabel', 'Display up to ten events without photos:'));

			$fields->addFieldToTab('Root.Main', new TextField('MoreEventsTitle', 'More Events Title'));

			$nonFeaturedEvent1Field = new DropdownField( "NonFeaturedRow1Event1ID", "Non Featured Event 1", $eventsArray );
			$nonFeaturedEvent1Field->setEmptyString( '(No Event)' );
			$fields->addFieldToTab('Root.Main', $nonFeaturedEvent1Field);

			$nonFeaturedEvent2Field = new DropdownField( "NonFeaturedRow1Event2ID", "Non Featured Event 2", $eventsArray );
			$nonFeaturedEvent2Field->setEmptyString( '(No Event)' );
			$fields->addFieldToTab('Root.Main', $nonFeaturedEvent2Field);

			$nonFeaturedEvent3Field = new DropdownField( "NonFeaturedRow2Event1ID", "Non Featured Event 3", $eventsArray );
			$nonFeaturedEvent3Field->setEmptyString( '(No Event)' );
			$fields->addFieldToTab('Root.Main', $nonFeaturedEvent3Field);

			$nonFeaturedEvent4Field = new DropdownField( "NonFeaturedRow2Event2ID", "Non Featured Event 4", $eventsArray );
			$nonFeaturedEvent4Field->setEmptyString( '(No Event)' );
			$fields->addFieldToTab('Root.Main', $nonFeaturedEvent4Field);

			$nonFeaturedEvent5Field = new DropdownField( "NonFeaturedRow3Event1ID", "Non Featured Event 5", $eventsArray );
			$nonFeaturedEvent5Field->setEmptyString( '(No Event)' );
			$fields->addFieldToTab('Root.Main', $nonFeaturedEvent5Field);

			$nonFeaturedEvent6Field = new DropdownField( "NonFeaturedRow3Event2ID", "Non Featured Event 6", $eventsArray );
			$nonFeaturedEvent6Field->setEmptyString( '(No Event)' );
			$fields->addFieldToTab('Root.Main', $nonFeaturedEvent6Field);

			$nonFeaturedEvent7Field = new DropdownField( "NonFeaturedRow4Event1ID", "Non Featured Event 7", $eventsArray );
			$nonFeaturedEvent7Field->setEmptyString( '(No Event)' );
			$fields->addFieldToTab('Root.Main', $nonFeaturedEvent7Field);

			$nonFeaturedEvent8Field = new DropdownField( "NonFeaturedRow4Event2ID", "Non Featured Event 8", $eventsArray );
			$nonFeaturedEvent8Field->setEmptyString( '(No Event)' );
			$fields->addFieldToTab('Root.Main', $nonFeaturedEvent8Field);

			$nonFeaturedEvent9Field = new DropdownField( "NonFeaturedRow5Event1ID", "Non Featured Event 9", $eventsArray );
			$nonFeaturedEvent9Field->setEmptyString( '(No Event)' );
			$fields->addFieldToTab('Root.Main', $nonFeaturedEvent9Field);

			$nonFeaturedEvent10Field = new DropdownField( "NonFeaturedRow5Event2ID", "Non Featured Event 10", $eventsArray );
			$nonFeaturedEvent10Field->setEmptyString( '(No Event)' );
			$fields->addFieldToTab('Root.Main', $nonFeaturedEvent10Field);

		}


		return $fields;
	}

	

}
class LocalistNewsletter_Controller extends Page_Controller {

	public function FeaturedEvent(){
		$calendar = LocalistCalendar::get()->First();
		$feature = new LocalistNewsletterFeature();
		$event = $calendar->SingleEvent($this->FeaturedEventID);

		
		$feature->Event = $event;
		$feature->Summary = $this->FeaturedEventSummary;
		//print_r($event);

		return $feature;

	}

	public function Categories(){
		$categories = new ArrayList();
		$calendar = LocalistCalendar::get()->First();

		for ($i = 1; $i <= 4; $i++) {
			$catProperty = 'Category'.$i;

			$catTitleProp = $catProperty.'Title';
			$catEvent1Prop = $catProperty.'Event1ID';
			$catEvent2Prop = $catProperty.'Event2ID';

			$cat = new LocalistNewsletterCategory();

			$cat->CatTitle = $this->{$catTitleProp};
			//print_r($catEvent1Prop);
			$cat->Event1 = $calendar->SingleEvent($this->{$catEvent1Prop});
			$cat->Event2 = $calendar->SingleEvent($this->{$catEvent2Prop});

			$categories->push($cat);
		}

		return $categories;
	}
	public function NonFeaturedRows(){
		$rows = new ArrayList();
		$calendar = LocalistCalendar::get()->First();

		for ($i = 1; $i <= 10; $i++) {
			$rowProperty = 'NonFeaturedRow'.$i;
			$rowEvent1Prop = $rowProperty.'Event1ID';
			$rowEvent2Prop = $rowProperty.'Event2ID';

			$row = new LocalistNewsletterNonFeaturedRow();

			//print_r($catEvent1Prop);
			$row->Event1 = $calendar->SingleEvent($this->{$rowEvent1Prop});
			$row->Event2 = $calendar->SingleEvent($this->{$rowEvent2Prop});

			$rows->push($row);
		}

		return $rows;
	}

}
?>
