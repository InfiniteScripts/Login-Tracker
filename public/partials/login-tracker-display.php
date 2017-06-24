<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://infintiescripts.com/login_tracker
 * @since      1.0.0
 *
 * @package    Login Tracker
 * @subpackage Login_Tracker/public/partials
 */


function login_tracker_options_page(  ) {
 
	?>
	<form action='options.php' method='post'>
		
		<h2>Login Tracker Settings</h2>
		
		<?php
			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );
			submit_button();
		?>
		
	</form>
	<?php

}

function login_tracker_ipinfodb_api_key_render(  ) { 

	$options = get_option( 'login_tracker_settings' );
	?>
	<input type='text' name='login_tracker_settings[login_tracker_ipinfodb_api_key]' size="128" value='<?php echo $options['login_tracker_ipinfodb_api_key']; ?>'>
	<?php

}


function login_tracker_settings_section_callback(  ) { 

	echo __( 'IPinfoDB APi key is required for IP location.', 'login_tracker' );

}


add_filter('manage_users_columns', 'pippin_add_user_id_column');
function pippin_add_user_id_column($columns) {
    $columns['last_login'] = 'Last Login';
    return $columns;
}
 
add_action('manage_users_custom_column',  'last_login_column_content', 110, 3);
function last_login_column_content($value, $column_name, $user_id) {
	
	switch ($column_name) {
		case 'last_login':
			$last_login = get_last_login($user_id);
			return ($last_login);
 		break;
 		default:
 			return $value;
 		break;
 		
 	}
}

function get_last_login( $user_id ){
	global $wpdb;
	$table_name = $wpdb->prefix . 'login_tracker';

	$login_query = "SELECT MAX(login_time) as login_time FROM $table_name WHERE user_id = $user_id";
	$results = $wpdb->get_results($login_query);

	return($results[0]->login_time);
}


add_filter( 'manage_users_sortable_columns', 'user_sortable_columns' );
function user_sortable_columns( $columns ) {
	$columns['last_login'] = 'Last Login';
	return $columns;
}


add_action('pre_user_query', 'my_user_query');
function my_user_query($userquery){
	if('Last Login'==$userquery->query_vars['orderby']) {//check if church is the column being sorted
		
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'login_tracker';
		$userquery->query_from .= " LEFT OUTER JOIN $table_name AS alias ON ($wpdb->users.ID = alias.user_id) ";//note use of alias
		
		$userquery->query_orderby = " ORDER BY alias.login_time ".($userquery->query_vars["order"] == "ASC" ? "asc " : "desc ");//set sort order
	}
}