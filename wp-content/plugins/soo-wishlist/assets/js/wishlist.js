jQuery( function ( $ ) {
	/**
	 * Check if a node is blocked for processing.
	 *
	 * @param {JQuery Object} $node
	 * @return {bool} True if the DOM Element is UI Blocked, false if not.
	 */
	var is_blocked = function ( $node ) {
		return $node.is( '.processing' ) || $node.parents( '.processing' ).length;
	};

	/**
	 * Block a node visually for processing.
	 *
	 * @param {JQuery Object} $node
	 */
	var block = function ( $node ) {
		if ( !is_blocked( $node ) ) {
			$node.addClass( 'processing' ).block( {
				message   : null,
				overlayCSS: {
					background: '#fff',
					opacity   : 0.6
				}
			} );
		}
	};

	/**
	 * Unblock a node after processing is complete.
	 *
	 * @param {JQuery Object} $node
	 */
	var unblock = function ( $node ) {
		$node.removeClass( 'processing' ).unblock();
	};

	var sooWishlist = {
		init: function () {
			this.data = soowData || {};

			$( document.body )
				.on( 'click', '.add-to-wishlist-button', this.add )
				.on( 'click', '.wishlist_table .remove', this.remove )
				.on( 'removed_from_wishlist', this.updateFragments );
		},

		/**
		 * Ajax add to wishlist
		 */
		add: function ( event ) {
			var $button = $( event.currentTarget );

			if ( $button.hasClass( 'added' ) ) {
				return true;
			}

			if ( !$button.data( 'product_id' ) ) {
				return true;
			}

			event.preventDefault();

			$button.addClass( 'loading' );

			$.post(
				sooWishlist.data.ajaxurl,
				{
					action    : 'soow_add_to_wishlist',
					product_id: $button.data( 'product_id' )
				},
				function ( response ) {
					if ( !response.success ) {
						return;
					}

					$button.removeClass( 'loading' ).addClass( 'added' );
					$button.attr( 'href', sooWishlist.data.wishlisturl );

					var fragments = response.data.fragments;

					if ( fragments ) {
						$.each( fragments, function ( element, content ) {
							$( element ).replaceWith( content );
						} );
					}

					$( document.body ).trigger( 'added_to_wishlist', [$button, fragments] );
				}
			);
		},

		/**
		 * Ajax remove product from cart
		 */
		remove: function ( event ) {
			event.preventDefault();

			var $button = $( event.currentTarget ),
				$list = $( 'div.soo-wishlist' ),
				productId = $button.data( 'product_id' );

			$button.addClass( 'loading' );
			block( $list );

			$.ajax( {
				type    : 'GET',
				url     : $button.attr( 'href' ),
				dataType: 'html',
				success : function ( response ) {
					var $html = $( response ),
						$newList = $( 'div.soo-wishlist', $html );

					if ( $newList.length ) {
						$list.replaceWith( $newList.get( 0 ) );

						$( document.body ).trigger( 'removed_from_wishlist', [productId] );
					}
				}
			} );
		},

		updateFragments: function ( event, productId ) {
			// Update product buttons
			$( '.add-to-wishlist-button[data-product_id="' + productId + '"]' ).removeClass( 'added' );

			$.post(
				sooWishlist.data.ajaxurl,
				{
					action: 'soow_update_fragments'
				},
				function ( response ) {
					if ( !response.success ) {
						return;
					}

					var fragments = response.data;

					if ( fragments ) {
						$.each( fragments, function ( element, content ) {
							$( element ).replaceWith( content );
						} );
					}

					$( document.body ).trigger( 'wishlist_fragments_updated', [fragments] );
				}
			);
		}
	};

	sooWishlist.init();
} );