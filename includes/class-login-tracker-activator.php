<?php

/**
 * Fired during plugin activation
 *
 * @link       http://www.infinitescripts.com/login-tracker
 * @since      1.0.0
 *
 * @package    Login_Tracker
 * @subpackage Login_Tracker/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Login_Tracker
 * @subpackage Login_Tracker/includes
 * @author     Kevin Greene <kevin@infinitescripts.com>
 */
class Login_Tracker_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		global $wpdb;

		/* Lets add the table to store login data */
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'login_tracker';

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			login_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			user_id smallint(5) NOT NULL,
			ip_address varchar(20) NOT NULL,
			city varchar(30) NOT NULL,
			state varchar(30) NOT NULL,
			street varchar(100) NOT NULL,
			zip int(5) NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

}
