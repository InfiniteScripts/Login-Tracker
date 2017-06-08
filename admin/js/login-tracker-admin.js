(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	*/

	/** Add ajax for our export button. No one likes additional page loads. **/
	$( window ).load(function() {
		$(document).ready(function() { // wait for page to finish loading 
   			$("#export_csv_single").click(function () {
   				var data = $('#export_csv_single').data('params');
   				
				$.ajax({
        			type: "POST",
        			url: "/wp-admin/admin-ajax.php",
        			data: {
            			action: 'login_tracker_export_button',
            			data: data
					},
					dataType:"json",
        			success: function (output) {
        				$('#download_link').attr('href', output.dir + output.filename);
            			$('#download_link')[0].click();
            			ajax_delete_file(output.filename);
        			}
   				});
			});
			function ajax_delete_file(filename){

				$.ajax({
        			type: "POST",
        			url: "/wp-admin/admin-ajax.php",
        			data: {
            			action: 'login_tracker_delete_file',
            			file: filename
					},
        			success: function (output) {

            			console.log(output);

        			}
   				});
			}
		});
	});

})( jQuery );
