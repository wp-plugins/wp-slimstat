﻿=== WP Slimstat ===
Contributors: coolmann
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BNJR5EZNY3W38
Tags: analytics, tracking, reports, analyze, wassup, geolocation, online users, spider, tracker, pageviews, stats, maxmind, statistics, statpress
Requires at least: 3.8
Tested up to: 4.2
Stable tag: 4.0.2

== Description ==
[youtube https://www.youtube.com/watch?v=iJCtjxArq4U]

= Key Features =
* Real-time activity log, server latency, heatmaps, email reports, export data to Excel, and much more
* Compatible with W3 Total Cache, WP SuperCache and most caching plugins
* Accurate IP geolocation, browser and platform detection (courtesy of [MaxMind](http://www.maxmind.com/) and [Browscap](http://browscap.org))
* Available in multiple languages: English, Chinese (沐熙工作室), Farsi ([Dean](http://www.mangallery.net)), French (Michael Bastin, Jean-Michel Venet, Yves Pouplard, Henrick Kac), German (TechnoViel), Italian, Japanese (h_a_l_f), Portuguese, Russian ([Vitaly](http://www.visbiz.org/)), Spanish ([WebHostingHub](http://www.webhostinghub.com/)), Swedish (Per Soderman). Is your language missing or incomplete? [Contact Us](http://support.getused.to.it/) if you would like to share your localization.
* World Map that works on your mobile device, too (courtesy of [amMap](http://www.ammap.com/)).

= What are people saying about Slimstat? =
* One of the 15+ Cool Free SEO Plugins for WordPress - [udesign](http://www.pixeldetail.com/wordpress/free-seo-plugins-for-wordpress/)
* Thanks you for such an excellent plugin. I am using it to kick Jetpack out of all the wordpress installations that I manage for myself and others - [robertwagnervt](http://wordpress.org/support/topic/plugin-wp-slimstat-excellent-but-some-errors-on-activating)
* I like Slimstat very much and so I decided to use it instead of Piwik - [Joannes](http://wordpress.org/support/topic/plugin-wp-slimstat-slimstat-and-privacy)
* Read all the [reviews](http://wordpress.org/support/view/plugin-reviews/wp-slimstat) and feel free to post your own!

= Requirements =
* WordPress 3.8+
* PHP 5.3+
* MySQL 5.0.3+
* At least 5 MB of free web space
* At least 5 MB of free DB space
* At least 4 Mb of free PHP memory for the tracker (peak memory usage)
* IE9+ or any browser supporting HTML5, to access the reports

= Premium Add-ons =
Visit [our website](http://slimstat.getused.to.it/addons/) for a list of available extensions.

= Free Add-ons =
* [WP Slimstat Shortcodes](http://wordpress.org/extend/plugins/wp-slimstat-shortcodes/) allows you to share your reports with your readers

== Installation ==

0. **If you are upgrading from 2.8.4 or earlier, you MUST first install [version 3.0](https://downloads.wordpress.org/plugin/wp-slimstat.3.0.zip) (deactivate/activate) and then upgrade to the latest release available**
1. In your WordPress admin, go to Plugins > Add New
2. Search for WP Slimstat
3. Click on **Install Now** under WP Slimstat and then activate the plugin
4. Make sure your template calls `wp_footer()` or the equivalent hook somewhere (possibly just before the `</body>` tag)
5. Go to Slimstat > Settings > Maintenance tab > MaxMind IP to Country section and click on "Install GeoLite DB" to detect your visitors' countries based on their IP addresses
6. If your `wp-admin` folder is not publicly accessible, make sure to check the [FAQs](http://wordpress.org/extend/plugins/wp-slimstat/faq/) to see if there's anything else you need to do

Please note: if you decide to uninstall Slimstat, all the stats will be **PERMANENTLY** deleted from your database. Make sure to setup a database backup (wp_slim_*) to avoid losing your data.

== Frequently Asked Questions ==

Our knowledge base is available on our [support center](https://slimstat.freshdesk.com/support/solutions) website.

== Screenshots ==

1. **Overview** - Your website traffic at a glance
2. **Activity Log** - A real-time view of your visitors' whereabouts 
3. **Settings** - Plenty of options to customize the plugin's behavior
4. **Interactive World Map** - See where your visitors are coming from
5. **Responsive layout** - Keep an eye on your reports on the go

== Changelog ==

= 4.0.2 =
* [Note] There seem to be some issues with the tracker not being updated throughout the CDN. If you are using this service, please disable it temporarily (Settings > Advanced > Enable CDN = No) until this is resolved. [We are in touch](https://github.com/jsdelivr/jsdelivr/issues/2632#issuecomment-101994217) with the team managing the CDN.
* [Fix] Some users reported a PHP syntax error message related to a short syntax used by the heuristic browser detection script (thank you, [engesco](https://wordpress.org/support/topic/error-with-new-plug-in-update?replies=27#post-6949271))

= 4.0.1 =
* [Note] Version 4.0 had a bumpy start, but that's expected when something radically new is released to the public. We thank you for your patience while we addressed the bugs that didn't surface during our tests. 
* [Note] Make sure to uninstall the Dashboard Widgets add-on before upgrading to Slimstat 4.0, or you might get a white screen of death. If this is the case, please remove the folder wp-content/plugins/wp-slimstat-dashboard-widgets via FTP. You will not lose your data.
* [New] Say hello to your new Dashboard Widgets. We decided to merge our free add-on into the main plugin: this way you don't have to deal with a separate software, our update cycle is streamlined, and performance increases. You can always deactivate this integration by using the corresponding option under Settings > General.
* [Fix] A few people pointed out a Unexpected T_FUNCTION parse error. Slimstat officially requires PHP 5.3 to function properly. Nevertheless, we implemented a workaround so that people with PHP 5.2 can still enjoy all the power of our plugin. Thank you for your patience.
* [Fix] MySQL Error 121 was preventing the plugin from creating the new table structure, if MySQL was configured to work in strict mode (thank you, [wvploeg](https://wordpress.org/support/topic/after-update-it-stopped-working?replies=6))
* [Fix] If you compile PHP with certain flags on Ubuntu, gzopen is not available (thank you, [larryisthere](https://wordpress.org/support/topic/geolite-db-installation-issue-on-ubuntu-trusty?replies=2))

= 4.0 =
* [Note] A brave new world is now ready to be explored: Slimstat 4.0. This version introduces a totally redesigned database architecture, new streamlined tracking code, new heuristic user agent parser, new filters and much more. You will surely notice the performance improvements!
* [Note] Our dev team should have read [this article](http://blog.codinghorror.com/maybe-normalizing-isnt-normal/) a long time ago. But it's never too late, and we can guarantee you that the new denormalized table structure will make your report generation so quick that your jaw will drop. Sure, the table size will increase 50%, but in the age where space is cheap, the real precious resource is time. The time you won't have to wait for your report to appear!
* [Note] Upon update, Slimstat will convert the old table structure to the new one. Just to stay on the safe side, the old tables will not be removed (wp_slim_stats will be renamed to wp_slim_stats_3). After a transition period, we will offer the option to remove the old tables with a button in the settings.
* [Note] Please make sure that your MySQL user can issue a RENAME command.
* [Note] We are now working on our premium add-ons to make them compatible with Slimstat 4.0. Some of them might stop working until a new update is available.
* [New] MaxMind upload folder path can now be filtered (thank you, [chrisl27](https://wordpress.org/support/topic/filter-for-maxmind-path)).
* [New] The new tracker is measuring both the screen resolution and the viewport size. [Here](http://www.quirksmode.org/mobile/viewports.html) you can find more information on this topic.
* [New] The library wp-slimstat-db.php has been cleaned up and reorganized (20% smaller!). Please note: some of the function signatures have changed (order of parameters), please update your custom code accordingly or contact us for more information.
* [New] Internal downloads are now tracked as regular pageviews, with content_type = download. This allows to make our filters more intuitive and our reports faster.
* [New] The table wp_slim_events will now store all the information regarding events happening on your pages (including the coordinates of clicks for the heatmap).
* [New] Inline data attributes on links will tell you right away if an external URL will be tracked or not.
* [New] Custom reports can now be added to ANY screen, and soon you will be able to move any built-in report to any screen.
* [Update] We are making our source code easier to read, by applying some well established best practices about indentation, spacing and variable names.
* [Update] Removed the chart Average Pageviews per Visit, which required a complex SQL query to be generated, and didn't convey any key information, according to a quick survey we had among some of our users. 
* [Update] The Spy View report under the Overview tab has been merged with the Real-Time log, since users were pointing out that it was confusing to have two separate reports displaying pretty much the same information.
* [Update] We decided to hide some reports under the Site Analysis tab by default. They are not gone, and can be quickly activated by enabling the corresponding checkbox under Screen Options.
* [Update] The browser CSS version is not tracked anymore.
* [Update] Google+1 clicks are not tracked/supported anymore.
* [Update] Vitaly has sent us the latest version of the Russian localization. Way to go!
* [Fix] Implemented a more robust fix for the issue with download_url throwing an undefined function error (this is supposed to be part of [WP Core](https://codex.wordpress.org/Function_Reference/download_url)!)
* [Fix] When dragging boxes around, the placeholder was not being displayed in the right place.

= 3.9.9 =
* [Fix] Some users get an error where download_url is undefined. This is a WordPress core function, so we're really not sure why that is happening to those few users. We included a fix that makes sure the function exists before calling it.

= 3.9.8.2 =
* [Note] Browscap.org just released a new version of their database, but it looks like [it has some issues](https://groups.google.com/forum/#!topic/browscap/x0onOyHz-D0). We'll wait for a more stable release and then update our optimized version of their db.
* [Fix] Some users are reporting problems related to the compressed (gzipped) version of the MaxMind GeoLite DB introduced in version 3.9.8.1. We updated our code to unzip the database before the tracker uses it. Please note: if your install is working as expected, you can skip this update.

= 3.9.8.1 =
* [Note] After further discussing with the repo moderators the incompatibility issue regarding the license under which MaxMind GeoLite is released, we were able to implement a much easier way to enable the geolocation functionality in Slimstat. There's no need to download a separate plugin anymore! Just go to Slimstat > Settings > Maintenance tab, and click on Install GeoLite DB. Of course, you can always deactivate this feature by clicking on the corresponding button under the Maintenance tab.
* [Note] If you had downloaded and installed our Get Country add-on, you can now *uninstall* it from your server. We apologize for any inconvenience this might have caused.
* [New] A warning message is now displayed on the reports screens to remind you to install the GeoLite database. You can hide this message by enabling the corresponding option under Slimstat > Settings > Reports tab > Miscellaneous section.
* [Update] Some of the Settings screens have been cleaned up and reorganized
* [Update] Cleaned up the interface for better readability
* [Update] Removed banner from our partner ManageWP

= 3.9.8 =
* [Note] The team who manages the WordPress Plugin Repository notified us that since the [MaxMind GeoLite library](http://dev.maxmind.com/geoip/legacy/geolite/) used by Slimstat to geolocate visitors is released under the Creative Commons BY-SA 3.0 license, it violates the repository guidelines, and cannot be bundled with the plugin. We were required to remove the code and alter the plugin so that this functionality becomes optional. We apologize for the inconvenience. However, the only immediate consequence is that your visitors' country will not be identified; everything else will still work as usual. You can download the geolocation DB as a [separate add-on](http://slimstat.getused.to.it/downloads/get-country/) on our store, free of charge. Don't forget to enter your license key in the corresponding field under Slimstat > Add-ons, to receive free updates!
* [New] A few new options under Slimstat > Settings > General tab > WordPress Integration section, allow you to have more control over the information displayed in the Posts admin screen (thank you, Brad).

= 3.9.7 =
* [Note] The uninstall routine now deletes the archive table (wp_slim_stats_archive) along with all the other tables (thank you, KalleL)
* [New] Some users who are using our "track external sites" feature, were getting an error saying that no 'Access-Control-Allow-Origin' header was present on the requested resource. We've added a new option under Settings > Advanced that allows you to specify what domains to allow. Please refer to [this page](http://www.w3.org/TR/cors/#security) for more information about the security implications of allowing an external domain to submit AJAX requests to your server.
* [New] Added debugging information (most recent tracker error code) under Slimstat > Settings > Maintenance tab > Debugging. This information is useful to troubleshoot issues with the tracker. Please include it when sending a support request.
* [Fix] The option to delete pageviews based on given filters (Settings > Maintenance > Data Maintenance) was not working as expected (thank you, [kentahayashi](https://wordpress.org/support/topic/cant-delete-pageviews-on-version-396))
* [Fix] The uninstall script was not deleting all the tables as expected (thank you, [KalleL](https://wordpress.org/support/topic/unable-to-uninstall-wp-slimstat-from-db))
* [Fix] We've implemented [Marc-Alexandre's new recommendations](http://blog.sucuri.net/2015/02/security-advisory-wp-slimstat-3-9-5-and-lower.html) to further tighten up our SQL queries.
* [Fix] The new encryption key was affecting the way external sites could be tracked. You can now track non-WP sites again: please make sure to copy and paste the new tracking code (Settings > Advanced) right before your closing BODY tag at the end of your pages.

= 3.9.6 =
* [Note] The security of our users' data is our top priority, and for this reason we tightened our SQL queries and made our encryption key harder to guess. If you are using a caching plugin, please flush its cache so that the tracking code can be regenerated with the new key. Also, if you are using Slimstat to track external websites, please make sure to replace the tracking code with the new one available under Settings > Advanced. As usual, feel free to contact us if you have any questions.
* [Note] Added un-minified js tracker to the repo, for those who would like to take a look at how things work.
* [New] Introduced option to ignore bots when in Server-side mode.
* [Update] Cleaned up the Settings/Filters screen by consolidating some options.
* [Update] AmMap has been updated to version 3.13.1
* [Update] MaxMind GeoLite IP has been updated to the latest version (2015-02-04).
* [Fix] Patched a rare SQL injection vulnerability exploitable using a bruteforce attack on the secret key (used to encrypt the data between client and server).
* [Fix] Increased checks on SQL code that stores data in the database (maybe_insert_row).
* [Fix] Report filters could not be removed after being set.

= 3.9.5 =
* [Note] Some of our add-ons had a bug preventing them from properly checking for updates. Please [contact us](http://support.getused.to.it) if you need to obtain the latest version of your add-ons.
* [Update] The Save button in the settings is now always visible, so that there is no need to scroll all the way to the bottom to save your options.
* [Update] More data layer updates introduced in wp_slimstat_db. Keep an eye on your custom add-ons!
* [Fix] Pagination was not working as expected when a date range was set in the filters (thank you, [nick-v](https://wordpress.org/support/topic/paging-is-broke))

= 3.9.4 =
* [Note] The URL of the CDN has changed, and is now using the official WordPress repository as a source: cdn.jsdelivr.net/wp/wp-slimstat/trunk/wp-slimstat.js - Please update your "external" tracking codes accordingly.
* [Note] The structure of the array **wp_slimstat_db::$sql_filters** has changed! Please make sure to update your custom code accordingly. Feel free to [contact us](http://support.getused.to.it) for more information.
* [New] The wait is over. Our heatmap add-on is finally [available on our store](http://slimstat.getused.to.it/downloads/heatmap/)! We would like to thank all those who provided helpful feedback to improve this initial release!
* [New] Our [knowledge base](https://slimstat.freshdesk.com/support/solutions) has been extended with a list of all the actions and filters available in Slimstat.
* [Fix] The Add-on update checker had a bug preventing the functionality to work as expected. Please make sure to get the latest version of your premium add-ons!
* [Fix] Date intervals were not accurate because of a bug related to calculating timezones in MySQL (thank you, [Chrisssssi](https://wordpress.org/support/topic/conflicting-data)).
* [Fix] Line height of report rows has been added to avoid conflicts with other plugins tweaking this parameter in the admin (thank you, [yk11](https://wordpress.org/support/topic/widgets-bottom-is-cut-off)).

= 3.9.3 =
* [Note] We're starting to work on a completely redesigned data layer, which will require less SQL resources and offer a much needed performance improvement. Stay tuned.
* [New] Three new settings to turn off the tracker completely on specific links (internal and external), by class name, rel attribute or simply by URL.
* [Update] MaxMind GeoLite IP has been updated to the latest version (2015-01-06).
* [Fix] Bug affecting our add-ons setting page, in some specific circumstances (thank you, Erik).

= 3.9.2 =
* [New] Welcome our recommended partner, [ManageWP](https://managewp.com/?utm_source=A&utm_medium=Banner&utm_content=mwp_banner_25_300x250&utm_campaign=A&utm_mrl=2844). You will get a 10% discount on their products using our affiliation link.
* [Fix] XSS Vulnerability introduced by the new Save Filters functionality (thank you, [Ryan](https://wpvulndb.com/vulnerabilities/7744)).

= 3.9.1 =
* [New] Quickly delete single pageviews in the Real-Time Log screen
* [New] Option to fix an issue occurring when the DB server and the website are in different timezones. Please disable this option if your charts seem to be off!
* [New] Using the new [WP Proxy CDN feature](https://github.com/jsdelivr/jsdelivr/issues/2632). Please contact us if you notice any problems with this new option, as this feature is still being tested.
* [Update] Reintroduced the NO PANIC button under Settings > Maintenance > Miscellaneous
* [Fix] Conflict with WP-Jalali, which forces date_i18n to return not western numerals but their Farsi representation

= 3.9 =
* [Note] Announcing our latest add-on: heatmaps! Get your free copy of our beta: contact our support team today.
* [New] Section under Settings > Filters that allows you to specify what links you want to "leave alone", so that the tracker doesn't interfere with your lightbox treatments.
* [New] You can now turn on the option to collect mouse coordinates for internal links, which will be used to draw the heatmap on your pages.
* [New] Operator "is included in" has been added to search matches in lists of strings (see [W3resources](http://www.w3resource.com/mysql/string-functions/mysql-find_in_set-function.php), thank you [pchrisl](https://github.com/27pchrisl/wp-slimstat/commit/5a5bc3b8c21ec16445292d8674d669c37c2a08b4))
* [New] Added new reports: Top Bounce Pages, Top Exit Pages, Recent Exit Pages (thank you, [Random Dev](https://wordpress.org/support/topic/no-visitor-path-through-site-wslimstat))
* [Update] Partial overhaul of the javascript tracker. We reintroduced the new algorithm to track pageviews, which now avoids the problem of triggering the popup blocker on links opening in a new tab. 
* [Update] Added browser and operating system to Spy View report
* [Update] MaxMind GeoLite IP has been updated to the latest version (2014-12-02)
* [Fix] Bug in archiving old pageviews under certain circumstances (thank you, Thomas)
* [Fix] Added max height to overlay, for those who have very long lists of saved filters
* [Fix] The button to reset date filters was not being displayed in some cases (thank you, [RangerPretzel](https://wordpress.org/support/topic/custom-data-range-in-slimstat-produces-charts-with-days-that-have-0-pageviews))
* [Fix] Charts were not accurate when a custom interval was selected and the mysql server's timezone was different from the web server timezone (thank you, [RangerPretzel](https://wordpress.org/support/topic/custom-data-range-in-slimstat-produces-charts-with-days-that-have-0-pageviews))

= 3.8.5 =
* [Update] Show notices only to admin users (thank you, [thisismyway](https://wordpress.org/support/topic/hide-notifications-for-non-admins))
* [Fix] The javascript tracker had been changed to deal with popup blocker issues, but the new code was causing even more problems to other people. Implemented a synchronous solution to make everybody happy! (thank you, bishoph)

= 3.8.4 =
* [New] You can now archive old pageviews, instead of deleting them
* [Update] Code optimizations to the Javascript tracker (and a bugfix - thank you, [themadproducer](https://wordpress.org/support/topic/external-links-problem))
* [Fix] Fixed a corrupted browscap data file (thank you, [crzyhrse](https://wordpress.org/support/topic/clobbered-my-sites-again))
* [Fix] Do not refresh the Real-Time log if a date filter is set (thank you, [asylum119](https://wordpress.org/support/topic/viewing-yesterdays-stats-still-auto-refreshes))

= 3.8.3 =
* [Update] Browscap v5035 - November 4, 2014 (this should fix all the issues with recent Firefox versions)
* [Fix] The originating IP address was not being ignored, if it was the same as the IP address (thank you, [morcom](https://wordpress.org/support/topic/real-time-log-originating-ip-on-all-entries))
* [Fix] Visits in map were not correctly displayed (thank you, [psn](https://wordpress.org/support/topic/numbers-of-visit-in-maps-shows-zero))

= 3.8.2 =
* [New] You can now load, save and delete filters (or "goal conversions", in Google's terminology). Please test this new functionality and let us know if you find any bugs!
* [Update] Added new WordPress filter hooks to initialize the arrays storing the data about the pageview (thank you, Tayyab)
* [Update] [AmMap](http://www.amcharts.com/download/) version 3.11.3
* [Update] MaxMind GeoLite IP has been updated to the latest version (2014-11-05)
* [Fix] Bug affecting links opening in a new tab/window (target=_blank). Our thanks go to all the users who helped us troubleshoot the issue!
* [Fix] Backward compatibility of new date/time filters with old ones
* [Fix] Issue with counters on Posts/Pages screen (thank you, [vaguiners](https://wordpress.org/support/topic/0-visits-in-every-post-in-list-of-post-after-update))
* [Fix] Warning about undefined array index in date/time filters (thank you, Chris)
* [Fix] Some tooltips were being displayed outside of the browser viewport (thank you, Vitaly)

= 3.8.1 =
* It was only released on Github to solve a critical bug affecting external links

= 3.8 =
* [New] We increased the filter granularity to the minute, so that now you can see who visited your website between 9 am and 10.34 am (thank you, [berserk77](https://wordpress.org/support/topic/need-help-with-some-filtering-features))
* [New] If admin is served over HTTPS but IP lookup service is not, don't use inline overlay dialog (thank you, [509tyler](https://wordpress.org/support/topic/https-overlay-suggestion))
* [Update] Javascript libraries: qTip v2.2.1 and SlimScroll 1.3.3
* [Fix] Outbound links from within the admin were not tracked as expected (thank you [mobilemindtech](https://wordpress.org/support/topic/outbound-links-problem-in-version-374))
* [Fix] Firewall Fix add-on was not tracking the originating ip's country as expected (thank you, JeanLuc)

== Special Thanks To ==

* [Vitaly](http://www.visbiz.org/), who volunteers quite a lot of time for QA, testing, and for his Russian localization.
* Davide Tomasello, who provided great feedback and plenty of ideas to take this plugin to the next level.

== Supporters ==
Slimstat is an Open Source project, dependent in large parts on donations. [This page](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BNJR5EZNY3W38)
is for those who want to donate money - be it once, be it regularly, be it a small or a big amount. Everything is set up for an easy donation process.
Try it out, you'll be amazed how good it feels! If you're on a tight budget, [a review](http://wordpress.org/support/view/plugin-reviews/wp-slimstat) for Slimstat is still a nice way to say thank you!

[7times77](http://7times77.com),
[Andrea Pinti](http://andreapinti.com),
Beauzartes,
[Bluewave Blog](http://blog.bluewaveweb.co.uk),
[BoldVegan](boldvegan.com),
[Caigo](http://www.blumannaro.net),
[Christian Coppini](http://www.coppini.me),
Dave Johnson,
[David Leudolph](https://flattr.com/profile/daevu),
[Dennis Kowallek](http://www.adopt-a-plant.com),
[Damian](http://wipeoutmedia.com),
[Edward Koon](http://www.fidosysop.org),
Erik Ludvigsson,
Fabio Mascagna,
[Gabriela Lungu](http://www.cosmeticebio.org),
Gary Swarer,
Giacomo Persichini,
Hal Smith,
[Hans Schantz](http://www.aetherczar.com),
Hajrudin Mulabecirovic,
[Herman Peet](http://www.hermanpeet.nl),
John Montano,
Kitty Cooper,
[La casetta delle pesche](http://www.lacasettadellepesche.it),
Mobile Lingo Inc,
[Mobilize Mail](http://blog.mobilizemail.com),
Mora Systems,
Motionart Inc,
Neil Robinson,
[Ovidiu](http://pacura.ru/),
Rocco Ammon,
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

== Tools of the trade, in alphabetical order ==
[Duri.Me](http://duri.me/),
[Filezilla](https://filezilla-project.org/),
[Fontello](http://fontello.com/),
[Gimp](http://www.gimp.org/),
[Google Chrome](https://www.google.com/intl/en/chrome/browser/),
[poEdit](http://www.poedit.net/),
[Notepad++](http://notepad-plus-plus.org/),
[Tortoise SVN](http://tortoisesvn.net/),
[WAMP Server](http://www.wampserver.com/en/)