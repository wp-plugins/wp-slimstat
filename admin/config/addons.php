<?php

// Avoid direct access to this piece of code
if (!function_exists('add_action')) exit(0);

// Update license keys, if needed
if (!empty($_POST['licenses'])){
	wp_slimstat_admin::update_option('addon_licenses', $_POST['licenses'], 'array');
}

echo '<div class="wrap"><h2>WP SlimStat Add-ons</h2>';
echo '<p>'.__('The following premium add-ons are available to extend the functionality of WP SlimStat. Each add-on can be installed as a separate plugin, which will receive regular updates via the WordPress Plugins panel. In order to be notified when a new version is available, please enter the license key you received after purchasing the add-on.','wp-slimstat').'</p>';

if (false === ($response = get_transient('wp_slimstat_addon_list'))){
	$response = wp_remote_get('http://slimstat.getused.to.it/update-checker/', array('headers' => array('referer' => get_site_url())));
	if(is_wp_error($response) || $response['response']['code'] != 200){
		$error_message = is_wp_error($response)?$response->get_error_message():$response['response']['code'].' '. $response['response']['message'];
		echo '<p>'.__('There was an error retrieving the add-ons list from the server. Please try again later. Error Message:','wp-slimstat').' '.$error_message.'</p></div>';
		return;
	}
	set_transient('wp_slimstat_addon_list', $response, 3600);
}

$list_addons = maybe_unserialize($response['body']);
if (!is_array($list_addons)){
	echo '<p>'.__('There was an error decoding the add-ons list from the server. Please try again later.','wp-slimstat').'</p></div>';
	return;
}

?>

<form action="<?php echo wp_slimstat_admin::$view_url ?>8" method="post" id="form-slimstat-options-tab-8">
<table class="wp-list-table widefat plugins" cellspacing="0">
	<thead>
	<tr>
		<th scope="col" id="name" class="manage-column column-name"><?php _e('Add-on','wp-slimstat') ?></th><th scope="col" id="description" class="manage-column column-description" style=""><?php _e('Description','wp-slimstat') ?></th></tr>
	</thead>

	<tfoot>
	<tr>
		<th scope="col" class="manage-column column-name" colspan="2"><input type="submit" value="Save Changes" class="button-primary" name="Submit"></tr>
	</tfoot>

	<tbody id="the-list">
		<?php foreach ($list_addons as $a_addon): ?>
		<tr id="<?php echo $a_addon['slug'] ?>">
			<td class="plugin-title">
				<strong><a target="_blank" href="<?php echo $a_addon['download_url'] ?>"><?php echo $a_addon['name'] ?></a></strong>
				<div class="row-actions-visible"><?php echo __('Version','wp-slimstat').' '.$a_addon['version'] ?><br/>Price: $<?php echo $a_addon['price']?></div>
			</td>
			<td class="column-description desc">
				<div class="plugin-description"><p><?php echo $a_addon['description'] ?></p></div>
				
				<?php if (is_plugin_active($a_addon['slug'].'/index.php')): ?>
				<div class="active second">
					License Key: <input type="text" name="licenses[<?php echo $a_addon['slug'] ?>]" value="<?php echo wp_slimstat::$options['addon_licenses'][$a_addon['slug']] ?>" size="50"/></div>
				<?php endif ?>
			</td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>
</form>