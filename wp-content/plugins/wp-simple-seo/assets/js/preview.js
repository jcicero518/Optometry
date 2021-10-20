var wp_simple_seo_update_preview,
	wp_simple_seo_updating_preview = false;

jQuery( document ).ready( function( $ ) {

	// Post Editor: Display Meta Editor when the Edit button is clicked
	$( '#wp-simple-seo a.edit-meta' ).on( 'click', function( e ) {

		e.preventDefault();
		$( '#wp-simple-seo .meta-editor' ).show();

	} );

	// Term Editor: Display Meta Editor when the Edit button is clicked
	$( 'tr.wp-simple-seo a.edit-meta' ).on( 'click', function( e ) {

		e.preventDefault();
		$( 'tr.wp-simple-seo .meta-editor' ).show();

	} );

	// Post Edit: Update Preview on field change
	wp_simple_seo_update_preview = function( type, form ) {

		// If we're currently running an AJAX request, don't run another one
		if ( wp_simple_seo_updating_preview ) {
			return;
		}

		// Get serialized form data
		var form_data = $( form ).serialize();

		// If no form data exists, bail
		if ( form_data.length == 0 ) {
			return;
		}

		// Depending on the object type we're previewing, maybe append some form data
		switch ( type ) {
			/**
			 * Post
			 */
			case 'post':
				// Append TinyMCE content and Permalink, if they're available
				if ( tinymce.activeEditor ) {
					form_data += '&post_content=' + tinymce.activeEditor.getContent();
				}
				form_data += '&post_url=' + $( 'span#sample-permalink' ).text();
				break;
		}

		// Show the loading spinner
		$( 'span[data-preview="spinner"]' ).css( 'visibility', 'visible' );

		// Set the flag so we don't send multiple AJAX requests to the server
		wp_simple_seo_updating_preview = true;

		// Send an AJAX request to fetch the parsed title, URL and description
		$.post( 
			wp_simple_seo_preview.ajax, 
			{
				'action': 		'wp_simple_seo_get_snippet_preview',
				'id': 			wp_simple_seo_preview.id, // Post or Term ID
				'type': 		type, // post | term

				// Template tags for Title and Description
				'title': 		$( '#wp-simple-seo input[data-preview="title"]' ).val(),
				'description': 	$( '#wp-simple-seo textarea[data-preview="description"]' ).val(),

				// Form data
				'form_data': 	form_data, 	

				// Security
				'nonce': 		wp_simple_seo_preview.get_snippet_preview_nonce
			},
			function( response ) {

				// If the request was successful, update the Snippet Preview
				if ( response.success ) {
					// Title
					$( '.count .number', $( 'div.wp-simple-seo-preview span[data-preview="title"]' ).parent() ).text( response.data.title.length );
					if ( response.data.title.length > wp_simple_seo_preview.max_meta_title_length ) {
						$( 'div.wp-simple-seo-preview span[data-preview="title"]' ).html( response.data.title.substr( 0, wp_simple_seo_preview.max_meta_title_length ) + '...' );
						$( '.count', $( 'div.wp-simple-seo-preview span[data-preview="title"]' ).parent() ).addClass( 'alert' );
					} else {
						$( 'div.wp-simple-seo-preview span[data-preview="title"]' ).html( response.data.title );
						$( '.count', $( 'div.wp-simple-seo-preview span[data-preview="title"]' ).parent() ).removeClass( 'alert' );
					}

					// Description
					$( '.count .number', $( 'div.wp-simple-seo-preview span[data-preview="description"]' ).parent() ).text( response.data.description.length );
					if ( response.data.description.length > wp_simple_seo_preview.max_meta_description_length ) {
						$( 'div.wp-simple-seo-preview span[data-preview="description"]' ).html( response.data.description.substr( 0, wp_simple_seo_preview.max_meta_description_length ) + '...' );
						$( '.count', $( 'div.wp-simple-seo-preview span[data-preview="description"]' ).parent() ).addClass( 'alert' );
					} else {
						$( 'div.wp-simple-seo-preview span[data-preview="description"]' ).html( response.data.description );
						$( '.count', $( 'div.wp-simple-seo-preview span[data-preview="description"]' ).parent() ).removeClass( 'alert' );
					}

					// If a Permalink now exists, use that instead of the ?p=ID shortlink
					var wp_simple_seo_preview_permalink = $( 'span#sample-permalink a' ).text();
					if ( wp_simple_seo_preview_permalink.length > 0 ) {
						$( 'div.wp-simple-seo-preview span[data-preview="url"]' ).html( wp_simple_seo_preview_permalink );
					}

					// Hide the spinner
					$( 'span[data-preview="spinner"]' ).css( 'visibility', 'hidden' );
				}

				// Reset the flag
				wp_simple_seo_updating_preview = false;
				
            }
        );

	}

	/**
	 * Post:
	 *
	 * Events which should fire the preview function (e.g. change of input)
	 *
	 * See includes/admin/post.php for TinyMCE events that are registered.
	 */
	$( 'form#post' ).on( 'change', 'input,select,textarea', function( e ) {

		wp_simple_seo_update_preview( 'post', jQuery( 'form#post' ) );

	} );
	$( 'form#post' ).on( 'click', 'input[type=submit]', function( e ) {

		wp_simple_seo_update_preview( 'post', jQuery( 'form#post' ) );

	} );

	/**
	 * Term:
	 *
	 * Events which should fire the preview function (e.g. change of input)
	 */
	$( 'form#edittag' ).on( 'change', 'input,select,textarea', function( e ) {

		wp_simple_seo_update_preview( 'term', jQuery( 'form#edittag' ) );

	} );

} );