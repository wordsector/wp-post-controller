jQuery(document).ready(function($){

            var from = $('input[name="wppc_filter_date_from"]'),
			    to = $('input[name="wppc_filter_date_to"]');
 
			$( 'input[name="wppc_filter_date_from"], input[name="wppc_filter_date_to"]' ).datepicker( {dateFormat : "yy-mm-dd"} );			      		
    			from.on( 'change', function() {
				to.datepicker( 'option', 'minDate', from.val() );
			});
 
			to.on( 'change', function() {
				from.datepicker( 'option', 'maxDate', to.val() );
			});
});