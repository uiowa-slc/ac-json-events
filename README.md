# Using Localist in SilverStripe
================================

## Installation
*Note: If you're using division-project or cfo-project, ac-json-events will be installed by default. In this case you only need to do step 1.*

1. Make a cache/ folder in your site's root and ensure that it's read/writable by everyone.
2. Make sure "ac-json-events : dev-master" is listed in the site's "require"  section in its composer.json file.
3. Ensure that the composer.json file has the ac-json-events repository listed in the "repositories" section. 
4. Run composer update from the site's root.

Example "repositories" section:

```
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/StudentLifeMarketingAndDesign/ac-json-events"
        }
    ]
```

*If you run into database problems or 'template not found' issues, be sure to run dev/build or ?flush=1 respecively.*

## Post-install
After installing ac-json-events, you'll need to add a LocalistCalendar page in the SilverStripe CMS. These pages are usually named 'Calendar'. After adding this page, you can choose several filters that will determine which events get fed to the website:

* Event Type
* Department
* Venue
* General Interest

If you don't choose any filters, all events from Localist will be fetched. If you choose more than one filter, the filters will be combined, further narrowing down the results

Additionally, you can choose up to four events (determined by the filters above-- you might need to Save & Publish + Refresh to see the filtered events in these dropdowns) to feature on the calendar or homepage using the ```<% loop FeaturedEvents %>``` function (described in the next section).

## Template usage

Once installed, you can override the following template files in your site's theme:

###LocalistCalendar.ss
This is the calendar's home page. The available loops and fields are as follows on Localist Calendar:

*Note: If you wish to use these functions on another page type, for example: HomePage, surround the loop block with: ```<% with LocalistCalendar %><% end_with %>```* 

```<% loop```

* ```EventList``` - All filtered events. See **LocalistEvent.ss** for all fields available in each event.
* ```FeaturedEvents``` - Events chosen as Featured in LocalistCalendar. If no events are chosen, this falls back on events marked as Featured in Localist. Otherwise no events are returned. See **LocalistEvent.ss** for all fields available in each event.
* ```TrendingTags``` - Trending Tags from the current set of events. See **LocalistTag** for all fields available in the tags/categories.
* ```TrendingTypes``` - Trending Types from the current set of events. See **LocalistType** for all fields available in the types.
* ```ActiveVenueList``` - List of all venues associated with full list of events from EventList. So if venue with no associated events won't display.
* ```VenuesList``` - List all Venues stored on Localist
* ```TypeList``` - List all the Event Types stored on Localist
* ```DepartmentList``` - List all the Departments stored on Localist
* ```GeneralInterestList``` - List all the General Interest Options stored on Localist
* ```getTodayEvents``` - List of events happening today
* ```getWeekendEvents``` - List of events happening Friday, Saturday, and Sunday.
* ```getMonthEvents``` - List of events happening in current month.

```%>```


###LocalistEvent.ss
This is an individual event page. The following loops and fields are available on each event individually or during a ```<% loop $EventList %>``` function on another page:

Fields:

* ```Featured``` - *Boolean* - True if the event is marked as featured in Localist
* ```Title``` - *String* - The title of the event.
* ```URLSegment``` - *String* - The final segment of the URL
* ```Cost``` - *String* - The cost of the event (sometimes "free")
* ```Location``` - *String* - Event room number or name
* ```Content``` - *String* - The event description
* ```SummaryContent``` - *String* - Summary of the content
LocalistImage.php more info.
* ```LocalistLink``` - *String* - The URL of the event on localist
* ```AfterClassLink``` - *String* - The URL of the event on afterclass.uiowa.edu
* ```MoreInfoLink``` - *String* - If the event has more information at another site, like a venue site, links there.
* ```FacebookEventLink``` - *String* - The ID of the facebook event, can be appended to "http://facebook.com/"
* ```ContactName``` - *String* - User supplied contact name
* ```ContactEmail``` - *String* - User supplied contact email
* ```Sponsor``` - *String* - User supplied sponsor name

* ```Image``` - *Object* - A LocalistImage object.
  * `Caption` - *String* - Caption text
  * `URL` - *String* - Full photo URL
  * `Credit` - *String* - Photo Credit
* ```Tags``` - *ArrayList* - An iterable ArrayList of LocalistTag tag objects.
  * `Title` - *String* - Title of the tag 
* ```Types``` - *ArrayList* - An iterable ArrayList of LocalistEventType objects.
  * `Title` - *String* - Title of the Type
* ```Dates``` - *ArrayList* - An interable ArrayList of LocalistDateTime objects which are extended from SS_Datetime objects.
  * ```StartDateTime``` - *SS_Datetime* - can run `format` methods on this.
  * ```EndDateTime``` - *SS_Datetime* - can run `format` methods on this.
* ```Venue``` - *ArrayList* - An interable ArrayList of LocalistVenue objects.
  * ```Title``` - *String* - Venue Title
  * ```Content``` - *String* - Venue Description
  * ```ImageURL``` - *String* - Full URL path to venue image
  * ```LocalistLink``` - *String* - URL to venue page on Localist
  * ```WebsiteLink``` - *String* - URL to maps.uiowa.edu/
  * ```Latitude``` - *String* - Given latitude of venue
  * ```Longitude``` - *String* - Given longitude of venue
  * ```Address``` - *String* - Given Address of venue
  