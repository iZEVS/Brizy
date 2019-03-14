(function ( $, Brizy, mediaView, l10n ) {

	var Unsplash = {
		mediaFrameContent: function() {
			return $( 'body .media-modal-content .media-frame-content' );
		},

		getToolbar: function() {

			return '<div class="media-toolbar">' +
				       '<div class="media-toolbar-secondary">' +
				            '<span class="spinner"></span>' +
				       '</div>' +
				       '<div class="media-toolbar-primary search-form">' +
				            '<label for="media-search-input" class="screen-reader-text">' + l10n.searchMediaLabel + '</label>' +
				            '<input type="search" placeholder="' + l10n.searchMediaPlaceholder  +'" class="search brz-unsplash-search">' +
				       '</div>' +
			       '</div>';
		},

		getImgs: function( query, page, append ) {

			$( '.attachments-browser' ).find( '.spinner' ).show();

			var self = this;

			$.ajax( {
				type: "POST",
				url: Brizy.ajaxUrl,
				data: {
					action: Brizy.actionGetImgs,
					nonce: Brizy.nonce,
					page: page,
					query: query
				}

			} ).done( function ( reply ) {

				 if ( ! reply.success ) {
					 return;
				 }

				 self.insertItems( reply.data, append );

				 $( '.attachments-browser' ).find( '.spinner' ).hide();
			} );
		},

		getItemsHtml: function( items ) {
			var out = '';

			for ( var i = 0; i < items.length; i++ ) {
				out +=
					'<li class="attachment save-ready" data-id="' + items[i].id + '" data-unsplash-src="' + items[i].src + '">' +
						'<div class="attachment-preview js--select-attachment type-image subtype-jpeg landscape">' +
							'<div class="thumbnail">' +
								'<div class="centered">' +
									'<img src="' + items[i].src + '">' +
								'</div>' +
							'</div>' +
						'</div>' +
						'<button type="button" class="check" tabindex="0">' +
							'<span class="media-modal-icon"></span><span class="screen-reader-text">Deselect</span>' +
						'</button>' +
					'</li>';
			}

			return out;
		},

		insertItems: function( items, append ) {
			var itemsHtml = this.getItemsHtml( items ),
				imgsContainer = $( '.brz-imgs' );

			if ( imgsContainer.length === 0 ) {

				this.mediaFrameContent().attr( 'data-columns', 8 );

				this.mediaFrameContent().html(
					'<div class="attachments-browser hide-sidebar">' +
						this.getToolbar() +
						'<ul class="attachments ui-sortable ui-sortable-disabled brz-imgs" data-page="1" id="brz-imgs-wrapp">' +
							itemsHtml +
						'</ul>' +
					'</div>'
				);

			} else if ( append ) {
				imgsContainer.append( itemsHtml );
			} else {
				imgsContainer.html( itemsHtml );
			}
		},

		uploadImg: function( item ) {

			if ( item.length === 0 ) {
				return;
			}

			$.ajax( {
				type: "POST",
				url: Brizy.ajaxUrl,
				data: {
					action: Brizy.actionUploadImg,
					nonce: Brizy.nonce,
					id: item.attr( 'data-id' ),
					src: item.attr( 'data-unsplash-src' ),
				}

			} ).done( function ( reply ) {

				if ( ! reply.success ) {
					return;
				}

				$( '.attachments-browser' ).find( '.spinner' ).hide();
			} );
		},

		init: function() {
			var self = this;

			mediaView.Modal.prototype.on( 'open', function () {
				if ( $( 'body' ).find( '.media-modal-content .media-router a.media-menu-item.active' ).text() === 'Unsplash' ) {
					self.getImgs( 'test', 1 );
				}
			} );

			$( wp.media ).on( 'click', '.media-router a.media-menu-item', function ( e ) {
				if ( e.target.innerText === 'Unsplash' ) {
					self.getImgs( 'test', 1 );
				}
			} );

			$( document ).on( 'change keyup search', '.brz-unsplash-search', function() {
				console.log( $( this ).val() );
				self.getImgs( $( this ).val(), $( '.brz-imgs' ).attr( 'data-page' ) );
			} );

			document.addEventListener( 'scroll', function ( event ) {

				if ( event.target.id !== 'brz-imgs-wrapp' ) {
					return;
				}

				var item = $( event.target );

				if ( item.scrollTop() + item.innerHeight() + 10 < item[0].scrollHeight ) {
					return;
				}

				self.getImgs( $( '.brz-unsplash-search' ).val(), item.attr( 'data-page' ), true );

			}, true );

			$( document ).on( 'click', '.brz-imgs li', function() {
				$( '.brz-imgs li.selected' ).removeClass( 'selected' ).removeClass( 'details' );
				$( this ).addClass( 'selected details' );
				$( this ).closest( '.media-modal-content' ).find( '.media-toolbar .media-button-select' ).removeAttr( 'disabled' );
			} );

			$( document ).on( 'click', '.media-modal-content .media-toolbar .media-button-select', function() {
				self.uploadImg( $( '.media-modal-content .brz-imgs .selected' ) );
			} );
		},

	};

	mediaView.MediaFrame.Select.prototype.browseRouter = function ( routerView ) {
		routerView.set( {
			upload: {
				text: l10n.uploadFilesTitle,
				priority: 20
			},
			browse: {
				text: l10n.mediaLibraryTitle,
				priority: 40
			},
			unsplash: {
				text: 'Unsplash',
				priority: 60
			}
		} );
	};

	Unsplash.init();

} )( jQuery, BrizyUnsplash, wp.media.view, wp.media.view.l10n );
/*
// Refresh media frame
https://wordpress.stackexchange.com/questions/78230/trigger-refresh-for-new-media-manager-in-3-5
*/