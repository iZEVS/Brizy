var l10n = wp.media.view.l10n;
wp.media.view.MediaFrame.Select.prototype.browseRouter = function ( routerView ) {
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

jQuery( document ).ready( function ( $ ) {

	if ( ! wp.media ) {
		return;
	}

	wp.media.view.Modal.prototype.on( 'open', function () {
		if ( $( 'body' ).find( '.media-modal-content .media-router a.media-menu-item.active' ).text() === 'Unsplash' ) {
			getImgs( 'test', 1 );
		}
	} );

	$( wp.media ).on( 'click', '.media-router a.media-menu-item', function ( e ) {
		if ( e.target.innerText === 'Unsplash' ) {
			getImgs( 'test', 1 );
		}
	} );
} );

$( document ).on( 'click', '.brz-imgs li', function() {
	$( '.brz-imgs li.selected' ).removeClass( 'selected' ).removeClass( 'details' );
	$( this ).addClass( 'selected details' );
	$( this ).closest( '.media-modal-content' ).find( '.media-toolbar .media-button-select' ).removeAttr( 'disabled' );
} );


function getImgs( query, page ) {

	$( '.attachments-browser' ).find( '.spinner' ).show();

	$.ajax( {
		type: "POST",
		url: BrizyUnsplash.ajaxUrl,
		data: {
			action: BrizyUnsplash.actionGetImgs,
			nonce: BrizyUnsplash.nonce,
			page: page,
			query: query
		}
	} )
	 .done( function ( reply ) {
	 	if ( ! reply.success ) {
	 		return;
	    }

		 insertImgs( reply.data );

		 $( '.attachments-browser' ).find( '.spinner' ).hide();
	 } )
	 .fail( function ( jqXHR, textStatus ) {

	 } );
}

function insertImgs( imgs ) {
	var html       = '<ul class="attachments ui-sortable ui-sortable-disabled brz-imgs">',
		containter = $( 'body .media-modal-content .media-frame-content' );

	for ( var i = 0; i < imgs.length; i++ ) {
		html +=
			'<li class="attachment save-ready" data-id="' + imgs[i].id + '">' +
		        '<div class="attachment-preview js--select-attachment type-image subtype-jpeg landscape">' +
					'<div class="thumbnail">' +
						'<div class="centered">' +
							'<img src="' + imgs[i].src + '">' +
						'</div>' +
					'</div>' +
				'</div>' +
				'<button type="button" class="check" tabindex="0">' +
					'<span class="media-modal-icon"></span><span class="screen-reader-text">Deselect</span>' +
				'</button>' +
			'</li>';
	}

	html += '</ul>';

	var mediaToolbar =
		'<div class="media-toolbar">' +
			'<div class="media-toolbar-secondary">' +
				'<span class="spinner"></span>' +
			'</div>' +
			'<div class="media-toolbar-primary search-form">' +
				'<label for="media-search-input" class="screen-reader-text">Search Media</label>' +
				'<input type="search" placeholder="Search media items..." id="media-search-input" class="search">' +
			'</div>' +
		'</div>';

	containter.html( '<div class="attachments-browser">' + mediaToolbar + html + '</div>' );
}