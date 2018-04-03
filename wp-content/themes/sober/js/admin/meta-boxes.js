jQuery( document ).ready( function ( $ ) {
	"use strict";

	// Show/hide settings for post format when choose post format
	var $format = $( '#post-formats-select' ).find( 'input.post-format' ),
		$formatBox = $( '#post-format-settings' );

	$format.on( 'change', function () {
		var type = $format.filter( ':checked' ).val();

		$formatBox.hide();
		if ( $formatBox.find( '.rwmb-field' ).hasClass( type ) ) {
			$formatBox.show();
		}

		$formatBox.find( '.rwmb-field' ).slideUp();
		$formatBox.find( '.' + type ).slideDown();
	} );
	$format.filter( ':checked' ).trigger( 'change' );

	// Show/hide settings for custom layout settings
	$( '#custom_layout' ).on( 'change', function () {
		if ( $( this ).is( ':checked' ) ) {
			$( '.rwmb-field.custom-layout' ).slideDown();
		}
		else {
			$( '.rwmb-field.custom-layout' ).slideUp();
		}
	} ).trigger( 'change' );

	// Toggle "Display Settings" for page template
	$( '#page_template' ).on( 'change', function () {
		if ( $( this ).val() == 'templates/homepage.php' ) {
			$( '#display-settings' ).slideUp();
		} else {
			$( '#display-settings' ).slideDown();
		}
	} ).trigger( 'change' );

	// Toggle page header fields
	$( '#hide_page_header' ).on( 'change', function () {
		var $el = $( this );

		if ( $el.is( ':checked' ) ) {
			$( '.rwmb-field.page-header-field' ).slideUp();
			$( '.rwmb-field.hide-page-title' ).slideDown();
		} else {
			$( '.rwmb-field.page-header-field' ).slideDown();
			$( '.rwmb-field.hide-page-title' ).slideUp();
		}
	} ).trigger( 'change' );

	// Toggle header fields
	$( '#site_header_bg' ).on( 'change', function () {
		var $el = $( this );

		if ( 'white' == $el.val() ) {
			$( '.rwmb-field.site_header_text_color' ).slideUp();
		} else {
			$( '.rwmb-field.site_header_text_color' ).slideDown();
		}
	} ).trigger( 'change' );

	// Toggle spacing fields
	$( '#top_spacing, #bottom_spacing' ).on( 'change', function() {
		var $el = $( this );

		if ( 'custom' === $el.val() ) {
			$el.closest( '.rwmb-field' ).next( '.custom-spacing' ).slideDown();
		} else {
			$el.closest( '.rwmb-field' ).next( '.custom-spacing' ).slideUp();
		}
	} ).trigger( 'change' );
} );
