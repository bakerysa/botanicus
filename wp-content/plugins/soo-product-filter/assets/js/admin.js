jQuery( document ).ready( function( $ ) {
	'use strict';

	var wp = window.wp,
		data = window.sooFilter,
		$body = $( 'body' ),
		template = wp.template( 'soopf-filter' );

	$body.on( 'click', '.soopf-add-new', function( e ) {
		e.preventDefault();

		var $this = $( this ),
			$filters = $this.parent().prev( '.soopf-filters' ),
			$title = $filters.closest( '.widget-content' ).find( 'input' ).first();

		data.number = $this.data( 'number' );
		data.name = $this.data( 'name' );
		data.count = $this.data( 'count' );

		$this.data( 'count', data.count + 1 );
		$filters.append( template( data ) );
		$filters.trigger( 'appended' );
		$title.trigger( 'change' ); // Support customize preview
	} );

	$body.on( 'change', '.soopf-filter-fields select.filter-by', function() {
		var $this = $( this ),
			source = $this.val(),
			template = wp.template( 'soopf-options' );

		$this.closest( '.source' ).next( '.display' ).find( 'select.display-type' ).html( template( { options: data.display[source] } ) );

		if ( 'attribute' == source ) {
			$this.next( 'select' ).removeClass( 'hidden' );
			$this.closest( '.source' ).next( '.display' ).find( 'select:last-child' ).removeClass( 'hidden' );
		} else {
			$this.next( 'select' ).addClass( 'hidden' );
			$this.closest( '.source' ).next( '.display' ).find( 'select:last-child' ).addClass( 'hidden' );
		}
	} );

	$body.on( 'change', '.soopf-filter-fields select.display-type', function() {
		var $this = $( this ),
			display = $this.val(),
			source = $this.closest( '.display' ).prev( '.source' ).find( 'select.filter-by' ).val();

		if ( 'attribute' != source || 'dropdown' == display ) {
			$this.next( 'select' ).addClass( 'hidden' );
		} else {
			$this.next( 'select' ).removeClass( 'hidden' );
		}
	} );

	$body.on( 'click', '.remove-filter', function( e ) {
		e.preventDefault();
		var $filters = $( this ).closest( '.soopf-filters' ),
			$title = $filters.closest( '.widget-content' ).find( 'input' ).first();

		$( this ).closest( '.soopf-filter-fields' ).slideUp( 300, function () {
			$( this ).remove();
			$filters.trigger( 'truncated' );
			$title.trigger( 'change' ); // Support customize preview
		} );
	} );

	$body.on( 'appended truncated', '.soopf-filters', function() {
		var $filters = $( this ).children( '.soopf-filter-fields' );

		if ( $filters.length ) {
			$( this ).children( '.no-filter' ).addClass( 'hidden' );
		} else {
			$( this ).children( '.no-filter' ).removeClass( 'hidden' );
		}
	} );
} );