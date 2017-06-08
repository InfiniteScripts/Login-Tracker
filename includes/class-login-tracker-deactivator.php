<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://www.infinitescripts.com/login-tracker
 * @since      1.0.0
 *
 * @package    Login_Tracker
 * @subpackage Login_Tracker/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Login_Tracker
 * @subpackage Login_Tracker/includes
 * @author     Kevin Greene <kevin@infinitescripts.com>
 */
class Login_Tracker_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		global $wpdb;

		//We need to remove the database table we added
		$table_name = $wpdb->prefix . 'login_tracker';
    	$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
    
	}

}
