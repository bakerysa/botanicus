var smm = smm || {};

(function ( $, _ ) {
	'use strict';

	var wp = window.wp;

	smm = {
		init: function () {
			this.$body = $( 'body' );
			this.$modal = $( '#smm-settings' );
			this.itemData = {};
			this.templates = {};

			this.frame = wp.media( {
				library: {
					type: 'image'
				}
			} );

			this.initTemplates();
			this.initActions();
		},

		initTemplates: function () {
			_.each( smmModals, function ( name ) {
				smm.templates[name] = wp.template( 'sober-' + name );
			} );
		},

		initActions: function () {
			smm.$body
				.on( 'click', '.opensettings', this.openModal )
				.on( 'click', '.smm-modal-backdrop, .smm-modal-close, .smm-button-cancel', this.closeModal );

			smm.$modal
				.on( 'click', '.smm-menu a', this.switchPanel )
				.on( 'click', '.smm-column-handle', this.resizeMegaColumn )
				.on( 'click', '.smm-button-save', this.saveChanges );
		},

		openModal: function () {
			smm.getItemData( this );

			smm.$modal.show();
			smm.$body.addClass( 'modal-open' );
			smm.render();

			return false;
		},

		closeModal: function () {
			smm.$modal.hide().find( '.smm-content' ).html( '' );
			smm.$body.removeClass( 'modal-open' );
			return false;
		},

		switchPanel: function ( e ) {
			e.preventDefault();

			var $el = $( this ),
				panel = $el.data( 'panel' );

			$el.addClass( 'active' ).siblings( '.active' ).removeClass( 'active' );
			smm.openSettings( panel );
		},

		render: function () {
			// Render menu
			smm.$modal.find( '.smm-frame-menu .smm-menu' ).html( smm.templates.menus( smm.itemData ) );

			var $activeMenu = smm.$modal.find( '.smm-menu a.active' );

			// Render content
			this.openSettings( $activeMenu.data( 'panel' ) );
		},

		openSettings: function ( panel ) {
			var $content = smm.$modal.find( '.smm-frame-content .smm-content' ),
				$panel = $content.children( '#smm-panel-' + panel );

			if ( $panel.length ) {
				$panel.addClass( 'active' ).siblings().removeClass( 'active' );
			} else {
				$content.append( smm.templates[panel]( smm.itemData ) );
				$content.children( '#smm-panel-' + panel ).addClass( 'active' ).siblings().removeClass( 'active' );

				if ( 'mega' == panel ) {
					smm.initMegaColumns();
				}
				if ( 'background' == panel ) {
					smm.initBackgroundFields();
				}
				if ( 'icon' == panel ) {
					smm.initIconFields();
				}
			}

			// Render title
			var title = smm.$modal.find( '.smm-frame-menu .smm-menu a[data-panel=' + panel + ']' ).data( 'title' );
			smm.$modal.find( '.smm-frame-title' ).html( smm.templates.title( {title: title} ) );
		},

		resizeMegaColumn: function ( e ) {
			e.preventDefault();

			var $el = $( e.currentTarget ),
				$column = $el.closest( '.smm-submenu-column' ),
				currentWidth = $column.data( 'width' ),
				widthData = smm.getWidthData( currentWidth ),
				nextWidth;

			if ( ! widthData ) {
				return;
			}

			if ( $el.hasClass( 'smm-resizable-w' ) ) {
				nextWidth = widthData.increase ? widthData.increase : widthData;
			} else {
				nextWidth = widthData.decrease ? widthData.decrease : widthData;
			}

			$column[0].style.width = nextWidth.width;
			$column.data( 'width', nextWidth.width );
			$column.find( '.smm-column-width-label' ).text( nextWidth.label );
			$column.find( '.menu-item-depth-0 .menu-item-width' ).val( nextWidth.width );
		},

		getWidthData: function( width ) {
			var steps = [
				{width: '25.00%', label: '1/4'},
				{width: '33.33%', label: '1/3'},
				{width: '50.00%', label: '1/2'},
				{width: '66.66%', label: '2/3'},
				{width: '75.00%', label: '3/4'},
				{width: '100.00%', label: '1/1'}
			];

			var index = _.findIndex( steps, function( data ) { return data.width == width; } );

			if ( index === 'undefined' ) {
				return false;
			}

			var data = {
				index: index,
				width: steps[index].width,
				label: steps[index].label
			};

			if ( index > 0 ) {
				data.decrease = {
					index: index - 1,
					width: steps[index - 1].width,
					label: steps[index - 1].label
				};
			}

			if ( index < steps.length - 1 ) {
				data.increase = {
					index: index + 1,
					width: steps[index + 1].width,
					label: steps[index + 1].label
				};
			}

			return data;
		},

		initMegaColumns: function () {
			var $columns = smm.$modal.find( '#smm-panel-mega .smm-submenu-column' ),
				defaultWidth = '25.00%';

			if ( !$columns.length ) {
				return;
			}

			// Support maximum 4 columns
			if ( $columns.length < 4 ) {
				defaultWidth = String( ( 100 / $columns.length ).toFixed( 2 ) ) + '%';
			}

			_.each( $columns, function ( column ) {
				var $column = $( column ),
					width = column.dataset.width;

				width = width || defaultWidth;

				var widthData = smm.getWidthData( width );

				column.style.width = widthData.width;
				column.dataset.width = widthData.width;
				$( column ).find( '.menu-item-depth-0 .menu-item-width' ).val( width );
				$( column ).find( '.smm-column-width-label' ).text( widthData.label );
			} );
		},

		initBackgroundFields: function () {
			smm.$modal.find( '.background-color-picker' ).wpColorPicker();

			// Background image
			smm.$modal.on( 'click', '.background-image .upload-button', function ( e ) {
				e.preventDefault();

				var $el = $( this );

				// Remove all attached 'select' event
				smm.frame.off( 'select' );

				// Update inputs when select image
				smm.frame.on( 'select', function () {
					// Update input value for single image selection
					var url = smm.frame.state().get( 'selection' ).first().toJSON().url;

					$el.siblings( '.background-image-preview' ).html( '<img src="' + url + '">' );
					$el.siblings( 'input' ).val( url );
					$el.siblings( '.remove-button' ).removeClass( 'hidden' );
				} );

				smm.frame.open();
			} ).on( 'click', '.background-image .remove-button', function ( e ) {
				e.preventDefault();

				var $el = $( this );

				$el.siblings( '.background-image-preview' ).html( '' );
				$el.siblings( 'input' ).val( '' );
				$el.addClass( 'hidden' );
			} );

			// Background position
			smm.$modal.on( 'change', '.background-position select', function () {
				var $el = $( this );

				if ( 'custom' == $el.val() ) {
					$el.next( 'input' ).removeClass( 'hidden' );
				} else {
					$el.next( 'input' ).addClass( 'hidden' );
				}
			} );
		},

		initIconFields: function () {
			var $input = smm.$modal.find( '#smm-icon-input' ),
				$preview = smm.$modal.find( '#smm-selected-icon' ),
				$icons = smm.$modal.find( '.smm-icon-selector .icons i' );

			smm.$modal.on( 'click', '.smm-icon-selector .icons i', function () {
				var $el = $( this ),
					icon = $el.data( 'icon' );

				$el.addClass( 'active' ).siblings( '.active' ).removeClass( 'active' );

				$input.val( icon );
				$preview.html( '<i class="' + icon + '"></i>' );
			} );

			$preview.on( 'click', 'i', function () {
				$( this ).remove();
				$input.val( '' );
			} );

			smm.$modal.on( 'keyup', '.smm-icon-search', function () {
				var term = $( this ).val().toUpperCase();

				if ( !term ) {
					$icons.show();
				} else {
					$icons.hide().filter( function () {
						return $( this ).data( 'icon' ).toUpperCase().indexOf( term ) > -1;
					} ).show();
				}
			} );
		},

		getItemData: function ( menuItem ) {
			var $menuItem = $( menuItem ).closest( 'li.menu-item' ),
				$menuData = $menuItem.find( '.mega-data' ),
				children = $menuItem.childMenuItems(),
				megaData = $menuData.data( 'mega' );

			megaData.content = $menuData.html();

			smm.itemData = {
				depth   : $menuItem.menuItemDepth(),
				megaData: megaData,
				data    : $menuItem.getItemData(),
				children: [],
				element : $menuItem.get( 0 )
			};

			if ( !_.isEmpty( children ) ) {
				_.each( children, function ( item ) {
					var $item = $( item ),
						$itemData = $item.find( '.mega-data' ),
						depth = $item.menuItemDepth(),
						megaData = $itemData.data( 'mega' );

					megaData.content = $itemData.html();

					smm.itemData.children.push( {
						depth   : depth,
						subDepth: depth - smm.itemData.depth - 1,
						data    : $item.getItemData(),
						megaData: megaData,
						element : item
					} );
				} );
			}
		},

		setItemData: function ( item, data ) {
			var $dataHolder = $( item ).find( '.mega-data' );

			if ( _.has( data, 'content' ) ) {
				$dataHolder.html( data.content );
				delete data.content;
			}

			$dataHolder.data( 'mega', data );
		},

		getFieldName: function ( name, id ) {
			name = name.split( '.' );
			name = '[' + name.join( '][' ) + ']';

			return 'menu-item-mega[' + id + ']' + name;
		},

		saveChanges: function () {
			var $inputs = smm.$modal.find( '.smm-content :input' ),
				$spinner = smm.$modal.find( '.smm-toolbar .spinner' );

			$inputs.each( function() {
				var $input = $( this );

				if ( $input.is( ':checkbox' ) && $input.is( ':not(:checked)' ) ) {
					$input.attr( 'value', '0' ).prop( 'checked', true );
				}
			} );

			var data = $inputs.serialize();

			$inputs.filter( '[value="0"]' ).prop( 'checked', false );

			$spinner.addClass( 'is-active' );
			$.post( ajaxurl, {
				action: 'sober_save_menu_item_data',
				data  : data
			}, function ( res ) {
				if ( !res.success ) {
					return;
				}

				var data = res.data['menu-item-mega'];

				// Update parent menu item
				if ( _.has( data, smm.itemData.data['menu-item-db-id'] ) ) {
					smm.setItemData( smm.itemData.element, data[smm.itemData.data['menu-item-db-id']] );
				}

				_.each( smm.itemData.children, function ( menuItem ) {
					if ( !_.has( data, menuItem.data['menu-item-db-id'] ) ) {
						return;
					}

					smm.setItemData( menuItem.element, data[menuItem.data['menu-item-db-id']] );
				} );

				$spinner.removeClass( 'is-active' );
				smm.closeModal();
			} );
		}
	};

	$( function () {
		smm.init();
	} );
})( jQuery, _ );
