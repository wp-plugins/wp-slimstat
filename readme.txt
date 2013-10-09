=== WP SlimStat ===
Contributors: coolmann
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BNJR5EZNY3W38
Tags: chart, analytics, visitors, users, spy, shortstat, tracking, reports, seo, referers, analyze, wassup, geolocation, online users, spider, tracker, pageviews, world map, stats, maxmind, flot, stalker, statistics, google+, monitor, seo
Requires at least: 3.2
Tested up to: 3.6
Stable tag: 3.3.6

== Description ==
A powerful real-time web analytics plugin for WordPress. Visit our [official site](http://slimstat.getused.to.it/) for more information.

= Main Features =
* Real-time web analytics reports
* Compatible with W3 Total Cache, WP SuperCache, HyperCache and friends
* Modern, easy to use and customizable interface (yep, you can move reports around and hide the ones you don't need)
* The most accurate IP geolocation, browser and platform detection ever seen (courtesy of [MaxMind](http://www.maxmind.com/) and [Browscap](http://tempdownloads.browserscap.com/))
* Advanced filtering
* World Map that works on your mobile device, too (courtesy of [amMap](http://www.ammap.com/)).

= What are people saying about WP SlimStat? =
* One of the 15+ Cool Free SEO Plugins for WordPress - [udesign](http://www.pixeldetail.com/wordpress/free-seo-plugins-for-wordpress/)
* Thanks you for such an excellent plugin. I am using it to kick Jetpack out of all the wordpress installations that I manage for myself and others - [robertwagnervt](http://wordpress.org/support/topic/plugin-wp-slimstat-excellent-but-some-errors-on-activating)
* I like SlimStat very much and so I decided to use it instead of Piwik - [Joannes](http://wordpress.org/support/topic/plugin-wp-slimstat-slimstat-and-privacy)
* Read all the [reviews](http://wordpress.org/support/view/plugin-reviews/wp-slimstat) and feel free to post your own

= Requirements =
* WordPress 3.2+ (it may not work on *large* multisite environments)
* PHP 5.3+
* MySQL 5.0.3+
* At least 5 MB of free web space
* At least 5 MB of free DB space
* At least 10 Mb of free memory for the tracker

= Browser Compatibility =
WP SlimStat uses the HTML5 Canvas element and SVG graphics to display its charts and the world map. Unfortunately Internet Explorer 8 and older versions don't support them, so you're encouraged to upgrade your browser.

= Premium Add-ons =
Please visit [our website](http://slimstat.getused.to.it/addons/) for an updated list of extensions.

= Free Add-ons =
* [WP SlimStat Shortcodes](http://wordpress.org/extend/plugins/wp-slimstat-shortcodes/) allows you to share your reports with your readers
* [WP SlimStat Dashboard Widgets](http://wordpress.org/extend/plugins/wp-slimstat-dashboard-widgets) adds the most important reports to your WordPress Dashboard

== Installation ==

0. **If you are upgrading from 2.8.4 or earlier, you MUST first install version 3.0 (deactivate/activate) and then upgrade to the latest release available**
1. In your WP admin, go to Plugins > Add New
2. Search for WP SlimStat
3. Click on Install Now under WP SlimStat
4. Make sure your template calls `wp_footer()` or the equivalent hook somewhere (possibly just before the `</body>` tag)
5. To customize all the plugin's options, go to Slimstat > Settings
6. If your wp-admin folder is not publicly accessible, please make sure to check the [FAQs](http://wordpress.org/extend/plugins/wp-slimstat/faq/) to see if there's anything else you need to do

== Frequently Asked Questions ==

= I see a warning message saying that a misconfigured setting and/or server environment is preventing WP SlimStat from properly tracking my visitors =
WP SlimStat's tracking engine has a server-side component, which records all the information available at the time the resource is served,
and a client-side component, which collects extra data from your visitors' browsers, like their screen resolution, (x,y) coordinates of their
clicks and the events they trigger. 

One of the files responsible for taking care of this is `admin-ajax.php`, usually located inside your */wp-admin/* folder.
Point your browser to that file directly: if you see an error 404 or 500, then you will need to fix that problem, to allow WP SlimStat to do its job.
If you see the number zero, then the problem could be related to a conflict with another plugin (caching, javascript minimizers, etc).

= I am using W3 Total Cache/WP Super Cache/HyperCache/etc, and it looks like your plugin is not tracking all of my visitors. Can you help me? =
Simply go to SlimStat > Settings > General tab, and enable Javascript Mode. WP SlimStat will only track human visitors (just like Google Analytics does, pretty much), but its accuracy will dramatically improve.
Don't forget to invalidate/clear your plugin's cache, to let SlimStat add its tracking code to all the newly cached pages.

= My screen goes blank when trying to access the reports / after installing WP SlimStat =
Try [to increase the amount of memory](http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP) allocated to PHP. If that doesn't help,
go to SlimStat > Settings > Maintenance and click on the Reset Tabs button.

= When trying to access any of options screens, I get the following error: You do not have sufficient permissions to access this page. =
You were playing with the plugin's permission settings, weren't you? But don't worry, there's a secret passage that will allow you to unlock your access. Create a new user `slimstatadmin`, and assign him the Administrator role. Then log into your WordPress admin area with the new user and... voila: you can now access WP SlimStat's settings again. Update your users' permissions and then get rid of this newly created user.

= I am using WP Touch, and mobile visitors are not tracked by your plugin. How can I fix this problem? =
WP Touch has an advanced option that they call Restricted Mode, which attempts to fix issues where other plugins load scripts which interfere with WPtouch CSS and JavaScript. If you enable this feature, it will prevent WP SlimStat from running the tracking script (thank you, [Per](http://wordpress.org/support/topic/known-users-not-logged)).

= How can I change the colors associated to color-coded pageviews (known user, known visitors, search engines, etc)? =
Go to SlimStat > Settings > Advanced tab and paste your custom CSS into the corresponding field. Use the following code as a reference:

`.postbox p.is-search-engine,
.legend span.is-search-engine{background-color:#C1E751;color:#444}

.postbox p.is-direct,
.legend span.is-direct{background-color:#D0E0EB;color:#111}

.postbox p.is-known-user,
.legend span.is-known-user{background-color:#F1CF90}

.postbox p.is-known-visitor,
.legend span.is-known-visitor{background-color:#EFFD8C}

.postbox p.is-spam,
.legend span.is-spam{background-color:#AAB3AB;color:#222}`

= Can I track clicks and other events happening on the page? =
Yes, you can. This plugin includes a Javascript handler that can be attached to any event: click, mouseover, focus, keypress, etc. Here's the syntax:

`SlimStat.ss_track(e, c, n)`

Where:

* `e` is the event that was triggered (the word 'event' *must* be used when attaching event handlers to HTML tags, see examples below)
* `c` is a numeric value between 1 and 254 (zero is reserved for outbound clicks)
* `n` is a custom text (up to 512 chars long) that you can use to add a note to the event tracked. If the ID attribute is defined, and no note has been specified, the former will be recorded. If the function is attached to a key-related event, the key pressed will be recorded.

Examples:

* `onclick="if(typeof SlimStat.ss_track == 'function') SlimStat.ss_track(event, 5, 'clicked on first link');"`
* `onkeypress="if(typeof SlimStat.ss_track == 'function') SlimStat.ss_track(event, 20);"`
* To make your life easier, a *Google Plus One* callback function is included as well: `<g:plusone callback="SlimStat.slimstat_plusone"></g:plusone>`. Clicks on your Google+ button will be identified by the note 'google-plus-on/off'. Pleae refer to the [official documentation](https://developers.google.com/+/plugins/+1button/) for more information.

= How do I use all those filters in the dropdown menu? =
Here's a brief description of what they mean. Please remember that you can access the same information directly from within the admin, by 'pulling' the Help tab that should appear in the top right hand corner.

Basic filters:

* `browser`: user agent (Firefox, Chrome, ...)
* `country code`: 2-letter code (us, ru, de, it, ...)
* `referring domain`: domain name of the referrer page (i.e., www.google.com if a visitor was coming from Google)
* `ip`: visitor's public IP address
* `search terms`: keywords visitors used to find your website on a search engine
* `language code`: please refer to the [language culture names](http://msdn.microsoft.com/en-us/library/ee825488(v=cs.20).aspx) (first column) for more information
* `operating system`: accepts identifiers like win7, win98, macosx, ...; please refer to [this manual page](http://php.net/manual/en/function.get-browser.php) for more information about these codes
* `permalink`: URL accessed on your site
* `referer`: complete URL of the referrer page
* `visitor's name`: visitor's name according to the cookie set by WordPress after leaving a comment

Advanced filters:

* `browser capabilities`: plugins or extensions installed by that user (flash, java, silverlight...)
* `browser version`: user agent version (9.0, 11, ...)
* `browser type`: 1 = search engine crawler, 2 = mobile device, 3 = syndication reader, 0 = all others
* `color depth`: visitor's screen's color depth (8, 16, 24, ...)
* `css version`: what CSS standard was supported by that browser (1, 2, 3 and other integer values)
* `pageview attributes`: this field is set to [pre] if the resource has been accessed through Link Prefetching or similar techniques
* `post author`: author associated to that post/page when the resource was accessed
* `post category id`: ID of the category/term associated to the resource, when available
* `private ip`: visitor's private IP address, if available
* `resource content type`: post, page, cpt:*custom-post-type*, attachment, singular, post_type_archive, tag, taxonomy, category, date, author, archive, search, feed, home; please refer to the [Conditional Tags](http://codex.wordpress.org/Conditional_Tags) manual page for more information
* `screen resolution`: viewport width and height (1024x768, 800x600, ...)
* `visit id`: generally used in conjunction with 'is not empty', identifies human visitors

= How do I create my own custom reports? =
You will need to embed them in a plugin that leverages WP SlimStat APIs to retrieve the data. You can also access WP SlimStat's tables directly, for more complicated stuff.
Please refer to the database description and API reference guide here below for more information on what tables/methods are available.

Let's say you came up with your own SQL query, something like

`SELECT resource, COUNT(*) countresults
FROM $this->table_stats
WHERE resource <> ''
GROUP BY resource
ORDER BY countresults DESC
LIMIT 0,20`

Just write a function that gets the results and displays them, making sure to use the same HTML mark-up shown in the example here below:

`public function my_cystom_report() {
	$sql = "SELECT ...";
	$results = $wpdb->get_results($sql, ARRAY_A);

	// Reports come in three sizes: wide, medium, normal (default).
	wp_slimstat_reports:report_header('my_custom_report_id', 'My Custom Report Inline Help', 'medium', false, '', 'My cool report');

	foreach($results as $a_result){
		echo "<p>{$a_result['resource']} <span>{$a_result['countresults']}</span></p>";
	}
	
	wp_slimstat_reports:report_footer();
}`

Then let WP SlimStat know about it:

`add_action('wp_slimstat_custom_report', 'my_cystom_report');`

Save your file as `my_custom_report.php` and then [follow these instructions](http://codex.wordpress.org/Writing_a_Plugin#Standard_Plugin_Information) on how to make a plugin out of that file.

= Can I disable outbound link tracking on a given link? =
Yes, you can. This is useful if you notice that, after clicking on a Lightbox-powered thumbnail, the image doesn't open inside the popup window as expected.
Let's say you have a link associated to Lightbox (or one of its clones):

`<a href="/wp-slimstat">Open Image in LightBox</a>`

Change it to:

`<a href="/wp-slimstat" class="noslimstat">Open Image in LightBox</a>`

You can also use the corresponding setting under Options > Advanced, and disable outbound link tracking for *all* the external links in your site.

= Why does WP SlimStat show more page views than actual pages clicked by a user? =
"Phantom" page views can occur when a user's browser does automatic feed retrieval,
[link pre-fetching](https://developer.mozilla.org/en/Link_prefetching_FAQ), or a page refresh. WP SlimStat tracks these because they are valid
requests from that user's browser and are indistinguishable from user link clicks. You can ignore these visits setting the corresponding option
in SlimStat > Settings > Filters

= Why can't WP SlimStat track visitors using IPv6? =
IPv6 support, as of today, is still really limited both in PHP and MySQL. There are a few workarounds that could be implemented, but this
would make the DB structure less optimized, add overhead for tracking regular requests, and you would have a half-baked product.

= How do I stop WP SlimStat from tracking spammers? =
Go to SlimStat > Settings > Filters and set "Ignore Spammers" to YES.

= How do I stop WP SlimStat from recording new visits on my site? =
Go to SlimStat > Settings > General and set "Activate tracking" to NO.

= Can I add/show reports on my website? =
Yes, you can. WP SlimStat offers two ways of displaying its reports on your website.

*Via shortcodes*

Please download and install [WP SlimStat Shortcodes](http://wordpress.org/extend/plugins/wp-slimstat-shortcodes/) to enable shortcode support in WP SlimStat.

*Using the API*

You will need to edit your template and add something like this where you want your metrics to appear:

`// Load WP SlimStat DB, the API library exposing all the reports
require_once(WP_PLUGIN_DIR.'/wp-slimstat/admin/view/wp-slimstat-db.php');

// Initialize the API. You can pass a filter in the options, i.e. show only hits by people who where using Firefox, any version
wp_slimstat_db::init('browser contains Firefox');

// Use the appropriate method to display your stats
echo wp_slimstat_db::count_records('1=1', '*', false);`

*Available methods*

* `count_records($where_clause = '1=1', $column = '*', $use_filters = true, $use_date_filters = true)` - returns the number of records matching your criteria
 * **$where_clause** is the one used in the SQL query
 * **$column**, if specified, will count DISTINCT values
 * **$use_filters** can be true or false, it enables or disables previously set filters (useful to count ALL the records in the database, since by default a filter for the current month is enabled)
 * **$use_date_filters** can be set to false to count ALL the pageviews from the beginning of time
* `count_bouncing_pages()` - returns the number of [pages that 'bounce'](http://en.wikipedia.org/wiki/Bounce_rate#Definition)
* `count_exit_pages()` - returns the number of [exit pages](http://support.google.com/analytics/bin/answer.py?hl=en&answer=2525491)
* `get_recent($column = 'id', $custom_where = '', $join_tables = '', $having_clause = '')` - returns recent results matching your criteria
 * **$column** is the column you want group results by
 * **$custom_where** can be used to replace the default WHERE clause
 * **$join_tables** by default, this method return all the columns of wp_slim_stats; if you need to access (join) other tables, use this param to list them: `tb.*, tss.*, tci.*`
 * **$having_clause** can be used to further filter results based on aggregate functions
* `get_popular($column = 'id', $custom_where = '', $join_tables = '')`
 * **$column** is the column you want group results by
 * **$custom_where** can be used to replace the default WHERE clause
 * **$_more_columns** to 'group by' more than one column and return the corresponding rows

*Examples*

Recent Posts: 

`wp_slimstat_db::init('content_type equals post');
$results = wp_slimstat_db::get_recent('t1.resource');
foreach ($results...`

Top Languages last month:

`wp_slimstat_db::init('month equals '.date('n', strtotime('-1 month')));
$results = wp_slimstat_db::get_popular('t1.language');
foreach ($results...`

== Screenshots ==

1. What's happening right now on your site
2. All the information at your fingertips
3. Configuration panels offer flexibility and plenty of options
4. Mobile view, to keep an eye on your stats on the go
5. Access your stats from within WordPress for iOS

== Changelog ==

= 3.3.6 =
* [New] Since you've asked, we added a datepicker to the filters
* [Update] MaxMind / Geolocation database updated to October 2013
* [Fix] We had some issues with our repository, which made WP SlimStat unavailable for a while. Sorry for the inconvenience.

= 3.3.5 =
* [Note] Our add-on [Export To Excel](http://slimstat.getused.to.it/addons/wp-slimstat-export-to-excel/) can now export the tabular data that makes up the charts (thank you, [consensus](http://wordpress.org/support/topic/graph-export))
* [New] Now all the charts include comparison data for both metrics
* [Fix] A javascript variable name conflict introduced in version 3.3.4 was affecting some advanced functionality (thank you, [Nanowisdoms](http://wordpress.org/support/topic/expand-details-option-not-working-in-334))
* [Fix] A pretty unique combination of settings was affecting the way the Spy View data was being listed (thank you, [Nanowisdoms](http://wordpress.org/support/topic/live-visitor-as-in-currently-still-on-the-site-view))

= 3.3.4 =
* [Note] Now you can export the data from your User Overview custom report, with the Export to Excel add-on!
* [New] Option that allows you to choose if you want to disable outbound links under Right Now (thank you, [STONE5572](http://wordpress.org/support/topic/right-now-tab-not-working-since-update-to-333))
* [New] 'Today' and 'Yesterday' one-click filters added to the Summary report (thank you, [Nanowisdoms](http://wordpress.org/support/topic/dashboard-and-other-widgets-set-default-date-ranges))
* [New] You can finally track admin pages in WP SlimStat, and see what your users do in your admin. Go to SlimStat > Settings > General to enable this feature (thank you, [Nanowisdoms](http://wordpress.org/support/topic/30-50-of-visitors-and-pageviews-not-being-recorded?replies=1))
* [Update] *Please Note* The API/class to render the data was renamed from wp_slimstat_boxes to wp_slimstat_reports. Make sure to update your custom code accordingly!
* [Update] WP SlimStat Dashboard Widgets has been updated to reflect the renaming here above
* [Update] The icon to highlight outbound links under Right Now has been updated to differentiate it even more from the one associated to inbound links (thank you, [Nanowisdoms](http://wordpress.org/support/topic/question-about-outbound-links))
* [Fix] An SQL injection vulnerability (exploitable only if the admin was logged in) was discovered and patched (thank you, maty)
* [Fix] PHP Warning if users has still old format for options (thank you, [troy7890](http://wordpress.org/support/topic/php-error-warning-stripos-expects-parameter-1-to-be-string-array))
* [Fix] PHP Warning under Top Known Visitors (thank you, [Focus3D](http://wordpress.org/support/topic/undefined-index-total-in-wp-slimstat-boxesphp-line-612))

= 3.3.3 =
* [Note] Two new add-ons join the team: Custom DB (to use external DBs to store SlimStat's data) and Label Your IP Addresses. [Check them out](http://slimstat.getused.to.it/addons/)!
* [New] Option that allows you to choose if you want to see your users' Display Name or their actual username (thank you, [psn](http://wordpress.org/support/topic/show-user-name-1))
* [New] More WordPress hooks (filters) were added to the tracking engine, documentation coming soon...
* [New] Outbound links are now shown under Right Now, to give you an even more detailed view of what a visitor did on your site (thank you, [Tommk](http://wordpress.org/support/topic/question-about-outbound-links))
* [Update] Your own domain is not counted among the referring sites anymore (thank you, [Ovidiu and Ursula](http://wordpress.org/support/topic/a-much-requested-feature))
* [Update] Added code to enable a 'network view' of the reports via add-on (coming soon)
* [Update] MaxMind / Geolocation database updated to September 2013
* [Fix] A screen resolution was being associated to search engines and other bots

= 3.3.2 =
* [New] Option that allows you to choose if you want to see the user agent string or the user friendly browser name (thank you, [GermanKiwi](http://wordpress.org/support/topic/default-browser-user-agent-reported-by-slimstat))
* [Update] Minor CSS tweaks
* [Fix] A bug was affecting some URLs for multisite installs using subfolders (thank you, [meldean](http://wordpress.org/support/topic/top-pages-links-not-working))
* [Fix] The entire suite of add-ons has been updated to include some crucial code optimizations

= 3.3.1 =
* [Note] If you are upgrading from WP SlimStat 2.9.2 or earlier, you MUST first install version 3.0 and then the latest version available
* [New] Two new reports added to the Visitors tab: Recent and Top (registered) Users. If you don't see them, go to SlimStat > Settings > Maintenance and click on Reset Tabs
* [Update] The new feature to use a separate database has been improved and will be available soon as an add-on on [our store](http://slimstat.getused.to.it/)
* [Update] The information about your visitors' type (mobile, desktop, crawler) has been reintroduced in the form of an icon next to the operating system under Right Now (thank you, [consensus](http://wordpress.org/support/topic/modality))
* [Update] [Flot](http://www.flotcharts.org/), the library we use to draw our charts, has been updated to version 0.8.1 
* [Fix] A bug introduced in 3.3 was preventing the autopurge functionality from working as expected (thank you, [Inposure](http://wordpress.org/support/topic/database-error-115))
* [Fix] The new 'user agent' column was creating a conflict in the database (unique key). We solved the issue and also enabled the updating of old entries in the database
* [Fix] A bug was affecting the pagination under Right Now in some rare circumstances

= 3.3 =
* [Note] We've just reached the 500,000 downloads mark! Thank you to all those who believe in this project.
* [Note] We're testing WP SlimStat with [MP6](http://wordpress.org/plugins/mp6/) and aside from a few tweaks, it seems to work pretty well with that custom admin theme
* [New] New filters allow you to use a separate database for WP SlimStat. The add-on to leverage this feature is coming soon!
* [New] The actual User Agent string is now recorded into the database (not retroactively, sorry) and can be searched and filtered
* [New] Now you can use negative intervals in date filters (thank you, [helices](http://wordpress.org/support/topic/view-3-months-or-1-year))
* [Update] Browscap hasn't been updated in a while and is starting to be less reliable. Our heuristic browser detector has been improved to overcome this
* [Update] MaxMind / Geolocation database updated to July 2013
* [Update] [AmMap](http://www.ammap.com/) (World Map) has been updated to version 3.3.3
* [Fix] Filters on user agents are now matched against the original UA, not the one returned by Browscap (thank you, [GermanKiwi](http://wordpress.org/support/topic/default-browser-user-agent-reported-by-slimstat))
* [Fix] Bug on uninstall affecting one of the tables used by WP SlimStat (thank you, [mkilian](http://wordpress.org/support/topic/small-confusion-of-base_prefix-vs-prefix-visible-when-using-multisite))

== Donors ==
[7times77](http://7times77.com),
[Andrea Pinti](http://andreapinti.com),
[Bluewave Blog](http://blog.bluewaveweb.co.uk),
[Caigo](http://www.blumannaro.net),
[Christian Coppini](http://www.coppini.me),
Dave Johnson,
[Dennis Kowallek](http://www.adopt-a-plant.com),
[Damian](http://wipeoutmedia.com),
[Edward Koon](http://www.fidosysop.org),
Fabio Mascagna,
[Gabriela Lungu](http://www.cosmeticebio.org),
Gary Swarer,
Giacomo Persichini,
Hal Smith,
[Hans Schantz](http://www.aetherczar.com),
Hajrudin Mulabecirovic,
[Herman Peet](http://www.hermanpeet.nl),
John Montano, 
[La casetta delle pesche](http://www.lacasettadellepesche.it),
[Mobilize Mail](http://blog.mobilizemail.com),
Mora Systems,
Neil Robinson,
[Ovidiu](http://pacura.ru/),
[Sahin Eksioglu](http://www.alternatifblog.com),
[Saill White](http://saillwhite.com),
[Sarah Parks](http://drawingsecretsrevealed.com),
[Sebastian Peschties](http://www.spitl.de),
[Sharon Villines](http://sociocracy.info), 
[SpenceDesign](http://spencedesign.com),
Stephane Sinclair,
[Stephen Korsman](http://blog.theotokos.co.za),
[The Parson's Rant](http://www.howardparsons.info),
Thomas Weiss,
Wayne Liebman,
Willow Ridge Press

== Special Thanks To ==

* [Davide Tomasello](http://www.davidetomasello.it/), for all the great feedback he sent me and the time spent looking for bugs and inconsistencies
* [Thomas Nielsen](http://www.bogt.dk/), for his generous donation and for helping with the new icon set.
