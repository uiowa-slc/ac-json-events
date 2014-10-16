# Using Localist in SilverStripe

## Installation
*Note: If you're using division-project or cfo-project, ac-json-events will be installed by default. In this case you only need to do step 1.*

1. Make a cache/ folder in your site's root and ensure that it's read/writable by everyone.
2. Make sure "ac-json-events : dev-master" is listed in the site's "require"  section in its composer.json file.
3. Ensure that the composer.json file has the ac-json-events repository listed in the "repositories" section. 
4. Run composer update

Example "repositories" section:

```
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/StudentLifeMarketingAndDesign/ac-json-events"
        }
    ]
```

## Post-install
After installing ac-json-events, you'll need to add a LocalistCalendar page in SilverStripe. After adding this page, you can choose several filters that will determine which events get fed to the website:

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

###LocalistEvent.ss
This is an individual event page. The following loops and fields are available on each event individually or during a ```<% loop $EventList %>``` function on another page:

Fields:

* ```Title``` - *String* - The title of the event.
* ```Featured``` - *Boolean* - True if the event is marked as featured in Localist
* ```Cost``` - *String* - The cost of the event (sometimes "free")
* ```Location``` - *String*






