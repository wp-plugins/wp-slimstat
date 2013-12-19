<?php

class wp_slimstat_reports{
	public static $filters = array();
	public static $system_filters = array();

	public static $screen_names = array();
	public static $dropdown_filter_names = array();
	public static $all_filter_names = array();
	
	// Variables used to generate the HTML code for the metaboxes
	public static $current_tab = 1;
	public static $current_tab_url = '';
	public static $plugin_url = '';
	public static $home_url = '';
	public static $ip_lookup_url = 'http://www.infosniper.net/?ip_address=';
	public static $meta_report_order_nonce = '';

	// Tab panels drag-and-drop functionality
	public static $all_reports_titles = array();
	public static $all_reports = '';
	public static $hidden_reports = array();
	
	// Shared descriptions
	public static $chart_tooltip = '';

	/**
	 * Initalizes class properties
	 */
	public static function init(){
		self::$screen_names = array(
			1 => __('Right Now','wp-slimstat'),
			2 => __('Overview','wp-slimstat'),
			3 => __('Visitors','wp-slimstat'),
			4 => __('Content','wp-slimstat'),
			5 => __('Traffic Sources','wp-slimstat'),
			6 => __('World Map','wp-slimstat'),
			7 => __('Custom Reports','wp-slimstat')
		);
		self::$dropdown_filter_names = array(
			'no-filter-selected-1' => '&nbsp;',
			'browser' => __('Browser','wp-slimstat'),
			'country' => __('Country Code','wp-slimstat'),
			'ip' => __('IP Address','wp-slimstat'),
			'searchterms' => __('Search Terms','wp-slimstat'),
			'language' => __('Language Code','wp-slimstat'),
			'platform' => __('Operating System','wp-slimstat'),
			'resource' => __('Permalink','wp-slimstat'),
			'domain' => __('Domain','wp-slimstat'),
			'referer' => __('Referer','wp-slimstat'),
			'user' => __('Visitor\'s Name','wp-slimstat'),
			'no-filter-selected-2' => '&nbsp;',
			'no-filter-selected-3' => __('-- Advanced filters --','wp-slimstat'),
			'plugins' => __('Browser Capabilities','wp-slimstat'),
			'version' => __('Browser Version','wp-slimstat'),
			'type' => __('Browser Type','wp-slimstat'),
			'user_agent' => __('User Agent','wp-slimstat'),
			'colordepth' => __('Color Depth','wp-slimstat'),
			'css_version' => __('CSS Version','wp-slimstat'),
			'notes' => __('Pageview Attributes','wp-slimstat'),
			'outbound_resource' => __('Outbound Link','wp-slimstat'),
			'author' => __('Post Author','wp-slimstat'),
			'category' => __('Post Category ID','wp-slimstat'),
			'other_ip' => __('Originating IP','wp-slimstat'),
			'content_type' => __('Resource Content Type','wp-slimstat'),
			'content_id' => __('Resource ID','wp-slimstat'),
			'resolution' => __('Screen Resolution','wp-slimstat'),
			'visit_id' => __('Visit ID','wp-slimstat')
		);
		self::$all_filter_names = array_merge(self::$dropdown_filter_names, array(
			'hour' => __('Hour','wp-slimstat'),
			'day' => __('Day','wp-slimstat'),
			'month' => __('Month','wp-slimstat'),
			'year' => __('Year','wp-slimstat'),
			'interval' => __('days','wp-slimstat')
		));
		self::$all_reports_titles = array(
			'slim_p1_01' => __('Pageviews (chart)','wp-slimstat'),
			'slim_p1_02' => __('About WP SlimStat','wp-slimstat'),
			'slim_p1_03' => __('Summary','wp-slimstat'),
			'slim_p1_04' => __('Currently Online','wp-slimstat'),
			'slim_p1_05' => __('Spy View','wp-slimstat'),
			'slim_p1_06' => __('Recent Search Terms','wp-slimstat'),
			'slim_p1_08' => __('Top Pages','wp-slimstat'),
			'slim_p1_10' => __('Top Traffic Sources','wp-slimstat'),
			'slim_p1_11' => __('Top Known Visitors','wp-slimstat'),
			'slim_p1_12' => __('Top Search Terms','wp-slimstat'),
			'slim_p1_13' => __('Top Countries','wp-slimstat'),
			'slim_p1_15' => __('Rankings','wp-slimstat'),
			'slim_p1_16' => __('Security Scan','wp-slimstat'),
			'slim_p1_17' => __('Top Language Families','wp-slimstat'),
			'slim_p2_01' => __('Human Visits (chart)','wp-slimstat'),
			'slim_p2_02' => __('Summary','wp-slimstat'),
			'slim_p2_03' => __('Top Languages','wp-slimstat'),
			'slim_p2_04' => __('Top Browsers','wp-slimstat'),
			'slim_p2_05' => __('Top Service Providers','wp-slimstat'),
			'slim_p2_06' => __('Top Operating Systems','wp-slimstat'),
			'slim_p2_07' => __('Top Screen Resolutions','wp-slimstat'),
			'slim_p2_09' => __('Browser Capabilities','wp-slimstat'),
			'slim_p2_10' => __('Top Countries','wp-slimstat'),
			'slim_p2_12' => __('Visit Duration','wp-slimstat'),
			'slim_p2_13' => __('Recent Countries','wp-slimstat'),
			'slim_p2_14' => __('Recent Screen Resolutions','wp-slimstat'),
			'slim_p2_15' => __('Recent Operating Systems','wp-slimstat'),
			'slim_p2_16' => __('Recent Browsers','wp-slimstat'),
			'slim_p2_17' => __('Recent Languages','wp-slimstat'),
			'slim_p2_18' => __('Top Browser Families','wp-slimstat'),
			'slim_p2_19' => __('Top OS Families','wp-slimstat'),
			'slim_p2_20' => __('Recent Users','wp-slimstat'),
			'slim_p2_21' => __('Top Users','wp-slimstat'),
			'slim_p3_01' => __('Traffic Sources (chart)','wp-slimstat'),
			'slim_p3_02' => __('Summary','wp-slimstat'),
			'slim_p3_03' => __('Top Search Terms','wp-slimstat'),
			'slim_p3_04' => __('Top Countries','wp-slimstat'),
			'slim_p3_05' => __('Top Traffic Sources','wp-slimstat'),
			'slim_p3_06' => __('Top Referring Search Engines','wp-slimstat'),
			'slim_p3_08' => __('Spy View','wp-slimstat'),
			'slim_p3_09' => __('Recent Search Terms','wp-slimstat'),
			'slim_p3_10' => __('Recent Countries','wp-slimstat'),
			'slim_p3_11' => __('Top Landing Pages','wp-slimstat'),
			'slim_p4_01' => __('Average Pageviews per Visit (chart)','wp-slimstat'),
			'slim_p4_02' => __('Recent Posts','wp-slimstat'),
			'slim_p4_03' => __('Recent Bounce Pages','wp-slimstat'),
			'slim_p4_04' => __('Recent Feeds','wp-slimstat'),
			'slim_p4_05' => __('Recent Pages Not Found','wp-slimstat'),
			'slim_p4_06' => __('Recent Internal Searches','wp-slimstat'),
			'slim_p4_07' => __('Top Categories','wp-slimstat'),
			'slim_p4_08' => __('Recent Outbound Links','wp-slimstat'),
			'slim_p4_10' => __('Recent Events','wp-slimstat'),
			'slim_p4_11' => __('Top Posts','wp-slimstat'),
			'slim_p4_12' => __('Top Feeds','wp-slimstat'),
			'slim_p4_13' => __('Top Internal Searches','wp-slimstat'),
			'slim_p4_14' => __('Top Search Terms','wp-slimstat'),
			'slim_p4_15' => __('Recent Categories','wp-slimstat'),
			'slim_p4_16' => __('Top Pages Not Found','wp-slimstat'),
			'slim_p4_17' => __('Top Landing Pages','wp-slimstat'),
			'slim_p4_18' => __('Top Authors','wp-slimstat'),
			'slim_p4_19' => __('Top Tags','wp-slimstat'),
			'slim_p4_20' => __('Recent Downloads','wp-slimstat'),
			'slim_p4_21' => __('Top Outbound Links and Downloads','wp-slimstat'),
			'slim_p4_22' => __('Your Website','wp-slimstat'),
			'slim_p7_02' => __('Activity Log','wp-slimstat')
		);

		if (!empty($_GET['page'])) self::$current_tab = intval(str_replace('wp-slim-view-', '', $_GET['page']));
		if (!empty(wp_slimstat_admin::$view_url)) self::$current_tab_url = wp_slimstat_admin::$view_url.self::$current_tab;
		if (!empty(wp_slimstat::$options['ip_lookup_service'])) self::$ip_lookup_url = wp_slimstat::$options['ip_lookup_service'];
		self::$plugin_url = plugins_url('', dirname(__FILE__));
		self::$home_url = home_url();
		self::$meta_report_order_nonce = wp_create_nonce('meta-box-order');


		// Retrieve the order of this tab's panels and which ones are hidden
		$user = wp_get_current_user();
		$page_location = (wp_slimstat::$options['use_separate_menu'] == 'yes')?'slimstat':'admin';

		
		self::$all_reports_titles = apply_filters('slimstat_report_titles', self::$all_reports_titles);

		if (self::$current_tab != 1){
			self::$all_reports = get_user_option("meta-box-order_{$page_location}_page_wp-slim-view-".self::$current_tab, $user->ID);
			self::$all_reports = (self::$all_reports === false)?get_user_option("meta-box-order_{$page_location}_page_wp-slimstat", $user->ID):self::$all_reports; // backward compatible with old settings
		}
		self::$all_reports = (empty(self::$all_reports) || empty(self::$all_reports[0]))?array():explode(',', self::$all_reports[0]);

		// Use default values, if the corresponding option hasn't been initialized
		$old_ids = array_intersect(array('p1_01','p2_01','p3_01','p4_01'), self::$all_reports);

		if (empty(self::$all_reports) || !empty($old_ids)){
			switch(self::$current_tab){
				case 2:
					self::$all_reports = array('slim_p1_01','slim_p1_02','slim_p1_03','slim_p1_04','slim_p1_11','slim_p1_12','slim_p1_05','slim_p1_08','slim_p1_10','slim_p1_13','slim_p1_15','slim_p1_16','slim_p1_17');
					break;
				case 3:
					self::$all_reports = array('slim_p2_01','slim_p2_02','slim_p2_03','slim_p2_04','slim_p2_06','slim_p2_05','slim_p2_07','slim_p2_09','slim_p2_10','slim_p2_12','slim_p2_13','slim_p2_14','slim_p2_15','slim_p2_16','slim_p2_17','slim_p2_18','slim_p2_19','slim_p2_20','slim_p2_21');
					break;
				case 4:
					self::$all_reports = array('slim_p4_01','slim_p4_22','slim_p1_06','slim_p4_07','slim_p4_02','slim_p4_03','slim_p4_05','slim_p4_04','slim_p4_06','slim_p4_08','slim_p4_12','slim_p4_13','slim_p4_14','slim_p4_15','slim_p4_16','slim_p4_17','slim_p4_18','slim_p4_11','slim_p4_10','slim_p4_19','slim_p4_20','slim_p4_21');
					break;
				case 5:
					self::$all_reports = array('slim_p3_01','slim_p3_02','slim_p3_03','slim_p3_04','slim_p3_06','slim_p3_05','slim_p3_08','slim_p3_10','slim_p3_09','slim_p3_11');
					break;
				default:
			}
		}

		// Some boxes are hidden by default
		if (!empty($_GET['page']) && strpos($_GET['page'], 'wp-slim-view-') !== false)
			self::$hidden_reports = get_user_option("metaboxhidden_{$page_location}_page_wp-slim-view-".self::$current_tab, $user->ID);
		else // the script is being called from the dashboard widgets plugin
			self::$hidden_reports = get_user_option("metaboxhidden_{$page_location}", $user->ID);
		
		// Default values
		if (self::$hidden_reports === false){
			switch(self::$current_tab){
				case 2:
					self::$hidden_reports = array('slim_p1_02', 'slim_p1_11', 'slim_p1_12', 'slim_p1_13','slim_p1_17');
					break;
				case 3:
					self::$hidden_reports = array('slim_p2_7', 'slim_p2_09', 'slim_p2_13', 'slim_p2_14', 'slim_p2_15', 'slim_p2_16', 'slim_p2_17', 'slim_p2_18', 'slim_p2_19', 'slim_p2_20', 'slim_p2_21');
					break;
				case 4:
					self::$hidden_reports = array('slim_p4_11', 'slim_p4_12', 'slim_p4_13', 'slim_p4_14', 'slim_p4_15', 'slim_p4_16', 'slim_p4_17');
					break;
				case 5:
					self::$hidden_reports = array('slim_p3_09', 'slim_p3_10');
					break;
				default:
					self::$hidden_reports = array();
			}
		}

		// Filters use the following format: browser equals Firefox|country contains gb
		if (!empty($_REQUEST['fs']) && is_array($_REQUEST['fs'])) self::$filters = $_REQUEST['fs'];
		if (!empty($_POST['f']) && !empty($_POST['o'])) self::$filters[$_POST['f']] = $_POST['o'].' '.(!isset($_POST['v'])?'':$_POST['v']);

		if (!empty($_POST['day'])) self::$filters['day'] = "equals {$_POST['day']}";
		if (!empty($_POST['month'])) self::$filters['month'] = "equals {$_POST['month']}";
		if (!empty($_POST['year'])) self::$filters['year'] = "equals {$_POST['year']}";
		if (!empty($_POST['interval']) && intval($_POST['interval']) != 0) self::$filters['interval'] = "equals {$_POST['interval']}";

		// Some filters only apply to the Right Now and Custom Reports tabs
		if (self::$current_tab != 1 && self::$current_tab != 7){
			self::$filters['starting'] = '';
			self::$filters['direction'] = '';
		}

		// System filters are used to restrict access to the stats based on some settings
		if (wp_slimstat::$options['restrict_authors_view'] == 'yes' && !current_user_can('manage_options')) self::$system_filters['author'] = 'contains '.$GLOBALS['current_user']->user_login;
		
		// Allow third-party add-ons to modify filters before they are used
		self::$filters = apply_filters('slimstat_modify_admin_filters', self::$filters);
		self::$system_filters = apply_filters('slimstat_modify_admin_system_filters', self::$system_filters);

		// Import and initialize the API to interact with the database
		include_once(WP_PLUGIN_DIR."/wp-slimstat/admin/view/wp-slimstat-db.php");
		wp_slimstat_db::init(self::$filters, self::$system_filters);

		// Default text for the inline help associated to the chart
		self::$chart_tooltip = '<strong>'.__('Chart controls','wp-slimstat').'</strong><ul><li>'.__('Use your mouse wheel to zoom in and out','wp-slimstat').'</li><li>'.__('While zooming in, drag the chart to move to a different area','wp-slimstat').'</li><li>'.__('Double click on an empty region to reset the zoom level','wp-slimstat').'</li>';
		self::$chart_tooltip .= (!wp_slimstat_db::$timeframes['current_day']['day_selected'])?'<li>'.__('Click on a data point to display the activity chart for each hour of that day','wp-slimstat').'</li>':'';
	}
	// end init

	public static function fs_url($_keys = array(), $_url = 'none', $_all = false){
		$filtered_url = ($_url == 'none')?self::$current_tab_url:$_url;

		if (!is_array($_keys)){
			if (!empty($_keys))
				$_keys = array($_keys => '');
			else
				$_keys = array();
		}
		
		if ($_all) $_keys = self::$filters;

		foreach($_keys as $a_key => $a_filter){
			if ($a_key == 'no-filter-selected-1') continue;
			$filtered_url .= "&amp;fs%5B$a_key%5D=".urlencode($_keys[$a_key]);
		}

		return $filtered_url;
	}
	
	public static function get_search_terms_info($_searchterms = '', $_domain = '', $_referer = '', $_serp_only = false){
		$query_details = '';
		$search_terms_info = '';

		parse_str("daum=search?q&naver=search.naver?query&google=search?q&yahoo=search?p&bing=search?q&aol=search?query&lycos=web?q&ask=web?q&cnn=search/?query&about=?q&mamma=result.php?q&voila=S/voila?rdata&virgilio=ricerca?qs&baidu=s?wd&yandex=yandsearch?text&najdi=search.jsp?q&seznam=?q&onet=wyniki.html?qt&yam=Search/Web/DefaultCSA.aspx?k&pchome=/search/?q&kvasir=alle?q&arama.mynet=web/goal/1/?q&nova_rambler=search?query", $query_formats);
		preg_match("/(daum|naver|google|yahoo|bing|aol|lycos|ask|cnn|about|mamma|voila|virgilio|baidu|yandex|najdi|seznam|onet|szukacz|yam|pchome|kvasir|mynet|ekolay|rambler)./", $_domain, $matches);
		parse_str($_referer, $query_parse_str);

		if (!empty($query_parse_str['source']) && !$_serp_only) $query_details = __('src','wp-slimstat').": {$query_parse_str['source']}";
		if (!empty($query_parse_str['cd'])) $query_details = __('serp','wp-slimstat').": {$query_parse_str['cd']}";
		if (!empty($query_details)) $query_details = "($query_details)";

		if (!empty($_searchterms)){		
			if (!empty($matches) && !empty($query_formats[$matches[1]])){
				$search_terms_info = '<a class="url" target="_blank" title="'.htmlentities(__('Go to the corresponding search engine result page','wp-slimstat'), ENT_QUOTES, 'UTF-8').'" href="http://'.$_domain.'/'.$query_formats[$matches[1]].'='.urlencode($_searchterms).'"></a> '.htmlentities($_searchterms, ENT_QUOTES, 'UTF-8');
			}
			else{
				$search_terms_info = '<a class="url" target="_blank" title="'.htmlentities(__('Go to the referring page','wp-slimstat'), ENT_QUOTES, 'UTF-8').'" href="'.$_referer.'"></a> '.htmlentities($_searchterms, ENT_QUOTES, 'UTF-8');
			}
			$search_terms_info = "$search_terms_info $query_details";
		}
		return $search_terms_info;
	}

	/**
	 * Generate the HTML that lists all the filters currently used
	 */
	public static function get_filters_html($_filters_array = array()){
		$filters_html = '';

		// Don't display direction and limit results
		$filters_dropdown = array_diff_key($_filters_array, array('direction' => 'asc', 'limit_results' => 20, 'starting' => 0, 'orderby' => 0, 'no-filter-selected-1' => 0));

		if (!empty($filters_dropdown)){
			foreach($filters_dropdown as $a_filter_label => $a_filter_details){
				$a_filter_value_no_slashes = htmlentities(str_replace('\\','', $a_filter_details[1]), ENT_QUOTES, 'UTF-8');
				$filters_html .= "<li>".self::$all_filter_names[$a_filter_label].' '.__(str_replace('_', ' ', $a_filter_details[0]),'wp-slimstat')." $a_filter_value_no_slashes <a class='slimstat-remove-filter' title='".htmlentities(__('Remove filter for','wp-slimstat'), ENT_QUOTES, 'UTF-8').' '.self::$all_filter_names[$a_filter_label]."' href='".self::fs_url($a_filter_label)."'></a></li>";
			}
		}
		if (!empty($filters_html)){
			$filters_html = "<ul class='slimstat-filter-list'>$filters_html</ul>";
		}
		if(count($filters_dropdown) > 1){
			$filters_html .= '<a href="'.self::$current_tab_url.'" id="slimstat-remove-all-filters" class="button-secondary">'.__('Reset All','wp-slimstat').'</a>';
		}

		return ($filters_html != "<span class='filters-title'>".__('Current filters:','wp-slimstat').'</span> ')?$filters_html:'';
	}

	public static function report_header($_id = 'p0', $_postbox_class = '', $_tooltip = '', $_title = ''){
		if (!empty($_postbox_class)) $_postbox_class .= ' ';
		
		$header_buttons = '<a class="slimstat-header-button slimstat-header-button-ajax refresh" title="'.__('Refresh','wp-slimstat').'" href="'.wp_slimstat_reports::fs_url().'"></a>';
		$header_buttons = apply_filters('slimstat_report_header_buttons', $header_buttons, $_id);
		
		$header_tooltip = !empty($_tooltip)?"<i class='slimstat-tooltip-trigger corner'></i><span class='slimstat-tooltip-content'>$_tooltip</span>":'';

		echo "<div class='postbox {$_postbox_class}slimstat' id='$_id'".(in_array($_id, self::$hidden_reports)?' style="display:none"':'')."><div class='hndle'>".(!empty($_title)?$_title:self::$all_reports_titles[$_id])."$header_buttons$header_tooltip</div><div class='inside'>";
		if (wp_slimstat::$options['async_load'] == 'yes') echo '<p class="loading"></p>';
	}

	public static function report_footer(){
		echo '</div></div>';
	}

	public static function report_pagination($_id = 'p0', $_count_page_results = 0, $_count_all_results = 0){
		$endpoint = min($_count_all_results, wp_slimstat_db::$filters['parsed']['starting'][1] + wp_slimstat_db::$filters['parsed']['limit_results'][1]);
		$pagination_buttons = '';

		if ($endpoint + wp_slimstat_db::$filters['parsed']['limit_results'][1] < $_count_all_results && $_count_page_results > 0){
			$startpoint = $_count_all_results - $_count_all_results%wp_slimstat_db::$filters['parsed']['limit_results'][1];
			if ($startpoint == $_count_all_results) $startpoint -= wp_slimstat_db::$filters['parsed']['limit_results'][1];
			$pagination_buttons .= '<a class="slimstat-header-button slimstat-header-button-ajax last-page" href="'.wp_slimstat_reports::fs_url(array('starting' => 'equals '.$startpoint)).'"></a> ';
		}
		if ($endpoint < $_count_all_results && $_count_page_results > 0){
			$startpoint = wp_slimstat_db::$filters['parsed']['starting'][1] + wp_slimstat_db::$filters['parsed']['limit_results'][1];
			$pagination_buttons .= '<a class="slimstat-header-button slimstat-header-button-ajax next-page" href="'.wp_slimstat_reports::fs_url(array('starting' => 'equals '.$startpoint)).'"></a> ';
		}
		if (wp_slimstat_db::$filters['parsed']['starting'][1] > 0){
			$startpoint = (wp_slimstat_db::$filters['parsed']['starting'][1] > wp_slimstat_db::$filters['parsed']['limit_results'][1])?wp_slimstat_db::$filters['parsed']['starting'][1]-wp_slimstat_db::$filters['parsed']['limit_results'][1]:0;
			$pagination_buttons .= '<a class="slimstat-header-button slimstat-header-button-ajax previous-page" href="'.wp_slimstat_reports::fs_url(array('starting' => 'equals '.$startpoint)).'"></a> ';
		}
		if (wp_slimstat_db::$filters['parsed']['starting'][1] - wp_slimstat_db::$filters['parsed']['limit_results'][1] > 0){
			$pagination_buttons .= '<a class="slimstat-header-button slimstat-header-button-ajax first-page" href="'.wp_slimstat_reports::fs_url(array('starting' => 'equals 0')).'"></a> ';
		}

		$pagination = '<p class="pagination">'.sprintf(__('Results %s - %s of %s', 'wp-slimstat'), number_format(wp_slimstat_db::$filters['parsed']['starting'][1]+1, 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']), number_format($endpoint, 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']), number_format($_count_all_results, 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']));
		if (wp_slimstat::$options['refresh_interval'] > 0 && $_id == 'slim_p7_02'){
			$pagination .= ' &ndash; '.__('Refresh in','wp-slimstat').' <i class="refresh-timer"></i>';
		}
		$pagination .= $pagination_buttons.'</p>';

		return $pagination;
	}
	
	/**
	 * Attempts to convert a permalink into a post title
	 */
	public static function get_resource_title($_resource = ''){
		if (wp_slimstat::$options['convert_resource_urls_to_titles'] == 'yes'){	
			$post_id = url_to_postid(strtok($_resource, '?'));
			if ($post_id > 0){
				return get_the_title($post_id);
			}
		}
		return htmlentities(urldecode($_resource), ENT_QUOTES, 'UTF-8');
	}
	
	public static function chart_title($_title = ''){
		if (wp_slimstat_db::$timeframes['current_day']['hour_selected']){
			return sprintf(__('%s Minute by Minute','wp-slimstat'), $_title);
		}
		elseif (wp_slimstat_db::$timeframes['current_day']['day_selected']){
			return sprintf(__('Hourly %s','wp-slimstat'), $_title);
		}
		elseif (wp_slimstat_db::$timeframes['current_day']['year_selected'] && !wp_slimstat_db::$timeframes['current_day']['month_selected']){
			return sprintf(__('Monthly %s','wp-slimstat'), $_title);
		}
		else{
			return sprintf(__('Daily %s','wp-slimstat'), $_title);
		}
	}
	
	public static function inline_help($_text = '', $_echo = true){
		$wrapped_text = "<i class='slimstat-tooltip-trigger corner'></i><span class='slimstat-tooltip-content'>$_text</span>";
		if ($_echo)
			echo $wrapped_text;
		else
			return $wrapped_text;
	}

	public static function show_results($_type = 'recent', $_id = 'p0', $_column = 'id', $_args = array()){
		// Initialize default values, if not specified
		$_args = array_merge(array('custom_where' => '', 'more_columns' => '', 'join_tables' => '', 'having_clause' => '', 'order_by' => '', 'total_for_percentage' => 0, 'as_column' => '', 'filter_op' => 'equals'), $_args);
		$column = !empty($_args['as_column'])?$_column:wp_slimstat_db::get_table_identifier($_column).$_column;

		// Get ALL the results
		$starting = wp_slimstat_db::$filters['parsed']['starting'][1];
		$limit_results = wp_slimstat_db::$filters['parsed']['limit_results'][1];
		
		wp_slimstat_db::$filters['parsed']['starting'][1] = 0;
		wp_slimstat_db::$filters['parsed']['limit_results'][1] = 99999;

		switch($_type){
			case 'recent':
				$all_results = wp_slimstat_db::get_recent($column, $_args['custom_where'], $_args['join_tables'], $_args['having_clause'], $_args['order_by']);
				break;
			case 'popular':
				$all_results = wp_slimstat_db::get_popular($column, $_args['custom_where'], $_args['more_columns'], $_args['having_clause'], $_args['as_column']);
				break;
			case 'popular_complete':
				$all_results = wp_slimstat_db::get_popular_complete($column, $_args['custom_where'], $_args['join_tables'], $_args['having_clause']);
				break;
			case 'popular_outbound':
				$all_results = wp_slimstat_db::get_popular_outbound();
				break;
			default:
		}

		// Restore the filter
		wp_slimstat_db::$filters['parsed']['starting'][1] = $starting;
		wp_slimstat_db::$filters['parsed']['limit_results'][1] = $limit_results;

		// Slice the array
		$results = array_slice($all_results, wp_slimstat_db::$filters['parsed']['starting'][1], wp_slimstat_db::$filters['parsed']['limit_results'][1]);

		$count_page_results = count($results);
		if ($count_page_results == 0){
			echo '<p class="nodata">'.__('No data to display','wp-slimstat').'</p>';
			return true;
		}

		// Sometimes we use aliases for columns
		if (!empty($_args['as_column'])){
			$_column = trim(str_replace('AS', '', $_args['as_column']));
		}

		echo self::report_pagination($_id, $count_page_results, count($all_results));

		for($i=0;$i<$count_page_results;$i++){
			$row_details = $percentage = '';
			$element_pre_value = '';
			$element_value = $results[$i][$_column];

			// Convert the IP address
			if (!empty($results[$i]['ip'])) $results[$i]['ip'] = long2ip($results[$i]['ip']);

			// Some columns require a special pre-treatment
			switch ($_column){
				case 'browser':
					if (!empty($results[$i]['user_agent']) && wp_slimstat::$options['show_complete_user_agent_tooltip'] == 'yes') $element_pre_value = self::inline_help($results[$i]['user_agent'], false);
					$element_value = $results[$i]['browser'].((isset($results[$i]['version']) && intval($results[$i]['version']) != 0)?' '.$results[$i]['version']:'');
					break;
				case 'category':
					$row_details .= '<br>'.__('Category ID','wp-slimstat').": {$results[$i]['category']}";
					$cat_ids = explode(',', $results[$i]['category']);
					if (!empty($cat_ids)){
						$element_value = '';
						foreach ($cat_ids as $a_cat_id){
							if (empty($a_cat_id)) continue;
							$cat_name = get_cat_name($a_cat_id);
							if (empty($cat_name)) {
								$tag = get_term($a_cat_id, 'post_tag');
								if (!empty($tag->name)) $cat_name = $tag->name;
							}
							$element_value .= ', '.(!empty($cat_name)?$cat_name:$a_cat_id);
						}
						$element_value = substr($element_value, 2);
					}
					break;
				case 'country':
					$row_details .= '<br>'.__('Country Code','wp-slimstat').": {$results[$i]['country']}";
					$element_value = __('c-'.$results[$i]['country'], 'wp-slimstat');
					break;
				case 'ip':
					if (wp_slimstat::$options['convert_ip_addresses'] == 'yes'){
						$element_value = gethostbyaddr($results[$i]['ip']);
					}
					else{
						$element_value = $results[$i]['ip'];
					}
					break;
				case 'language':
					$row_details = '<br>'.__('Language Code','wp-slimstat').": {$results[$i]['language']}";
					$element_value = __('l-'.$results[$i]['language'], 'wp-slimstat');
					break;
				case 'platform':
					$row_details = '<br>'.__('OS Code','wp-slimstat').": {$results[$i]['platform']}";
					$element_value = __($results[$i]['platform'], 'wp-slimstat');
					break;
				case 'resource':
					$post_id = url_to_postid(strtok($results[$i]['resource'], '?'));
					if ($post_id > 0) $row_details = '<br>'.htmlentities($results[$i]['resource'], ENT_QUOTES, 'UTF-8');
					$element_value = self::get_resource_title($results[$i]['resource']);
					break;
				case 'searchterms':
					if ($_type == 'recent'){
						$row_details = '<br>'.__('Referrer','wp-slimstat').": {$results[$i]['domain']}";
						$element_value = self::get_search_terms_info($results[$i]['searchterms'], $results[$i]['domain'], $results[$i]['referer'], true);
					}
					else{
						$element_value = htmlentities($results[$i]['searchterms'], ENT_QUOTES, 'UTF-8');
					}
					break;
				case 'user':
					$element_value = $results[$i]['user'];
					if (wp_slimstat::$options['show_display_name'] == 'yes' && strpos($results[$i]['notes'], '[user:') !== false){
						$element_custom_value = get_user_by('login', $results[$i]['user']);
						if (is_object($element_custom_value)) $element_value = $element_custom_value->display_name;
					}
					break;
				default:
			}
			
			$element_value = "<a class='slimstat-filter-link' href='".self::fs_url(array($_column => $_args['filter_op'].' '.$results[$i][$_column]))."'>$element_value</a>";

			if ($_type == 'recent'){
				$row_details = date_i18n(wp_slimstat_db::$formats['date_time_format'], $results[$i]['dt'], true).$row_details;
			}
			else{
				$percentage = '<span>'.(($_args['total_for_percentage'] > 0)?number_format(sprintf("%01.2f", (100*$results[$i]['count']/$_args['total_for_percentage'])), 2, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']):0).'%</span>';
				$row_details = __('Hits','wp-slimstat').': '.number_format($results[$i]['count'], 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']).$row_details;
			}

			// Some columns require a special post-treatment
			if ($_column == 'resource' && strpos($_args['custom_where'], '404') === false){
				$element_value = '<a target="_blank" class="url" title="'.__('Open this URL in a new window','wp-slimstat').'" href="'.$results[$i]['resource'].'"></a> '.$element_value;
			}
			if ($_column == 'domain'){
				$element_url = htmlentities((strpos($results[$i]['referer'], '://') == false)?"http://{$results[$i]['domain']}{$results[$i]['referer']}":$results[$i]['referer'], ENT_QUOTES, 'UTF-8');
				$element_value = '<a target="_blank" class="url" title="'.__('Open this URL in a new window','wp-slimstat').'" href="'.$element_url.'"></a> '.$element_value;
			}
			if (!empty($results[$i]['ip']))
				$row_details .= '<br><a title="WHOIS: '.$results[$i]['ip'].'" class="whois" href="'.self::$ip_lookup_url.$results[$i]['ip'].'"></a> IP: <a class="slimstat-filter-link" href="'.self::fs_url(array('ip' => 'equals '.$results[$i]['ip'])).'">'.$results[$i]['ip'].'</a>'.(!empty($results[$i]['other_ip'])?' / '.long2ip($results[$i]['other_ip']):'');

			echo "<p>$element_pre_value$element_value$percentage <b class='slimstat-row-details'>$row_details</b></p>";
		}
	}

	public static function show_chart($_id = 'p0', $_chart_data = array(), $_chart_labels = array()){
		$rtl_filler_current = $rtl_filler_previous = 0;
		if ($GLOBALS['wp_locale']->text_direction == 'rtl' && !wp_slimstat_db::$timeframes['current_day']['day_selected']){
			$rtl_filler_current = 31-((date_i18n('Ym') == wp_slimstat_db::$timeframes['current_day']['y'].wp_slimstat_db::$timeframes['current_day']['m'])?wp_slimstat_db::$timeframes['current_day']['d']:cal_days_in_month(CAL_GREGORIAN, wp_slimstat_db::$timeframes['current_day']['m'], wp_slimstat_db::$timeframes['current_day']['y']));
			$rtl_filler_previous = 31-cal_days_in_month(CAL_GREGORIAN, wp_slimstat_db::$timeframes['previous_month']['m'], wp_slimstat_db::$timeframes['previous_month']['y']);
		} ?>
		<div id="chart-placeholder"></div><div id="chart-legend"></div>
		<script type="text/javascript">
			if (typeof SlimStatAdmin == 'undefined') var SlimStatAdmin = {data:[],ticks:[],options:{}};
			SlimStatAdmin.options.rtl_filler_current = <?php echo $rtl_filler_current ?>;
			SlimStatAdmin.options.rtl_filler_previous = <?php echo $rtl_filler_previous ?>;
			SlimStatAdmin.options.interval = <?php echo !empty(wp_slimstat_db::$filters['parsed']['interval'])?wp_slimstat_db::$filters['parsed']['interval'][1]:0 ?>;
			SlimStatAdmin.options.daily_chart = <?php echo ((!wp_slimstat_db::$timeframes['current_day']['year_selected'] || wp_slimstat_db::$timeframes['current_day']['month_selected']) && !wp_slimstat_db::$timeframes['current_day']['day_selected'] && !wp_slimstat_db::$timeframes['current_day']['hour_selected'])?'true':'false' ?>;
			SlimStatAdmin.options.max_yaxis = <?php echo $_chart_data['max_yaxis'] ?>;
			SlimStatAdmin.options.current_month = parseInt('<?php echo wp_slimstat_db::$timeframes['current_day']['m'] ?>');
			SlimStatAdmin.options.current_year = parseInt('<?php echo wp_slimstat_db::$timeframes['current_day']['y'] ?>');
			
			// Data for the chart
			SlimStatAdmin.ticks = [<?php echo $_chart_data['ticks'] ?>];
			SlimStatAdmin.data = [];
			SlimStatAdmin.data.push({<?php echo !empty($_chart_data['markings'])?"data:[{$_chart_data['markings']}],bars:{show:true,radius:1,barWidth:0.2,lineWidth:1,align:'center',fill:true,fillColor:'#bbff44',},lines:{show:false}":''; ?>});
			SlimStatAdmin.data.push({<?php echo !empty($_chart_data['previous']['data2'])?"label:'{$_chart_labels[1]} ".wp_slimstat_db::$timeframes['label_previous']."',data:[{$_chart_data['previous']['data2']}],points:{show:true,symbol:function(ctx, x, y, radius, shadow){ctx.arc(x, y, 2, 0, Math.PI * 2, false)}}":''; ?>});
			SlimStatAdmin.data.push({<?php echo !empty($_chart_data['previous']['data1'])?"label:'{$_chart_labels[0]} ".wp_slimstat_db::$timeframes['label_previous']."',data:[{$_chart_data['previous']['data1']}],points:{show:true,symbol:function(ctx, x, y, radius, shadow){ctx.arc(x, y, 2, 0, Math.PI * 2, false)}}":''; ?>});
			SlimStatAdmin.data.push({label:'<?php echo $_chart_labels[1].' '.wp_slimstat_db::$timeframes['label_current'] ?>',data:[<?php echo $_chart_data['current']['data2'] ?>],points:{show:true,symbol:function(ctx, x, y, radius, shadow){ctx.arc(x, y, 2, 0, Math.PI * 2, false)}}});
			SlimStatAdmin.data.push({label:'<?php echo $_chart_labels[0].' '.wp_slimstat_db::$timeframes['label_current'] ?>',data:[<?php echo $_chart_data['current']['data1'] ?>],points:{show:true,symbol:function(ctx, x, y, radius, shadow){ctx.arc(x, y, 2, 0, Math.PI * 2, false)}}});
		</script>
		<?php 
	}
	
	public static function show_spy_view($_id = 'p0', $_type = 'undefined'){
		$results = !is_int($_type)?wp_slimstat_db::get_recent('t1.id', '(t1.visit_id > 0 AND tb.type <> 1)', 'tb.*', '', 't1.visit_id DESC'):wp_slimstat_db::get_recent_outbound($_type);

		if (count($results) == 0){
			echo '<p class="nodata">'.__('No data to display','wp-slimstat').'</p>';
			return true;
		}

		$visit_id = 0;
		for($i=0;$i<count($results);$i++){
			$row_details = '';
			$results[$i]['ip'] = long2ip($results[$i]['ip']);

			$host_by_ip = $results[$i]['ip'];
			if (wp_slimstat::$options['convert_ip_addresses'] == 'yes'){
				$host_by_ip = gethostbyaddr($results[$i]['ip']);
				$host_by_ip .= ($host_by_ip != $results[$i]['ip'])?" ({$results[$i]['ip']})":'';
			}

			$results[$i]['dt'] = date_i18n(wp_slimstat_db::$formats['date_time_format'], $results[$i]['dt'], true);
			if (!empty($results[$i]['searchterms']) && empty($results[$i]['resource'])){
				$results[$i]['resource'] = __('Search for','wp-slimstat').': '.htmlentities($results[$i]['searchterms'], ENT_QUOTES, 'UTF-8');
			}
			if (!empty($results[$i]['resource']) && $_type == 0){
				$results[$i]['resource'] = '<a target="_blank" class="url" title="'.__('Open this URL in a new window','wp-slimstat').'" href="'.$results[$i]['resource'].'"></a> '.self::get_resource_title($results[$i]['resource']);
			}

			if ($visit_id != $results[$i]['visit_id']){
				$highlight_row = !empty($results[$i]['searchterms'])?' is-search-engine':' is-direct';
				if (empty($results[$i]['user'])){
					$host_by_ip = "<a class='slimstat-filter-link' href='".self::fs_url(array('ip' => 'equals '.$results[$i]['ip']))."'>$host_by_ip</a>";
				}
				else{
					$display_user_name = $results[$i]['user'];
					if (wp_slimstat::$options['show_display_name'] == 'yes' && strpos($results[$i]['notes'], '[user:') !== false){
						$display_real_name = get_user_by('login', $results[$i]['user']);
						if (is_object($display_real_name)) $display_user_name = $display_real_name->display_name;
					}
					$host_by_ip = "<a class='slimstat-filter-link' class='highlight-user' href='".self::fs_url(array('user' => 'equals '.$results[$i]['user']))."'>{$display_user_name}</a>";
					$highlight_row = (strpos( $results[$i]['notes'], '[user]') !== false)?' is-known-user':' is-known-visitor';
				}
				$host_by_ip = "<a class='whois' href='".self::$ip_lookup_url."{$results[$i]['ip']}' target='_blank' title='WHOIS: {$results[$i]['ip']}'></a> $host_by_ip";
				$results[$i]['country'] = "<a class='slimstat-filter-link' href='".self::fs_url(array('country' => 'equals '.$results[$i]['country']))."'><img class='slimstat-tooltip-trigger' src='".wp_slimstat_reports::$plugin_url."/images/flags/{$results[$i]['country']}.png' width='16' height='16'/><span class='slimstat-tooltip-content'>".__('c-'.$results[$i]['country'],'wp-slimstat').'</span></a>';
				$results[$i]['other_ip'] = !empty($results[$i]['other_ip'])?" <a class='slimstat-filter-link' href='".self::fs_url(array('other_ip' => 'equals '.$results[$i]['other_ip']))."'>".long2ip($results[$i]['other_ip']).'</a>&nbsp;&nbsp;':'';
		
				echo "<p class='header$highlight_row'>{$results[$i]['country']} $host_by_ip <span class='date-and-other'><em>{$results[$i]['other_ip']} {$results[$i]['dt']}</em></span></p>";
				$visit_id = $results[$i]['visit_id'];
			}

			if (!empty($results[$i]['domain'])){
				if (!is_int($_type)){
					$element_url = htmlentities((strpos($results[$i]['referer'], '://') == false)?"http://{$results[$i]['domain']}{$results[$i]['referer']}":$results[$i]['referer'], ENT_QUOTES, 'UTF-8');
					$row_details = __('Source','wp-slimstat').": <a class='slimstat-filter-link' href='".self::fs_url(array('domain' => 'equals '.$results[$i]['domain']))."'>{$results[$i]['domain']}</a>";
					if (!empty($results[$i]['searchterms'])){
						$row_details .= "<br>".__('Keywords','wp-slimstat').": ";
						$row_details .= "<a class='slimstat-filter-link' href='".self::fs_url(array('searchterms' => 'equals '.$results[$i]['searchterms']))."'>".htmlentities(self::get_search_terms_info($results[$i]['searchterms'], $results[$i]['domain'], $results[$i]['referer'], true), ENT_QUOTES, 'UTF-8')."</a>";
					}
				}
				else{
					$permalink = parse_url($results[$i]['referer']);
					$results[$i]['notes'] = str_replace('|ET:click', '', $results[$i]['notes']);
					$element_url = htmlentities((strpos($results[$i]['referer'], '://') === false)?self::$home_url.$results[$i]['referer']:$results[$i]['referer'], ENT_QUOTES, 'UTF-8');
					$row_details = __('Source','wp-slimstat').": <a target=\"_blank\" class=\"url\" title=\"".__('Open this URL in a new window','wp-slimstat')."\" href=\"$element_url\"></a><a class=\"slimstat-filter-link\" title=\"".htmlentities(sprintf(__('Filter results where resource equals %s','wp-slimstat'), $permalink['path']), ENT_QUOTES, 'UTF-8')."\" href=\"".self::fs_url(array('resource' => 'equals '.$permalink['path']))."\">{$permalink['path']}</a>";
					$row_details .= !empty($results[$i]['notes'])?'<br><strong>Link Details</strong>: '.htmlentities($results[$i]['notes'], ENT_QUOTES, 'UTF-8'):'';
					$row_details .= ($_type == -1)?' <strong>Type</strong>: '.$results[$i]['type']:'';
				}
			}
			echo "<p>{$results[$i]['resource']} <b class='slimstat-row-details'>$row_details</b></p>";
		}
	}
	
	public static function show_about_wpslimstat($_id = 'p0'){ ?>
		<p><?php _e('Total Pageviews', 'wp-slimstat') ?> <span><?php echo number_format(wp_slimstat_db::count_records('1=1', '*', false, '', false), 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
		<p><?php _e('DB Size', 'wp-slimstat') ?> <span><?php echo wp_slimstat_db::get_data_size() ?></span></p>
		<p><?php _e('Tracking Active', 'wp-slimstat') ?> <span><?php _e(ucfirst(wp_slimstat::$options['is_tracking']), 'wp-slimstat') ?></span></p>
		<p><?php _e('Javascript Mode', 'wp-slimstat') ?> <span><?php _e(ucfirst(wp_slimstat::$options['javascript_mode']), 'wp-slimstat') ?></span></p>
		<p><?php _e('Tracking Browser Caps', 'wp-slimstat') ?> <span><?php _e(ucfirst(wp_slimstat::$options['enable_javascript']), 'wp-slimstat') ?></span></p>
		<p><?php _e('Auto purge', 'wp-slimstat') ?> <span><?php echo (wp_slimstat::$options['auto_purge'] > 0)?wp_slimstat::$options['auto_purge'].' '.__('days','wp-slimstat'):__('No','wp-slimstat') ?></span></p>
		<p><?php _e('Oldest pageview', 'wp-slimstat') ?> <span><?php $dt = wp_slimstat_db::get_oldest_visit('1=1', false); echo ($dt == null)?__('No visits','wp-slimstat'):date_i18n(get_option('date_format'), $dt) ?></span></p>
		<p>Geo IP <span><?php echo date_i18n(get_option('date_format'), @filemtime(WP_PLUGIN_DIR.'/wp-slimstat/databases/maxmind.dat')) ?></span></p><?php
	}

	public static function show_overview_summary($_id = 'p0', $_current_pageviews = 0, $_chart_data = array()){
		$reset_timezone = date_default_timezone_get();
		date_default_timezone_set('UTC');

		$temp_filters_date_sql_where = wp_slimstat_db::$filters['date_sql_where'];
		wp_slimstat_db::$filters['date_sql_where'] = ''; // override date filters
		$today_pageviews = wp_slimstat_db::count_records('t1.dt BETWEEN '.wp_slimstat_db::$timeframes['current_day']['utime'].' AND '.(wp_slimstat_db::$timeframes['current_day']['utime']+86399));
		$yesterday_pageviews = wp_slimstat_db::count_records('t1.dt BETWEEN '.(wp_slimstat_db::$timeframes['previous_day']['utime']).' AND '.(wp_slimstat_db::$timeframes['previous_day']['utime']+86399));
		wp_slimstat_db::$filters['date_sql_where'] = $temp_filters_date_sql_where; ?>
			<p><?php self::inline_help(__('A request to load a single HTML file. WP SlimStat logs a "pageview" each time the tracking code is executed.','wp-slimstat'));
				_e('Pageviews', 'wp-slimstat'); echo ' '.wp_slimstat_db::$timeframes['label_current']; ?> <span><?php echo number_format($_current_pageviews, 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
			<p><?php self::inline_help(__('This counter is based on any user activity in the last 5 minutes.','wp-slimstat'));
				_e('Last 5 minutes', 'wp-slimstat') ?> <span><?php echo number_format(wp_slimstat_db::count_records('t1.dt > '.(date_i18n('U')-300), '*', true, '', false), 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
			<p><?php self::inline_help(__('This counter is based on any user activity in the last 30 minutes.','wp-slimstat'));
				_e('Last 30 minutes', 'wp-slimstat') ?> <span><?php echo number_format(wp_slimstat_db::count_records('t1.dt > '.(date_i18n('U')-1800), '*', true, '', false), 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
			<p><?php _e('Today', 'wp-slimstat'); ?> <span><?php echo number_format(wp_slimstat_db::count_records('t1.dt > '.(date_i18n('U', mktime(0, 0, 0, date_i18n('m'), date_i18n('d'), date_i18n('Y')))), '*', true, '', false), 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
			<p><?php _e('Yesterday', 'wp-slimstat'); ?> <span><?php echo number_format(wp_slimstat_db::count_records('t1.dt BETWEEN '.(date_i18n('U', mktime(0, 0, 0, date_i18n('m'), date_i18n('d')-1, date_i18n('Y')))).' AND '.(date_i18n('U', mktime(23, 59, 59, date_i18n('m'), date_i18n('d')-1, date_i18n('Y')))), '*', true, '', false), 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
			<p><?php self::inline_help(__('How many pages have been visited on average during the current period.','wp-slimstat'));
				_e('Avg Pageviews', 'wp-slimstat') ?> <span><?php echo number_format(($_chart_data['current']['non_zero_count'] > 0)?intval($_current_pageviews/$_chart_data['current']['non_zero_count']):0, 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
			<p><?php self::inline_help(__('Visitors who landed on your site after searching for a keyword on Google, Yahoo, etc.','wp-slimstat'));
				_e('From Search Results', 'wp-slimstat') ?> <span><?php echo number_format(wp_slimstat_db::count_records('t1.searchterms <> ""'), 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
			<p><?php self::inline_help(__('Used to differentiate between multiple requests to download a file from one internet address (IP) and requests originating from many distinct addresses','wp-slimstat'));
				_e('Unique IPs', 'wp-slimstat'); ?> <span><?php echo number_format(wp_slimstat_db::count_records('1=1', 't1.ip'), 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p><?php
		date_default_timezone_set($reset_timezone);
	}
	
	public static function show_visitors_summary($_id = 'p0', $_total_human_hits = 0, $_total_human_visits = 0){
		$new_visitors = wp_slimstat_db::count_records_having('visit_id > 0', 'ip', 'COUNT(visit_id) = 1');
		$new_visitors_rate = ($_total_human_hits > 0)?sprintf("%01.2f", (100*$new_visitors/$_total_human_hits)):0;
		if (intval($new_visitors_rate) > 99) $new_visitors_rate = '100';
		$metrics_per_visit = wp_slimstat_db::get_max_and_average_pages_per_visit(); ?>
			<p><?php self::inline_help(__('A visit is a session of at most 30 minutes. Returning visitors are counted multiple times if they perform multiple visits.','wp-slimstat')) ?>
				<?php _e('Human visits', 'wp-slimstat') ?> <span><?php echo number_format($_total_human_visits, 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
			<p><?php self::inline_help(__('It includes only traffic generated by human visitors.','wp-slimstat')) ?>
				<?php _e('Unique IPs', 'wp-slimstat') ?> <span><?php echo number_format(wp_slimstat_db::count_records('t1.visit_id > 0 AND tb.type <> 1', 't1.ip'), 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
			<p><?php self::inline_help(__('Percentage of single-page visits, i.e. visits in which the person left your site from the entrance page.','wp-slimstat')) ?>
				<?php _e('Bounce rate', 'wp-slimstat') ?> <span><?php echo number_format($new_visitors_rate, 2, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?>%</span></p>
			<p><?php self::inline_help(__('Visitors who had previously left a comment on your blog.','wp-slimstat')) ?>
				<?php _e('Known visitors', 'wp-slimstat') ?> <span><?php echo wp_slimstat_db::count_records('t1.user <> ""', 't1.user') ?></span></p>
			<p><?php self::inline_help(__('Human users who visited your site only once.','wp-slimstat')) ?>
				<?php _e('New visitors', 'wp-slimstat') ?> <span><?php echo number_format($new_visitors, 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
			<p><?php _e('Bots', 'wp-slimstat') ?> <span><?php echo number_format(wp_slimstat_db::count_records('tb.type = 1'), 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
			<p><?php _e('Pages per visit', 'wp-slimstat') ?> <span><?php echo number_format($metrics_per_visit['avg'], 2, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
			<p><?php _e('Longest visit', 'wp-slimstat') ?> <span><?php echo number_format($metrics_per_visit['max'], 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']).' '.__('hits','wp-slimstat') ?></span></p><?php
	}

	public static function show_plugins($_id = 'p0', $_total_human_hits = 0){
		$wp_slim_plugins = array('flash', 'silverlight', 'acrobat', 'java', 'mediaplayer', 'director', 'real', 'quicktime');
		foreach($wp_slim_plugins as $i => $a_plugin){
			$count_results = wp_slimstat_db::count_records("t1.plugins LIKE '%{$a_plugin}%'");
			echo "<p title='".__('Hits','wp-slimstat').": $count_results'>".ucfirst($a_plugin).' <span>';
			echo ($_total_human_hits > 0)?number_format(sprintf("%01.2f", (100*$count_results/$_total_human_hits)), 2, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']):0;
			echo '%</span></p>';
		}
	}
	
	public static function show_visit_duration($_id = 'p0', $_total_human_visits = 0){
		$count = wp_slimstat_db::count_records_having('t1.visit_id > 0 AND tb.type <> 1', 'visit_id', 'max(t1.dt) - min(t1.dt) >= 0 AND max(t1.dt) - min(t1.dt) <= 30');
		$percentage = ($_total_human_visits > 0)?sprintf("%01.2f", (100*$count/$_total_human_visits)):0;
		$extra_info =  "title='".__('Hits','wp-slimstat').": {$count}'";
		$average_time = 30 * $count;
		echo "<p $extra_info>".__('0 - 30 seconds','wp-slimstat')." <span>$percentage%</span></p>";

		$count = wp_slimstat_db::count_records_having('t1.visit_id > 0 AND tb.type <> 1', 'visit_id', 'max(t1.dt) - min(t1.dt) > 30 AND max(t1.dt) - min(t1.dt) <= 60');
		$percentage = ($_total_human_visits > 0)?sprintf("%01.2f", (100*$count/$_total_human_visits)):0;
		$extra_info =  "title='".__('Hits','wp-slimstat').": {$count}'";
		$average_time += 60 * $count;
		echo "<p $extra_info>".__('31 - 60 seconds','wp-slimstat')." <span>$percentage%</span></p>";

		$count = wp_slimstat_db::count_records_having('t1.visit_id > 0 AND tb.type <> 1', 'visit_id', 'max(t1.dt) - min(t1.dt) > 60 AND max(t1.dt) - min(t1.dt) <= 180');
		$percentage = ($_total_human_visits > 0)?sprintf("%01.2f", (100*$count/$_total_human_visits)):0;
		$extra_info =  "title='".__('Hits','wp-slimstat').": {$count}'";
		$average_time += 180 * $count;
		echo "<p $extra_info>".__('1 - 3 minutes','wp-slimstat')." <span>$percentage%</span></p>";

		$count = wp_slimstat_db::count_records_having('t1.visit_id > 0 AND tb.type <> 1', 'visit_id', 'max(t1.dt) - min(t1.dt) > 180 AND max(t1.dt) - min(t1.dt) <= 300');
		$percentage = ($_total_human_visits > 0)?sprintf("%01.2f", (100*$count/$_total_human_visits)):0;
		$extra_info =  "title='".__('Hits','wp-slimstat').": {$count}'";
		$average_time += 300 * $count;
		echo "<p $extra_info>".__('3 - 5 minutes','wp-slimstat')." <span>$percentage%</span></p>";

		$count = wp_slimstat_db::count_records_having('t1.visit_id > 0 AND tb.type <> 1', 'visit_id', 'max(t1.dt) - min(t1.dt) > 300 AND max(t1.dt) - min(t1.dt) <= 420');
		$percentage = ($_total_human_visits > 0)?sprintf("%01.2f", (100*$count/$_total_human_visits)):0;
		$extra_info =  "title='".__('Hits','wp-slimstat').": {$count}'";
		$average_time += 420 * $count;
		echo "<p $extra_info>".__('5 - 7 minutes','wp-slimstat')." <span>$percentage%</span></p>";

		$count = wp_slimstat_db::count_records_having('t1.visit_id > 0 AND tb.type <> 1', 'visit_id', 'max(t1.dt) - min(t1.dt) > 420 AND max(t1.dt) - min(t1.dt) <= 600');
		$percentage = ($_total_human_visits > 0)?sprintf("%01.2f", (100*$count/$_total_human_visits)):0;
		$extra_info =  "title='".__('Hits','wp-slimstat').": {$count}'";
		$average_time += 600* $count;
		echo "<p $extra_info>".__('7 - 10 minutes','wp-slimstat')." <span>$percentage%</span></p>";

		$count = wp_slimstat_db::count_records_having('t1.visit_id > 0 AND tb.type <> 1', 'visit_id', 'max(t1.dt) - min(t1.dt) > 600');
		$percentage = ($_total_human_visits > 0)?sprintf("%01.2f", (100*$count/$_total_human_visits)):0;
		$extra_info =  "title='".__('Hits','wp-slimstat').": {$count}'";
		$average_time += 900 * $count;
		echo "<p $extra_info>".__('More than 10 minutes','wp-slimstat')." <span>$percentage%</span></p>";

		$average_time /= $_total_human_visits;
		$average_time = date('m:s', intval($average_time));
		echo '<p>'.__('Average time on site','wp-slimstat')." <span>$average_time </span></p>";
	}
	
	public static function show_traffic_sources_summary($_id = 'p0', $_current_pageviews = 0){
		$total_human_hits = wp_slimstat_db::count_records('t1.visit_id > 0 AND tb.type <> 1');
		$new_visitors = wp_slimstat_db::count_records_having('visit_id > 0', 'ip', 'COUNT(visit_id) = 1');
		$new_visitors_rate = ($total_human_hits > 0)?sprintf("%01.2f", (100*$new_visitors/$total_human_hits)):0;
		if (intval($new_visitors_rate) > 99) $new_visitors_rate = '100'; ?>		
		<p><?php self::inline_help(__('A request to load a single HTML file. WP SlimStat logs a "pageview" each time the tracking code is executed.','wp-slimstat')) ?>
			<?php _e('Pageviews', 'wp-slimstat') ?> <span><?php echo number_format($_current_pageviews, 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
		<p><?php self::inline_help(__('A referrer (or referring site) is the site that a visitor previously visited before following a link to your site.','wp-slimstat')) ?>
			<?php _e('Unique Referrers', 'wp-slimstat') ?> <span><?php echo number_format(wp_slimstat_db::count_records("t1.domain <> '{$_SERVER['SERVER_NAME']}' AND t1.domain <> ''", 't1.domain'), 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
		<p><?php self::inline_help(__("Visitors who visited the site by typing the URL directly into their browser. <em>Direct</em> can also refer to the visitors who clicked on the links from their bookmarks/favorites, untagged links within emails, or links from documents that don't include tracking variables.",'wp-slimstat')) ?>
			<?php _e('Direct Pageviews', 'wp-slimstat') ?> <span><?php echo number_format(wp_slimstat_db::count_records('t1.domain = ""', 't1.id'), 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
		<p><?php self::inline_help(__("Visitors who came to your site via searches on Google or some other search engine.",'wp-slimstat')) ?>
			<?php _e('From a search result', 'wp-slimstat') ?> <span><?php echo number_format(wp_slimstat_db::count_records("t1.searchterms <> '' AND t1.domain <> '{$_SERVER['SERVER_NAME']}' AND t1.domain <> ''", 't1.id'), 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
		<p><?php self::inline_help(__("The first page that a user views during a session. This is also known as the <em>entrance page</em>. For example, if they search for 'Brooklyn Office Space,' and they land on your home page, it gets counted (for that visit) as a landing page.",'wp-slimstat')) ?>
			<?php _e('Unique Landing Pages', 'wp-slimstat') ?> <span><?php echo number_format(wp_slimstat_db::count_records('t1.domain <> ""', 't1.resource'), 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
		<p><?php self::inline_help(__("Number of single-page visits to your site over the selected period.",'wp-slimstat')) ?>
			<?php _e('Bounce Pages', 'wp-slimstat') ?> <span><?php echo number_format(wp_slimstat_db::count_bouncing_pages(), 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
		<p><?php self::inline_help(__('Percentage of single-page visits, i.e. visits in which the person left your site from the entrance page.','wp-slimstat')) ?>
			<?php _e('New Visitors Rate', 'wp-slimstat') ?> <span><?php echo number_format($new_visitors_rate, 2, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?>%</span></p>
		<p><?php self::inline_help(__("Visitors who visited the site in the last 5 minutes coming from a search engine.",'wp-slimstat')) ?>
			<?php _e('Currently from search engines', 'wp-slimstat') ?> <span><?php echo number_format(wp_slimstat_db::count_records("t1.searchterms <> '' AND t1.domain <> '{$_SERVER['SERVER_NAME']}' AND t1.domain <> '' AND t1.dt > UNIX_TIMESTAMP()-300", 't1.id', false), 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p><?php
	}
	
	public static function show_rankings($_id = 'p0'){
		$options = array('timeout' => 1, 'headers' => array('Accept' => 'application/json'));
		$site_url_array = parse_url(home_url());
		
		// Check if we have a valied transient
		if (false === ($rankings = get_transient( 'slimstat_ranking_values' ))){
			$rankings = array('google_index' => 0, 'google_backlinks' => 0, 'facebook_likes' => 0, 'facebook_shares' => 0, 'facebook_clicks' => 0, 'alexa_world_rank' => 0, 'alexa_country_rank' => 0, 'alexa_popularity' => 0);

			// Google Index
			$response = @wp_remote_get('https://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=site:'.$site_url_array['host'], $options);
			if (!is_wp_error($response) && isset($response['response']['code']) && ($response['response']['code'] == 200) && !empty($response['body'])){
				$response = @json_decode($response['body']);
				if (is_object($response) && !empty($response->responseData->cursor->resultCount)){
					$rankings['google_index'] = (int)$response->responseData->cursor->resultCount;
				}
			}
			
			// Google Backlinks
			$response = @wp_remote_get('https://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=link:'.$site_url_array['host'], $options);
			if (!is_wp_error($response) && isset($response['response']['code']) && ($response['response']['code'] == 200) && !empty($response['body'])){
				$response = @json_decode($response['body']);
				if (is_object($response) && !empty($response->responseData->cursor->resultCount)){
					$rankings['google_backlinks'] = (int)$response->responseData->cursor->resultCount;
				}
			}
			
			// Facebook
			$options['headers']['Accept'] = 'text/xml';
			$response = @wp_remote_get("https://api.facebook.com/method/fql.query?query=select%20%20like_count,%20total_count,%20share_count,%20click_count%20from%20link_stat%20where%20url='".$site_url_array['host']."'", $options);
			if (!is_wp_error($response) && isset($response['response']['code']) && ($response['response']['code'] == 200) && !empty($response['body'])){
				$response = new SimpleXMLElement($response['body']);
				if (is_object($response) && is_object($response->link_stat) && !empty($response->link_stat->like_count)){
					$rankings['facebook_likes'] = (int)$response->link_stat->like_count;
					$rankings['facebook_shares'] = (int)$response->link_stat->share_count;
					$rankings['facebook_clicks'] = (int)$response->link_stat->click_count;
				}
			}

			// Alexa
			$response = @wp_remote_get("http://data.alexa.com/data?cli=10&dat=snbamz&url=".$site_url_array['host'], $options);
			if (!is_wp_error($response) && isset($response['response']['code']) && ($response['response']['code'] == 200) && !empty($response['body'])){
				$response = new SimpleXMLElement($response['body']);
				if (is_object($response) && is_object($response->SD[1]->POPULARITY)){
					if ($response->SD[1]->POPULARITY && $response->SD[1]->POPULARITY->attributes()){
						$attributes = $response->SD[1]->POPULARITY->attributes();
						$rankings['alexa_popularity'] = (int)$attributes['TEXT'];
					}

					if ($response->SD[1]->REACH && $response->SD[1]->REACH->attributes()){
						$attributes = $response->SD[1]->REACH->attributes();
						$rankings['alexa_world_rank'] = (int)$attributes['RANK'];
					}

					if ($response->SD[1]->COUNTRY && $response->SD[1]->COUNTRY->attributes()){
						$attributes = $response->SD[1]->COUNTRY->attributes();
						$rankings['alexa_country_rank'] = (int)$attributes['RANK'];
					}
				}
			}

			// Store rankings as transients for 12 hours
			set_transient('slimstat_ranking_values', $rankings, 43200);
		}
		?>
		
		<p><?php self::inline_help(__("Number of pages in your site included in Google's index.",'wp-slimstat')) ?>
			<?php _e('Google Index', 'wp-slimstat') ?> <span><?php echo number_format($rankings['google_index'], 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
		<p><?php self::inline_help(__("Number of pages, according to Google, that link back to your site.",'wp-slimstat')) ?>
			<?php _e('Google Backlinks', 'wp-slimstat') ?> <span><?php echo number_format($rankings['google_backlinks'], 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
		<p><?php self::inline_help(__("How many times the Facebook Like button has been approximately clicked on your site.",'wp-slimstat')) ?>
			<?php _e('Facebook Likes', 'wp-slimstat') ?> <span><?php echo number_format($rankings['facebook_likes'], 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
		<p><?php self::inline_help(__("How many times your site has been shared by someone on the social network.",'wp-slimstat')) ?>
			<?php _e('Facebook Shares', 'wp-slimstat') ?> <span><?php echo number_format($rankings['facebook_shares'], 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
		<p><?php self::inline_help(__("How many times links to your website have been clicked on Facebook.",'wp-slimstat')) ?>
			<?php _e('Facebook Clicks', 'wp-slimstat') ?> <span><?php echo number_format($rankings['facebook_clicks'], 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
		<p><?php self::inline_help(__("Alexa is a subsidiary company of Amazon.com which provides commercial web traffic data.",'wp-slimstat')) ?>
			<?php _e('Alexa World Rank', 'wp-slimstat') ?> <span><?php echo number_format($rankings['alexa_world_rank'], 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
		<p><?php _e('Alexa Country Rank', 'wp-slimstat') ?> <span><?php echo number_format($rankings['alexa_country_rank'], 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
		<p><?php _e('Alexa Popularity', 'wp-slimstat') ?> <span><?php echo number_format($rankings['alexa_popularity'], 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p><?php
	}

	public static function show_hacker_ninja($_id = 'p0'){
		$hn_urls = array('hn01' => 'http://www.hackerninja.com/scanner/crawler/launcher_bing_bot.php', 'hn02' => 'http://www.hackerninja.com/scanner/crawler/launcher_clamav_scanner.php', 'hn03' => 'http://www.hackerninja.com/scanner/crawler/launcher_google_bot.php', 'hn04' => 'http://www.hackerninja.com/scanner/crawler/launcher_hostile_strings.php', 'hn05' => 'http://www.hackerninja.com/scanner/crawler/launcher_vanilla_bot.php', 'hn06' => 'http://www.hackerninja.com/scanner/crawler/scan_google_malware_list.php', 'hn07' => 'http://www.hackerninja.com/scanner/crawler/scan_external_links.php', 'hn08' => 'http://www.hackerninja.com/scanner/crawler/scan_external_links.php');
		$parsed_url = parse_url(home_url());
		
		if (!isset($_POST['run_scan'])){
			echo '<p class="nodata"><a class="button-hacker-ninja button-secondary" href="#">Start Free Scan</a><br/><br/><a target="_blank" href="http://hackerninja.com/">More Info</a></p>';
		}
		else{
			switch($_POST['run_scan']){
				case '00': ?>
					<p><?php _e('Bing Test', 'wp-slimstat') ?> <span id="hn01" class="blink">Loading</span></p>
					<p><?php _e('AntiVirus Scan', 'wp-slimstat') ?> <span id="hn02" class="blink">Loading</span></p>
					<p><?php _e('Google Bot Test', 'wp-slimstat') ?> <span id="hn03" class="blink">Loading</span></p>
					<p><?php _e('Scan for Hostile Strings', 'wp-slimstat') ?> <span id="hn04" class="blink">Loading</span></p>
					<p><?php _e('Generic Web Bot test', 'wp-slimstat') ?> <span id="hn05" class="blink">Loading</span></p>
					<p><?php _e('Google Safe Browsing List', 'wp-slimstat') ?> <span id="hn06" class="blink">Loading</span></p>
					<p><?php _e('Hostile External Links', 'wp-slimstat') ?> <span id="hn07" class="blink">Loading</span></p>
					<p><?php _e('Other Treats', 'wp-slimstat') ?> <span id="hn08" class="blink">Loading</span></p><?php
					break;
				case 'hn01':
				case 'hn02':
				case 'hn03':
				case 'hn04':
				case 'hn05':
				case 'hn06':
				case 'hn07':
				case 'hn08':
					$options = array('timeout' => 30);
					$response = @wp_remote_get($hn_urls[$_POST['run_scan']], $options);
					if (!is_wp_error($response) && isset($response['response']['code']) && ($response['response']['code'] == 200) && !empty($response['body'])){
						echo '<a target="_blank" href="http://hackerninja.com/">'.((stripos($response['body'],'passed') !== false)?__('Passed','wp-slimstat'):__('At Risk','wp-slimstat')).'</a>';
					}
					else{
						echo __('Timed Out','wp-slimstat');
					}
					break;
				default:
					echo '<p class="nodata"><a class="button-hacker-ninja button-secondary" href="#">Start Free Scan</a><br/><br/><a target="_blank" href="http://hackerninja.com/">More Info</a></p>';
					break;
			}
		}
	}
	
	public static function show_your_blog($_id = 'p0'){		
		if (false === ($your_content = get_transient( 'slimstat_your_content' ))){
			$your_content = array();
			$your_content['content_items'] = $GLOBALS['wpdb']->get_var("SELECT COUNT(*) FROM {$GLOBALS['wpdb']->posts} WHERE post_type != 'revision' AND post_status != 'auto-draft'");
			$your_content['posts'] = $GLOBALS['wpdb']->get_var("SELECT COUNT(*) FROM {$GLOBALS['wpdb']->posts} WHERE post_type = 'post'");
			$your_content['comments'] = $GLOBALS['wpdb']->get_var("SELECT COUNT(*) FROM {$GLOBALS['wpdb']->comments}");
			$your_content['pingbacks'] = $GLOBALS['wpdb']->get_var("SELECT COUNT(*) FROM {$GLOBALS['wpdb']->comments} WHERE comment_type = 'pingback'");
			$your_content['trackbacks'] = $GLOBALS['wpdb']->get_var("SELECT COUNT(*) FROM {$GLOBALS['wpdb']->comments} WHERE comment_type = 'trackback'");
			$your_content['longest_post_id'] = $GLOBALS['wpdb']->get_var("SELECT ID FROM {$GLOBALS['wpdb']->posts} WHERE post_type = 'post' ORDER BY LENGTH(post_content) DESC LIMIT 0,1");
			$your_content['longest_comment_id'] = $GLOBALS['wpdb']->get_var("SELECT comment_ID FROM {$GLOBALS['wpdb']->comments}");
			$your_content['avg_comments_per_post'] = !empty($your_content['posts'])?$your_content['comments']/$your_content['posts']:0;

			$days_in_interval = floor((wp_slimstat_db::$timeframes['current_utime_end']-wp_slimstat_db::$timeframes['current_utime_start'])/86400);
			$your_content['avg_posts_per_day'] = $your_content['posts']/$days_in_interval;

			// Store values as transients for 30 minutes
			set_transient('slimstat_your_content', $your_content, 1800);
		}
		
		?>
		
		<p><?php self::inline_help(__("This value includes not only posts, but also custom post types, regardless of their status",'wp-slimstat')) ?>
			<?php _e('Content Items', 'wp-slimstat') ?> <span><?php echo number_format($your_content['content_items'], 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
		<p><?php _e('Total Comments', 'wp-slimstat') ?> <span><?php echo number_format($your_content['comments'], 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
		<p><?php _e('Pingbacks', 'wp-slimstat') ?> <span><?php echo number_format($your_content['pingbacks'], 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
		<p><?php _e('Trackbacks', 'wp-slimstat') ?> <span><?php echo number_format($your_content['trackbacks'], 0, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
		<p><?php _e('Longest Post (ID)', 'wp-slimstat') ?> <span><?php echo '<a href="post.php?action=edit&post='.$your_content['longest_post_id'].'">'.$your_content['longest_post_id'].'</a>' ?></span></p>
		<p><?php _e('Longest Comment (ID)', 'wp-slimstat') ?> <span><?php echo '<a href="comment.php?action=editcomment&c='.$your_content['longest_comment_id'].'">'.$your_content['longest_comment_id'].'</a>' ?></span></p>
		<p><?php _e('Avg Comments Per Post', 'wp-slimstat') ?> <span><?php echo number_format($your_content['avg_comments_per_post'], 2, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p>
		<p><?php _e('Avg Posts Per Day', 'wp-slimstat') ?> <span><?php echo number_format($your_content['avg_posts_per_day'], 2, wp_slimstat_db::$formats['decimal'], wp_slimstat_db::$formats['thousand']) ?></span></p><?php
	}

	public static function show_report_wrapper($_report_id = 'p0'){
		$is_ajax = false;
		if (!empty($_POST['report_id'])){
			// Let's make sure the request is coming from the right place
			check_ajax_referer('meta-box-order', 'security');
			$_report_id = $_POST['report_id'];
			self::$current_tab_url = !empty($_POST['current_tab'])?wp_slimstat_admin::$view_url.intval($_POST['current_tab']):1;
			$is_ajax = true;
		}

		if (!$is_ajax && (in_array($_report_id, self::$hidden_reports) || wp_slimstat::$options['async_load'] == 'yes')) return; 
		
		// Some boxes need extra information
		if (in_array($_report_id, array('slim_p1_03', 'slim_p1_08', 'slim_p1_13', 'slim_p1_17', 'slim_p2_03', 'slim_p2_04', 'slim_p2_05', 'slim_p2_06', 'slim_p2_18', 'slim_p2_19', 'slim_p2_10', 'slim_p3_02', 'slim_p3_04'))){
			$current_pageviews = wp_slimstat_db::count_records();
		}

		switch($_report_id){
			case 'slim_p1_01':
			case 'slim_p1_03':
				$chart_data = wp_slimstat_db::extract_data_for_chart('COUNT(t1.ip)', 'COUNT(DISTINCT(t1.ip))');
				$chart_labels = array(__('Pageviews','wp-slimstat'), __('Unique IPs','wp-slimstat'));
				break;
			case 'slim_p2_01':
				$chart_data = wp_slimstat_db::extract_data_for_chart('COUNT(DISTINCT t1.visit_id)', 'COUNT(DISTINCT t1.ip)', 'AND (tb.type = 0 OR tb.type = 2)');
				$chart_labels = array(__('Visits','wp-slimstat'), __('Unique IPs','wp-slimstat'));
				break;
			case 'slim_p3_01':
				$chart_data = wp_slimstat_db::extract_data_for_chart('COUNT(DISTINCT(`domain`))', 'COUNT(DISTINCT(ip))', "AND domain <> '' AND domain <> '{$_SERVER['SERVER_NAME']}'");
				$chart_labels = array(__('Domains','wp-slimstat'), __('Unique IPs','wp-slimstat'));
				break;
			case 'slim_p4_01':
				$sql_from_where = " FROM (SELECT t1.visit_id, count(t1.ip) count, MAX(t1.dt) dt FROM [from_tables] WHERE [where_clause] GROUP BY t1.visit_id) AS ts1";
				$chart_data = wp_slimstat_db::extract_data_for_chart('ROUND(AVG(ts1.count),2)', 'MAX(ts1.count)', 'AND t1.visit_id > 0', $sql_from_where);
				$chart_labels = array(__('Avg Pageviews','wp-slimstat'), __('Longest visit','wp-slimstat'));
				break;
			default:
		}
		
		switch($_report_id){
			case 'slim_p1_01':
			case 'slim_p2_01':
			case 'slim_p3_01':
			case 'slim_p4_01':
				self::show_chart($_report_id, $chart_data, $chart_labels);
				break;
			case 'slim_p1_02':
				self::show_about_wpslimstat($_report_id);
				break;
			case 'slim_p1_03':
				self::show_overview_summary($_report_id, $current_pageviews, $chart_data);
				break;
			case 'slim_p1_04':
				$last_five_minutes = date_i18n('U')-300;
				self::show_results('recent', $_report_id, 'user', array('custom_where' => 't1.dt > '.$last_five_minutes));
				break;
			case 'slim_p1_05':
			case 'slim_p3_08':
				self::show_spy_view($_report_id);
				break;
			case 'slim_p1_06':
			case 'slim_p3_09':
				self::show_results('recent', $_report_id, 'searchterms');
				break;
			case 'slim_p1_08':
				self::show_results('popular', $_report_id, 'SUBSTRING_INDEX(t1.resource, "?", 1)', array('total_for_percentage' => $current_pageviews, 'as_column' => 'resource', 'filter_op' => 'contains'));
				break;
			case 'slim_p1_10':
			case 'slim_p3_05':
				$self_domain = parse_url(site_url());
				$self_domain = $self_domain['host'];
				self::show_results('popular_complete', $_report_id, 'domain', array('total_for_percentage' => wp_slimstat_db::count_records('t1.referer <> ""'), 'custom_where' => 't1.domain <> "'.$self_domain.'" AND t1.domain <> ""'));
				break;
			case 'slim_p1_11':
				self::show_results('popular_complete', $_report_id, 'user', array('total_for_percentage' => wp_slimstat_db::count_records('t1.user <> ""')));
				break;
			case 'slim_p1_12':
			case 'slim_p3_03':
			case 'slim_p4_14':
				self::show_results('popular', $_report_id, 'searchterms', array('total_for_percentage' => wp_slimstat_db::count_records('t1.searchterms <> ""')));
				break;
			case 'slim_p1_13':
			case 'slim_p2_10':
			case 'slim_p3_04':
				self::show_results('popular', $_report_id, 'country', array('total_for_percentage' => $current_pageviews));
				break;
			case 'slim_p1_15':
				self::show_rankings($_report_id);
				break;
			case 'slim_p1_16':
				self::show_hacker_ninja($_report_id);
				break;
			case 'slim_p1_17':
				self::show_results('popular', $_report_id, 'SUBSTRING(t1.language, 1, 2)', array('total_for_percentage' => $current_pageviews, 'as_column' => 'language', 'filter_op' => 'contains'));
				break;
			case 'slim_p2_02':
				self::show_visitors_summary($_report_id, wp_slimstat_db::count_records('t1.visit_id > 0 AND tb.type <> 1'), wp_slimstat_db::count_records('t1.visit_id > 0 AND tb.type <> 1', 'visit_id'));
				break;
			case 'slim_p2_03':
				self::show_results('popular', $_report_id, 'language', array('total_for_percentage' => $current_pageviews));
				break;
			case 'slim_p2_04':
				self::show_results('popular', $_report_id, 'browser', array('total_for_percentage' => $current_pageviews, 'more_columns' => ',tb.version'.((wp_slimstat::$options['show_complete_user_agent_tooltip']=='yes')?',tb.user_agent':'')));
				break;
			case 'slim_p2_05':
				self::show_results('popular', $_report_id, 'ip', array('total_for_percentage' =>$current_pageviews));
				break;
			case 'slim_p2_06':
				self::show_results('popular', $_report_id, 'platform', array('total_for_percentage' => $current_pageviews));
				break;
			case 'slim_p2_07':
				self::show_results('popular', $_report_id, 'resolution', array('total_for_percentage' => wp_slimstat_db::count_records('tss.resolution <> ""')));
				break;
			case 'slim_p2_09':
				self::show_plugins($_report_id, wp_slimstat_db::count_records('t1.visit_id > 0 AND tb.type <> 1'));
				break;
			case 'slim_p2_12':
				self::show_visit_duration($_report_id, wp_slimstat_db::count_records('visit_id > 0 AND tb.type <> 1', 'visit_id'));
				break;
			case 'slim_p2_13':
			case 'slim_p3_10':
				self::show_results('recent', $_report_id, 'country');
				break;
			case 'slim_p2_14':
				self::show_results('recent', $_report_id, 'resolution', array('join_tables' => 'tss.*'));
				break;
			case 'slim_p2_15':
				self::show_results('recent', $_report_id, 'platform', array('join_tables' => 'tb.*'));
				break;
			case 'slim_p2_16':
				self::show_results('recent', $_report_id, 'browser', array('join_tables' => 'tb.*'));
				break;
			case 'slim_p2_17':
				self::show_results('recent', $_report_id, 'language');
				break;
			case 'slim_p2_18':
				self::show_results('popular', $_report_id, 'browser', array('total_for_percentage' => $current_pageviews));
				break;
			case 'slim_p2_19':
				self::show_results('popular', $_report_id, 'CONCAT("p-", SUBSTRING(tb.platform, 1, 3))', array('total_for_percentage' => $current_pageviews, 'as_column' => 'platform'));
				break;
			case 'slim_p2_20':
				self::show_results('recent', $_report_id, 'user', array('custom_where' => 'notes LIKE "%[user:%"'));
				break;
			case 'slim_p2_21':
				self::show_results('popular_complete', $_report_id, 'user', array('total_for_percentage' => wp_slimstat_db::count_records('notes LIKE "%[user:%"'), 'custom_where' => 'notes LIKE "%[user:%"'));
				break;
			case 'slim_p3_02':
				self::show_traffic_sources_summary($_report_id, $current_pageviews);
				break;
			case 'slim_p3_06':
				self::show_results('popular_complete', $_report_id, 'domain', array('total_for_percentage' => wp_slimstat_db::count_records("t1.searchterms <> '' AND t1.domain <> '{$_SERVER['SERVER_NAME']}' AND t1.domain <> ''", 't1.id'), 'custom_where' => "t1.searchterms <> '' AND t1.domain <> '{$_SERVER['SERVER_NAME']}'"));
				break;
			case 'slim_p3_11':
			case 'slim_p4_17':
				self::show_results('popular', $_report_id, 'resource', array('total_for_percentage' => wp_slimstat_db::count_records('t1.domain <> ""'), 'custom_where' => 't1.domain <> ""'));
				break;
			case 'slim_p4_02':
				self::show_results('recent', $_report_id, 'resource', array('custom_where' => 'tci.content_type = "post"'));
				break;
			case 'slim_p4_03':
				self::show_results('recent', $_report_id, 'resource', array('custom_where' => 'tci.content_type <> "404"', 'having_clause' => 'HAVING COUNT(visit_id) = 1'));
				break;
			case 'slim_p4_04':
				self::show_results('recent', $_report_id, 'resource', array('custom_where' => '(t1.resource LIKE "%/feed%" OR t1.resource LIKE "%?feed=%" OR t1.resource LIKE "%&feed=%" OR tci.content_type LIKE "%feed%")'));
				break;
			case 'slim_p4_05':
				self::show_results('recent', $_report_id, 'resource', array('custom_where' => '(t1.resource LIKE "[404]%" OR tci.content_type LIKE "%404%")'));
				break;
			case 'slim_p4_06':
				self::show_results('recent', $_report_id, 'searchterms', array('custom_where' => '(t1.resource = "__l_s__" OR t1.resource = "" OR tci.content_type LIKE "%search%")'));
				break;
			case 'slim_p4_07':
				self::show_results('popular', $_report_id, 'category', array('total_for_percentage' => wp_slimstat_db::count_records('(tci.content_type LIKE "%category%")'), 'custom_where' => '(tci.content_type LIKE "%category%")', 'more_columns' => ',tci.category'));
				break;
			case 'slim_p4_08':
				self::show_spy_view($_report_id, 0);
				break;
			case 'slim_p4_10':
				self::show_spy_view($_report_id, -1);
				break;
			case 'slim_p4_11':
				self::show_results('popular', $_report_id, 'resource', array('total_for_percentage' => wp_slimstat_db::count_records('tci.content_type = "post"'), 'custom_where' => 'tci.content_type = "post"'));
				break;
			case 'slim_p4_12':
				self::show_results('popular', $_report_id, 'resource', array('total_for_percentage' => wp_slimstat_db::count_records('(t1.resource LIKE "%/feed%" OR t1.resource LIKE "%?feed=%" OR t1.resource LIKE "%&feed=%" OR tci.content_type LIKE "%feed%")'), 'custom_where' => '(t1.resource LIKE "%/feed%" OR t1.resource LIKE "%?feed=%" OR t1.resource LIKE "%&feed=%" OR tci.content_type LIKE "%feed%")'));
				break;
			case 'slim_p4_13':
				self::show_results('popular', $_report_id, 'searchterms', array('total_for_percentage' => wp_slimstat_db::count_records('(t1.resource = "__l_s__" OR t1.resource = "" OR tci.content_type LIKE "%search%")'), 'custom_where' => '(t1.resource = "__l_s__" OR t1.resource = "" OR tci.content_type LIKE "%search%")'));
				break;
			case 'slim_p4_15':
				self::show_results('recent', $_report_id, 'resource', array('custom_where' => '(tci.content_type = "category" OR tci.content_type = "tag")', 'join_tables' => 'tci.*'));
				break;
			case 'slim_p4_16':
				self::show_results('popular', $_report_id, 'resource', array('total_for_percentage' => wp_slimstat_db::count_records('(t1.resource LIKE "[404]%" OR tci.content_type LIKE "%404%")'), 'custom_where' => '(t1.resource LIKE "[404]%" OR tci.content_type LIKE "%404%")'));
				break;
			case 'slim_p4_18':
				self::show_results('popular', $_report_id, 'author', array('total_for_percentage' => wp_slimstat_db::count_records('tci.author <> ""')));
				break;
			case 'slim_p4_19':
				self::show_results('popular', $_report_id, 'category', array('total_for_percentage' => wp_slimstat_db::count_records('(tci.content_type LIKE "%tag%")'), 'custom_where' => '(tci.content_type LIKE "%tag%")', 'more_columns' => ',tci.category'));
				break;
			case 'slim_p4_20':
				self::show_spy_view($_report_id, 1);
				break;
			case 'slim_p4_21':
				self::show_results('popular_outbound', $_report_id, 'resource', array('total_for_percentage' => wp_slimstat_db::count_outbound()));
				break;
			case 'slim_p4_22':
				self::show_your_blog($_report_id);
				break;

			case 'slim_p7_02':
				$using_screenres = wp_slimstat_admin::check_screenres();
				include_once(WP_PLUGIN_DIR."/wp-slimstat/admin/view/right-now.php");
				break;
			default:
		}
		if (!empty($_POST['report_id'])) die();
	}
}