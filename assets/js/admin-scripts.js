(function ( $ ) {

	'use strict';

	/**
	 * Bootstrap navigations
	 *
	 */
	$( "ul.nav-tabs a" ).click( function ( e ) {
		e.preventDefault();
		$( this ).tab( 'show' );
	} );

	$( document ).ready( function () {

		/**
		 * Initiate touchspin
		 */
		$( '.dse-selector-schedule-value' ).TouchSpin( {
			buttondown_class: 'btn btn-secondary',
			buttonup_class: 'btn btn-secondary',
			min: 1,
			max: 60,
			step: 1,
			decimals: 0,
			boostat: 5,
			maxboostedstep: 10,
		} );

		$( '#dse-selector-review-count' ).TouchSpin( {
			buttondown_class: 'btn btn-secondary',
			buttonup_class: 'btn btn-secondary',
			min: 1,
			max: 60,
			step: 1,
			decimals: 0,
			boostat: 5,
			maxboostedstep: 10,
		} );

		$( '.dse-selector-price-flat' ).TouchSpin( {
			buttondown_class: 'btn btn-secondary',
			buttonup_class: 'btn btn-secondary',
			forcestepdivisibility: 'none',
			min: 0.01,
			step: 1,
			decimals: 2,
			boostat: 5,
			maxboostedstep: 10,
		} );

		/**
		 * Initiate Popper
		 *
		 */
		$( '.dse-popover' ).popover( {
			trigger: 'hover',
		} );

		/**
		 * Initiate Select2
		 */
		$( '.dse-select2' ).select2( {
			placeholder: "Select",
			minimumResultsForSearch: Infinity,
			width: '100%',
			dropdownAutoWidth: true
		} );

		$( '.dse-product-search' ).select2( {
			placeholder: "Select",
			minimumResultsForSearch: 3,
			width: '100%'
		} );

		$( '#dse_select_search_source' ).on( 'select2:select', function ( e ) {
			let selectData = e.params.data;
			$( "form.dse-search-product-form[data-id='" + selectData.id + "']" ).fadeIn().siblings( 'form' ).hide();
		} );

		/**
		 * Initiate Form Repeater for AliExpress Replace rules
		 */
		if ( $( '.dse-replace-rules-wrapper' ).length ) {

			/**
			 * AliExpress search & replace rules
			 *
			 * @type {jQuery|jQuery.fn.init|HTMLElement}
			 */
			let aliexpressReplaceRulesRepeater = $( '#dse-aliexpress-replace-rules' ).repeater( {
				initEmpty: true,
				show: function () {
					$( this ).slideDown();
				},
				hide: function ( deleteElement ) {
					$( this ).slideUp( deleteElement );
				}
			} );

			// Set the values for AliExpress Replace Rules
			if ( typeof dse_admin_localization === 'object' && "aliexpress_replace_rules" in dse_admin_localization ) {
				let aliexpressReplaceRules = [];
				$.each( dse_admin_localization.aliexpress_replace_rules, function ( index, value ) {
					aliexpressReplaceRules.push( { 'search': value.search, 'value': value.value } );
				} );
				aliexpressReplaceRulesRepeater.setList( aliexpressReplaceRules );

				// We need to rerun the loop after the repeater has set
				// the values, because it refreshes the DOM if it's done before
				let aliexpressSingleReplaceRule = $( '.dse-aliexpress-replace-rule' );

				$.each( dse_admin_localization.aliexpress_replace_rules, function ( index, value ) {
					aliexpressSingleReplaceRule.eq( index ).find( '.dse-apply-title' ).prop( 'checked', 'yes' === value.apply_title );
					aliexpressSingleReplaceRule.eq( index ).find( '.dse-apply-desc' ).prop( 'checked', 'yes' === value.apply_desc );
					aliexpressSingleReplaceRule.eq( index ).find( '.dse-apply-attr' ).prop( 'checked', 'yes' === value.apply_attr );
					aliexpressSingleReplaceRule.eq( index ).find( '.dse-apply-tags' ).prop( 'checked', 'yes' === value.apply_tags );
					aliexpressSingleReplaceRule.eq( index ).find( '.dse-apply-reviews' ).prop( 'checked', 'yes' === value.apply_reviews );
				} );
			}
		}

		/**
		 * Fetch the store search results via ajax
		 *
		 */
		$( 'form.dse-search-product-form' ).on( 'submit', function ( e ) {
			e.preventDefault();

			let spinnerClass   = 'dse-spinner dse-spinner--right dse-spinner--md dse-spinner--light';
			let button         = $( this ).find( 'button[type=submit]' );
			let formData       = $( this ).serialize();
			let resultsWrapper = $( '#dse-import-results' );
			resultsWrapper.block( {
				message: '<div class="blockui "><span>' + dse_admin_localization.i18n.processing + '</span><span><div class="dse-spinner dse-spinner--v2 dse-spinner--primary "></div></span></div>',
				css: { border: '0px', backgroundColor: 'transparent', textAlign: 'center', width: 'auto' }
			} );

			$.ajax( {
				method: 'GET',
				url: $( this ).attr( 'action' ),
				dataType: 'html',
				data: formData,
				beforeSend: function () {
					// Add a spinner and disable the search button
					button.addClass( spinnerClass ).prop( 'disabled', true );
					// Add a loader to the results container
					resultsWrapper.block( {
						message: '<div class="blockui "><span>' + dse_admin_localization.i18n.searching + '</span><span><div class="dse-spinner dse-spinner--v2 dse-spinner--primary "></div></span></div>',
						css: {
							border: '0px',
							backgroundColor: 'transparent',
							textAlign: 'center',
							width: 'auto',
						},
						overlayCSS: {
							backgroundColor: '#000000',
							opacity: 0.1,
							cursor: 'wait'
						}
					} );
				},
				success: function ( response ) {
					$( '#dse-import-results' ).html( response );
					$( this ).find( 'input.dse_product_search_page' ).val( 1 );
				},
				error: function ( jqXHR ) {
					toastr.error( dse_admin_localization.i18n.error_unknown );
				},
				complete: function () {
					// Remove the spinners, enable the button and remove the loader
					button.removeClass( spinnerClass ).prop( 'disabled', false );
					resultsWrapper.unblock();

					// Unblock the pagination if exists
					if ( $( '#dse-import-pagination' ).length ) {
						$( '#dse-import-pagination' ).unblock();
					}
				}
			} );
		} );

		/**
		 * Import a single product into shop
		 *
		 */
		$( '#dse-import-results' ).on( 'submit', '.dse-search-result', function ( e ) {
			e.preventDefault();
			let spinnerClass = 'dse-spinner dse-spinner--right dse-spinner--md dse-spinner--light';
			let button       = $( this ).find( 'button[type=submit]' );
			let formData     = $( this ).serialize();
			$.ajax( {
				method: 'POST',
				url: $( this ).attr( 'action' ),
				dataType: 'json',
				data: formData,
				beforeSend: function () {
					// Add a spinner and disable the search button
					button.addClass( spinnerClass ).prop( 'disabled', true ).text( 'Importing ...' );
				},
				success: function ( response ) {
					if ( true === response.success ) {
						button.removeClass( 'btn-success' ).addClass( 'btn-danger' ).text( 'Imported' );
					} else {
						toastr.error( response.message );
						button.text( 'Import' ).prop( 'disabled', false );
					}
				},
				error: function ( jqXHR ) {
					toastr.error( dse_admin_localization.i18n.error_unknown );
					button.text( 'Import' ).prop( 'disabled', false );
				},
				complete: function () {
					// Remove the spinners, enable the button and remove the loader
					button.removeClass( spinnerClass );
				}
			} );
		} );

		/**
		 * Ajax product search's pagination
		 */
		$( '#dse-import-results' ).on( 'click', '#dse-import-pagination ul li', function ( e ) {

			e.preventDefault();

			// Find the proper form
			let formName = $( '#dse-search-results-source' ).val();

			if ( formName.length ) {
				let form = $( '.dse-search-product-form[data-id="' + formName + '"]' );

				// Set the page number for the form
				let pageNumber = $( this ).data( 'page' );

				if ( pageNumber ) {

					form.find( '.dse_product_search_page' ).val( pageNumber );

					// Block the pagination
					$( this ).block();

					// Submit the form with the new page set
					form.submit();

					// Scroll to top
					$( "html, body" ).animate( { scrollTop: 0 }, "slow" );
				}
			}

		} );

		/**
		 *
		 */
		$( 'button.dse-single-publish-submit' ).on( 'click', function ( e ) {
			e.preventDefault();

			let form           = $( this ).closest( 'form.dse-single-publish-form' );
			let formData       = form.serializeArray();
			let imported_badge = $( '#dse-imported-count-badge' );

			// Add the action
			formData.push( { name: "dse_single_publish_submit", value: $( this ).val() } );

			$.ajax( {
				method: 'POST',
				url: form.attr( 'action' ),
				dataType: 'json',
				data: $.param( formData ),
				beforeSend: function () {
					// Block the product
					form.block( {
						message: '<div class="blockui "><span>' + dse_admin_localization.i18n.processing + '</span><span><div class="dse-spinner dse-spinner--v2 dse-spinner--primary "></div></span></div>',
						css: {
							border: '0px',
							backgroundColor: 'transparent',
							textAlign: 'center',
							width: 'auto',
						},
						overlayCSS: {
							backgroundColor: '#000000',
							opacity: 0.1,
							cursor: 'wait'
						}
					} );
				},
				success: function ( response ) {
					if ( true === response.success ) {
						// Notify the results
						toastr.success( response.message );

						// Remove the form
						form.hide( 'slow', function () {
							form.parent( 'div' ).remove();

							// Refresh the page if there's nothing left
							if ( 0 === $( '.dse-single-publish-form' ).length ) {
								location.reload();
							}
						} );
						// Deduct the post counts by 1
						if ( imported_badge.html() > 0 ) {
							imported_badge.html( imported_badge.html() - 1 );
						}
					} else {
						toastr.error( response.message );
					}
				},
				error: function ( jqXHR ) {
					toastr.error( dse_admin_localization.i18n.error_unknown );
				},
				complete: function () {
					form.unblock();
				}
			} );

		} );

		// Remove a single image from a product before
		// being published
		$( '.dse-remove-single-imported-image' ).click( function ( e ) {
			e.preventDefault();
			$( this ).closest( '.dse-single-image-item' ).remove();
		} );

		// Remove a single review from a product before
		// being published
		$( '.dse-remove-single-imported-review' ).click( function ( e ) {
			e.preventDefault();
			$( this ).closest( '.dse-single-review' ).remove();
		} );

		// Remove a single variation
		$( '.dse-variation-controls a' ).click( function ( e ) {
			e.preventDefault();
			$( this ).closest( '.dse-single-variation-wrapper' ).remove();
		} );

		// Select2 for initial import rule
		$( '.dse-import-rules-select' ).select2( {
			placeholder: "Select",
			minimumResultsForSearch: Infinity,
			width: '100%'
		} );

		$( '#dse_import_rules_cat' ).select2( {
			placeholder: "Choose a category",
			minimumResultsForSearch: 3,
			width: '100%'
		} );

		// touchspin for initial import rule
		$( '.dse-import-rules-timer' ).TouchSpin( {
			buttondown_class: 'btn btn-secondary',
			buttonup_class: 'btn btn-secondary',
			min: 1,
			max: 60,
			step: 1,
			decimals: 0,
			boostat: 5,
			maxboostedstep: 10,
			verticalbuttons: true,
		} );

		/**
		 * Format the credit card inputs
		 *
		 */
		if ( $( '.dse_cc_number' ).length ) {
			$( '.dse_cc_number' ).toArray().forEach( function ( field ) {
				return new Cleave( field, {
					creditCard: true,
				} );
			} );
		}

		if ( $( '.dse_cc_year' ).length ) {
			$( '.dse_cc_year' ).toArray().forEach( function ( field ) {
				return new Cleave( field, {
					date: true,
					datePattern: [ 'Y' ]
				} );
			} );
		}

		if ( $( '.dse_cc_month' ).length ) {
			$( '.dse_cc_month' ).toArray().forEach( function ( field ) {
				return new Cleave( field, {
					date: true,
					datePattern: [ 'm' ]
				} );
			} );
		}
	} );
})( jQuery );