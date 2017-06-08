<?php

/**
 * Provide a csv export class for the reports in this plugin
 *
 *
 * @link       http://infinitescripts.com/login_tracker
 * @since      1.0.0
 *
 * @package    Login Tracker
 * @subpackage Login_Tracker/admin/
 */

class Login_Tracker_CSV_Export{

	public function login_tracker_export_table($headers, $data){
		
		$filename = 'login_tracker-' . time() . '.csv';
		$data_rows = array();
	
		$upload_dir = wp_upload_dir();
		$dir_name = $upload_dir['basedir'].'/login_tracker';
		
		if ( ! file_exists( $dir_name ) ) {
    		wp_mkdir_p( $dir_name );
		}

		foreach ( $data as $item ) {
			$row = array();
			foreach ($item as $sub){
				$row[] = $sub;
			}
			
			
			$data_rows[] = $row;
		}
		
		$fh = fopen( $dir_name  . '/' . $filename, 'w' );
	
		
		
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/octet-stream');
		header( "Content-Disposition: attachment; filename={$filename}" );
		header( 'Content-Length: ' . filesize('{$filename}'));
		header( 'Expires: 0' );
		header( 'Pragma: public' );
	
		fputcsv( $fh, $headers );
	

		foreach ( $data_rows as $data_row ) {
			fputcsv( $fh, $data_row );
		}

		fclose( $fh );
		$return_data = array(
			'dir' =>  $upload_dir['baseurl'].'/login_tracker/',
			'filename' => $filename
		);
		echo json_encode($return_data);

		die();
	}
}