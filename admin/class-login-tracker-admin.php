<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.infinitescripts.com
 * @since      1.0.0
 *
 * @package    Login_Tracker
 * @subpackage Login_Tracker/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Login_Tracker
 * @subpackage Login_Tracker/admin
 * @author     Kevin Greene <kevin@infinitescripts.com>
 */
class Login_Tracker_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name   The ID of this plugin.
	 */
	private $plugin_name = "Login Tracker";

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version = "1.0.0";

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function login_tracker_admin_enqueue_styles() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Login_Tracker_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Login_Tracker_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/login-tracker-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function login_tracker_admin_enqueue_scripts() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Login_Tracker_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Login_Tracker_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/login-tracker-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function login_tracker_admin($user_login, $user){
		
		/**
		*
		* This function fires when a user logs in. It registers the time, IP address, then checks the IP against IP info DB to check the city state, and ZIP and stores all those values.
		*
		*/

		global $wpdb;

		// Get Api key from the settings
		$ip_api_key = get_option('login_tracker_ipinfodb_api_key');

		// Make sure that a key exists, it not exit.
		if(!$ip_api_key){
			return;
		}

		// Assemble Table name with correct prefix for other installs.
		$table_name = $wpdb->prefix . 'login_tracker';

		// Get users IP.
        $ip = $_SERVER['REMOTE_ADDR'];

        // Log the user.
		$login_user_id = $user->ID;

		// Log the time.
		$login_time =  date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']);
		
		/**
		* 
		* IP location has no documentation. After testing I found the array returned is as follows:
		*
		* [3] = Country
		* [5] = State
		* [6] = City
		* [7] = Zip
		* [8] = Lat
		* [9] = Lng
		*
		**/

		$url = 'http://api.ipinfodb.com/v3/ip-city/?key='. $ip_api_key .'&ip=' . $ip;

		// Make the HTTP request.
		$data = file_get_contents($url); 

		// Break the String into an array for easy reading.
		$pieces = explode(';', $data);

		// Assemble data array for easy insertion.
		$data =  array(
			'user_id' => $login_user_id,
			'ip_address' => $ip, 
			'login_time' => $login_time,
			'city' => $pieces[6],
			'state' => $pieces[5],
			'zip' => $pieces[7],

		);

		// Store the data in our unique table.
		$result = $wpdb->insert($table_name, $data);
	}

	public function login_tracker_export_button(){

		/**
		*
		* This function fires via ajax when export csv button is clicked.
		*
		**/

		// Retrieve the data passed via json.
		$data = $_POST['data'];
		
		// Set the headers for the csv.
		/**
		'Username',
		'First Name',
		'Last Name',
		'Email',
		**/
		$headers  = array(
			'Login Time',
			'IP',
			'City',
			'State',
			'Zip'
	 	);

		// Create an instance of our custom export class.
		$login_tracker_export = new Login_Tracker_CSV_Export();

		// Use the Export Class to create the csv version of table we're exporting.
		$login_tracker_export->login_tracker_export_table($headers, $data);
	}

	public function login_tracker_delete_file(){

		/**
		*
		* This function fires after a successful download. Since one csv will every be downloaded more than once, we dont 
		* want extra files remaining on the server. Thus we delete them.
		*
		**/

		// Grab the file name from the Ajax call.
		$filename = $_POST['file'];

		// WP upload dir
		$upload_dir = wp_upload_dir();

		// When saving the file, we used a custom dir as not to clutter 'login_tracker'. Assemble the full path.
		$file = $upload_dir['basedir'].'/login_tracker/' . $filename;

		// I wonder what WP delete file does.
		wp_delete_file($file);

		// End Ajax Call.
		die();
	}

}
