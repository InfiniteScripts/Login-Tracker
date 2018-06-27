<?php

/**
* 
* This class extends the WP List Table class to easily style and render tables like the WP users table while using our Login TRacker data
*
*/
class Login_Tracker_Table extends WP_List_Table {

    public function __construct() {

        /**
        * Constructor, we override the parent to pass our own arguments
        * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
        **/

        parent::__construct( 
            array(
                'singular'=> 'wp_list_text_link', //Singular label
                'plural' => 'wp_list_test_links', //plural label, also this well be one of the table css class
                'ajax'   => false //We won't support Ajax for this table
            ) 
        );
    }

    public function get_columns() {
        return $columns= array(
            'login_time'=>__('Login Time'),
            'ip_address'=>__('IP Address'),
            'city'=>__('City'),
            'state'=>__('State'),
            'zip'=>__('Zip')
        );
    }

    protected function get_sortable_columns() {
        return $sortable_columns = array(
            'login_time'    => array( 'login_time', false ),
            'city'   => array( 'city', false ),
            'state' => array( 'state', false ),
        );
    }

    public function prepare_items() {

        /**
        *
        * Prepare the table with different parameters, pagination, columns and table elements.
        *
        **/

        global $wpdb, $_wp_column_headers;
        $table_name = $wpdb->prefix . 'login_tracker';
        $screen = get_current_screen();
        $user_id = $_GET['login_tracker_user_id'];
        // Preparing your query.
        $query = "SELECT login_time, ip_address, city, state, zip FROM $table_name WHERE user_id = $user_id";

        // Parameters that are going to be used to order the result.
        $orderby = !empty($_GET["orderby"]) ? $_GET["orderby"] : 'ASC';
        $order = !empty($_GET["order"]) ? $_GET["order"] : '';

        if(!empty($orderby) & !empty($order)){ 
            $query.=' ORDER BY '.$orderby.' '.$order; 
        }
        
        // Number of elements in your table?
        $totalitems = $wpdb->query($query); //return the total number of affected rows

        // How many to display per page?
        $perpage = 20;

        // Which page is this?
        $paged = !empty($_GET["paged"]) ? $_GET["paged"] : '';

        // Page Number
        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ 
            $paged=1; 
        } 

        // How many pages do we have in total? 
        $totalpages = ceil($totalitems/$perpage); //adjust the query to take pagination into account 

        if(!empty($paged) && !empty($perpage)){
            $offset=($paged-1)*$perpage; $query.=' LIMIT '.(int)$offset.','.(int)$perpage; 
        } 

        // Register the pagination.
        $this->set_pagination_args( 
            array(
                "total_items" => $totalitems,
                "total_pages" => $totalpages,
                "per_page" => $perpage,
            )
        );

        // The pagination links are automatically built according to those parameters.
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $results = $wpdb->get_results($query);

        // Fetch the items.
        $this->items = $results;
        return $results;
    }

    public function login_tracker_render_csv_export_button($data){

        /**
        *
        * This function renders the export button to trigger the export class.
        *
        **/

        ?>
        <button type="button" id="export_csv_single" data-params="<?php echo htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8'); ?>">Export CSV</button>
        <!-- This anchor is here so for the js to attach the location of the file, since we can't trigger a download in this function because its ajax. The js attaches the location of the csv file, then cliocks the link. -->
        <a href="" id="download_link" ></a>
        <?php
    }
    
    protected function column_default( $item, $column_name ) {

        /**
        *
        * This function adds the column headers
        *
        **/

        switch( $column_name ) { 
            case "login_time":
                return $item->login_time;
            case 'ip_address':
                return $item->ip_address;
            case "city":
                return $item->city;
            case 'state':
                return $item->state;
            case "zip":
                return $item->zip;
        }
    }
}