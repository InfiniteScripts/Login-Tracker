<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.infinitescripts.com/login-tracker
 * @since      1.0.0
 *
 * @package    Login_Tracker
 * @subpackage Login_Tracker/public
 */

/**
 *
 * @since      1.0.0
 * @package    Login_Tracker
 * @subpackage Login_Tracker/public
 * @author     Kevin Greene <kevin@infinitescripts.com>
 */
class Login_Tracker_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function login_tracker_public_enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Login_Tracker_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Login_Tracker_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/login-tracker-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function login_tracker_public_enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Login_Tracker_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Login_Tracker_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/login-tracker-public.js', array( 'jquery' ), $this->version, false );

	}

	public function login_tracker_add_admin_menu(  ) { 

		/**
		* This funcion adds the menu item and register the page with Wordpress
		**/

		add_options_page( 'Login Tracker', 'Login Tracker', 'manage_options', 'login_tracker_settings', 'login_tracker_options_page' );

	}

	public function login_tracker_reports(){

		/**
		* This funcion adds the menu item and register the page with Wordpress
		**/

		add_menu_page('Login Reports', 'Login Reports', 'manage_options', 'login_tracker_reports', 'login_tracker_report_page_render');
	}
		/** 
		* This function registers the settings to keep the APi key
		**/

	public function login_tracker_settings_init(  ) { 

		register_setting( 'pluginPage', 'login_tracker_settings' );

		add_settings_section(
			'login_tracker_pluginPage_section', 
			__( 'API Keys', 'login_tracker' ), 
			'login_tracker_settings_section_callback', 
			'pluginPage'
		);

		add_settings_field( 
			'login_tracker_ipinfodb_api_key', 
			__( 'IPinfoDB API key', 'login_tracker' ), 
			'login_tracker_ipinfodb_api_key_render', 
			'pluginPage', 
			'login_tracker_pluginPage_section' 
		);
	}
}