<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://infinitescripts.com/login_tracker
 * @since      1.0.0
 *
 * @package    Login Tracker
 * @subpackage Login_Tracker/public/partials
 */



add_filter('manage_users_columns', 'login_tracker_user_id_column');
function login_tracker_user_id_column($columns) {
    $columns['last_login'] = 'Last Login';
    return $columns;
}
 
add_action('manage_users_custom_column',  'last_login_column_content', 110, 3);
function last_login_column_content($value, $column_name, $user_id) {
	
	switch ($column_name) {
		case 'last_login':
			$last_login = login_tracker_get_last_login($user_id);
			return ($last_login);
 		break;
 		default:
 			return $value;
 		break;
 		
 	}
}

function login_tracker_get_last_login( $user_id ){
	global $wpdb;
	$table_name = $wpdb->prefix . 'login_tracker';

	$login_query = "SELECT MAX(login_time) as login_time FROM $table_name WHERE user_id = $user_id";
	$results = $wpdb->get_results($login_query);
	$link = '<a href="' . admin_url() . '/admin.php?page=login_tracker_reports&login_tracker_user_id=' . $user_id . '">' . $results[0]->login_time . '</a>';
	return($link);
}


add_filter( 'manage_users_sortable_columns', 'login_tracker_user_sortable_columns' );
function login_tracker_user_sortable_columns( $columns ) {
	$columns['last_login'] = 'Last Login';
	return $columns;
}


add_action('pre_user_query', 'login_tracker_my_user_query');
function login_tracker_my_user_query($userquery){
	if($userquery->query_vars['orderby'] == 'Last Login') {		
		global $wpdb;
		$table_name = $wpdb->prefix . 'login_tracker';
		$userquery->query_from .= " LEFT OUTER JOIN $table_name AS alias ON ($wpdb->users.ID = alias.user_id) ";		
		$userquery->query_orderby = " ORDER BY alias.login_time ".($userquery->query_vars["order"] == "ASC" ? "asc " : "desc ");//set sort order
	}
}

function login_tracker_report_page_render(){
	global $wpdb;

	$user_id = $_GET['login_tracker_user_id'];
	$table_name = $wpdb->prefix . 'login_tracker';

	/*$header_row = array(
		0 => 'Display Name',
		1 => 'Email',
		2 => 'Institution',
		3 => 'Registration Date',
	); */

	/*$headers  = array(
		'Username',
		'First Name',
		'Last Name',
		'Email',
		'Login Time',
		'IP',
		'City',
		'State',
	 ); */


	if($user_id){

		$user_info = get_userdata( $user_id );

		if( $user_info->first_name){
      		echo '<h2>User:  ' . $user_info->first_name . ' ' . $user_info->last_name . '</h2>';
      	}
      	echo '<h2>Username:  ' . $user_info->user_login . '</h2>';
      	echo '<h2>Email:  ' . $user_info->user_email . '</h2>';
      	

      	login_tracker_render_table($user_id);
	} else {

		echo '<p>No user selected. To acccess a user report, check the  <a href="/wp-admin/users.php">Users</a> tab and click their last login time.</p>';  

	}
}

function login_tracker_render_table(){
	$login_tracker_list_table = new Login_Tracker_Table();
	$results = $login_tracker_list_table->prepare_items( );
	$login_tracker_list_table->display();
	$login_tracker_list_table->login_tracker_render_csv_export_button($results);
}
	
