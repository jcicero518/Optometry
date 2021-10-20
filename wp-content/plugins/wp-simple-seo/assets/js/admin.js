jQuery( document ).ready( function( $ ) {

	/**
	 * Bulk Actions View
	 */
	if ( $( '#wp-simple-seo-bulk-actions' ).length > 0 ) {
		$( 'form#posts-filter' ).before( $( '#wp-simple-seo-bulk-actions' ) );
	}

	/**
	 * Bulk Actions
	 */
	$( 'input#doaction, input#doaction2' ).on( 'click', function( e ) {

		// Bail if no Bulk Actions are specified
		if ( wp_simple_seo_admin.bulk_actions.length == 0 ) {
			return true;
		}

		// Depending on the input button clicked, determine whether any Bulk Actions were selected
		switch ( $( this ).attr( 'id' ) ) {

			case 'doaction':
				// Bail if the chosen action isn't for an Addon
				if ( wp_simple_seo_admin.bulk_actions.indexOf( $( 'select[name=action]' ).val() ) == -1 ) {
					return true;
				}

				// Get action
				var action = $( 'select[name=action]' ).val().replace( new RegExp( '-', 'g' ), '_' );
				break;

			case 'doaction2':
				// Bail if the chosen action isn't for an Addon
				if ( wp_simple_seo_admin.bulk_actions.indexOf( $( 'select[name=action2]' ).val() ) == -1 ) {
					return true;
				}

				// Get action
				var action = $( 'select[name=action2]' ).val().replace( new RegExp( '-', 'g' ), '_' );
				break;

			default:
				return true;
				break;

		}

		// If here, we are running a Bulk Action belonging to this Plugin or an Addon
		// Prevent form submission
		e.preventDefault();

		// Get selected Post IDs
		var post_ids = [];
		$( 'input[name="post[]"]:checked' ).each( function() {
			post_ids.push( $( this ).val() );
		} );

		// If no Posts were selected, show an alert and cancel
		if ( post_ids.length == 0 ) {
			alert( wp_simple_seo_admin.messages.no_posts_selected );
			return;
		}

		// Display Bulk Actions view
		$( '#wp-simple-seo-bulk-actions' ).fadeIn();

		// Start Bulk Actions
		$( '#wp-simple-seo-bulk-actions .progress-bar' ).wp_simple_seo_bulk_actions( {
			action: 	action,
			post_ids: 	post_ids,
			elements: 	{
				container: 	$( '#wp-simple-seo-bulk-actions' ),
				current: 	$( '#wp-simple-seo-bulk-actions .progress-number .current' ),
				total: 		$( '#wp-simple-seo-bulk-actions .progress-number .total' ),
				cancel: 	$( '#wp-simple-seo-bulk-actions .cancel.button' ),
				checkboxes: $( 'input#cb-select-all-1, input#cb-select-all-2, input[name="post[]"]' ),
				selects: 	$( 'select[name=action], select[name=action2]' ),
			}
		} );

	} );

} );

/**
 * Performs synchronous requests for the given Post IDs, sending them
 * to the AJAX endpoint.
 *
 * @since 	1.0.0
 *
 * @param 	object 	params 		Params:
 * - action 	string 	AJAX Bulk Action
 * - post_ids 	array 	Post IDs
 */
( function( $ ) {

   	$.fn.wp_simple_seo_bulk_actions = function( params ) {

   		// Set flag to indicate whether the user manually cancelled the process
   		var cancelled = false;

   		// Define Total
   		$( params.elements.total ).text( params.post_ids.length );
      
		$( this ).synchronous_request( {
			url:                ajaxurl,
			number_requests:    params.post_ids.length,
			offset:             0,
			data: {
			    action: 		params.action,
			    post_ids: 		params.post_ids
			},

			onRequestSuccess: function( response, currentIndex ) {

			    // Update counter
			    $( params.elements.current ).text( ( currentIndex + 1 ) );

			    // Update Post's cell value
			    $( 'tr#post-' + params.post_ids[ currentIndex ] + ' td.' + params.action ).html( response.data.output );

			    // Bail if the user has manually cancelled the process
			    if ( cancelled == true ) {
			    	this.onFinished();
			    	return false;
			    }

			    return true;

			},

			onRequestError: function( xhr, textStatus, e, currentIndex ) {

				// Update counter
			    $( params.elements.current ).text( ( currentIndex + 1 ) );

			    // Update Post's cell value
			    $( 'tr#post-' + params.post_ids[ currentIndex ] + ' td.' + params.action ).text( 'Error' );

			    // Bail if the user has manually cancelled the process
			    if ( cancelled == true ) {
			    	this.onFinished();
			    	return false;
			    }

			    return true;

			},

			onFinished: function() {

				$( params.elements.current ).text( wp_simple_seo_admin.messages.finished );
				$( params.elements.total ).text( '' );

				// Delay hiding the progress bar by 2 seconds
				setTimeout( function() {

					// Hide Progress Bar
					$( params.elements.container ).fadeOut( 'slow', function() {

						// Reset progress bar
						$( params.elements.current ).text( '0' );
						$( params.elements.total ).text( '0' );

						// Reset cancel button
						$( params.elements.cancel ).text( wp_simple_seo_admin.messages.cancel );

					} );

					// Deselect Posts
					$( params.elements.checkboxes ).prop( 'checked', false );

					// Reset Bulk Actions dropdowns
					$( params.elements.selects ).val( '-1' );

				}, 1000 );

			}
		} );

		// Cancel synchronous request if the Cancel button is clicked
		$( params.elements.cancel).on( 'click', function( e ) {

			e.preventDefault();

			cancelled = true;

			$( this ).attr( 'disabled', 'disabled' ).text( wp_simple_seo_admin.messages.cancelling );

		} );

   }; 

} )( jQuery );