/*global soberTermData */

jQuery( document ).ready( function ( $ ) {
	'use strict';

	// Only show the "remove image" button when needed
	if ( $( '#product_cat_image_id' ).val() == 0 ) {
		$( '.remove_header_image_button' ).hide();
	}

	// Uploading files
	var file_frame;

	$( document.body ).on( 'click', '.upload_header_image_button', function ( event ) {

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			file_frame.open();
			return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.downloadable_file = wp.media( {
			title   : soberTermData.l10n.title,
			button  : {
				text: soberTermData.l10n.button
			},
			multiple: false
		} );

		// When an image is selected, run a callback.
		file_frame.on( 'select', function () {
			var attachment = file_frame.state().get( 'selection' ).first().toJSON();

			$( '#product_cat_image_id' ).val( attachment.id );
			$( '#product_cat_page_header' ).find( 'img' ).attr( 'src', attachment.sizes.thumbnail.url );
			$( '.remove_header_image_button' ).show();
		} );

		// Finally, open the modal.
		file_frame.open();
	} );

	$( document.body ).on( 'click', '.remove_header_image_button', function ( event ) {
		event.preventDefault();

		$( '#product_cat_image_id' ).val( '' );
		$( '#product_cat_page_header' ).find( 'img' ).attr( 'src', soberTermData.placeholder );
		$( '.remove_header_image_button' ).hide();
	} );

	$( document ).ajaxComplete( function ( event, request, options ) {
		if ( request && 4 === request.readyState && 200 === request.status
			&& options.data && 0 <= options.data.indexOf( 'action=add-tag' ) ) {

			var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response' );
			if ( !res || res.errors ) {
				return;
			}

			// Clear Thumbnail fields on submit
			$( '#product_cat_image_id' ).val( '' );
			$( '#product_cat_page_header' ).find( 'img' ).attr( 'src', soberTermData.placeholder );
			$( '.remove_header_image_button' ).hide();

			return;
		}
	} );
} );