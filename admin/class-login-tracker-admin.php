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
		
		global $wpdb;

		$ip_api_key = '9154ee208e7ea95c250c705a40d6881ad0511dfaa152d5ebbb4e4ef3c6bf0b76';
		$table_name = $wpdb->prefix . 'login_tracker';
        $ip = $_SERVER['REMOTE_ADDR'];
		$login_user_id = $user->ID;
		$login_time =  date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME']);
		
		//IP location has zero docs. The array is as follows:
		// [3] = Country
		// [5] = State
		// [6] = City
		// [7] = Zip
		// [8] = Lat
		// [9] = Lng

		$url = 'http://api.ipinfodb.com/v3/ip-city/?key='. $ip_api_key .'&ip=' . $ip;

		// Make the HTTP request
		$data = file_get_contents($url); 
		// Break the String into an array for easy reading
		$pieces = explode(';', $data);

		$data =  array(
			'user_id' => $login_user_id,
			'ip_address' => $ip, 
			'login_time' => $login_time,
			'city' => $pieces[6],
			'state' => $pieces[5],
			'zip' => $pieces[7],

		);
    	
		$result = $wpdb->insert($table_name, $data);
	}
	public function login_tracker_export_button(){
		$data = $_POST['data'];
		

		
		$headers  = array(
			'Username',
			'First Name',
			'Last Name',
			'Email',
			'Login Time',
			'IP',
			'City',
			'State',
			'Zip'
	 	);


		$login_tracker_export = new Login_Tracker_CSV_Export();
		$login_tracker_export->login_tracker_export_table($headers, $data);
	}

	public function login_tracker_delete_file(){
		$filename = $_POST['file'];

		$upload_dir = wp_upload_dir();
		$file = $upload_dir['basedir'].'/login_tracker/' . $filename;

		wp_delete_file($file);
		die($file);
	}

}
