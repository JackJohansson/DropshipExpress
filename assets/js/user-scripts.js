/**
 * User scripts used by DropshipExpress plugin
 *
 */
(function ( $ ) {
	'use strict';

	$( document ).ready( function () {
		/**
		 * Update a product whenever it's been
		 * viewed by a user
		 */
		if ( "undefined" !== typeof dse_client_localization && true === dse_client_localization.constants.is_dse_product ) {
			$.ajax( {
				method: 'GET',
				url: dse_client_localization.constants.ajax_url,
				dataType: 'JSON',
				data: {
					product_id: dse_client_localization.constants.product_id,
					action: 'dse_update_visited_product'
				},
				success: function ( response ) {
					if ( 200 === response.status ) {
						// Notify the user if the product was updated and refresh the page
						toastr.options = {
							"closeButton": true,
							"debug": false,
							"newestOnTop": false,
							"progressBar": true,
							"positionClass": "toast-top-right",
							"preventDuplicates": false,
							"showDuration": "300",
							"hideDuration": "1000",
							"timeOut": "10000",
							"extendedTimeOut": "1000",
							"showEasing": "swing",
							"hideEasing": "linear",
							"showMethod": "fadeIn",
							"hideMethod": "fadeOut"
						};
						// Reload the page
						toastr.options.onHidden = function () {
							location.reload();
						}
						// Show a notification
						toastr.success( dse_client_localization.i18n.refresh_required );
					}
				}
			} );
		}
	} );
})( jQuery );