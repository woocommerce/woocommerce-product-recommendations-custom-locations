;( function( $, window, document, navigator ) {

	$( function() {

		// Shortcode metabox.
		var $shortcode_metabox = $( '#wc-prl-cl-shortcode' );
		if ( $shortcode_metabox.length ) {
			var shortcode = $shortcode_metabox.find( 'code' ).text(),
				$button   = $shortcode_metabox.find( 'a.copy' );

			$button.on( 'click', function( e ) {
				e.preventDefault();
				copy_to_clipboard( shortcode );
			} );
		}

		// Shortcodes in List table.
		var $list_table = $( 'body.post-type-prl_hook .wp-list-table' );
		if ( $list_table.length ) {
			var $shortcode_buttons = $list_table.find( '.wc-action-button-shortcode' );
			$shortcode_buttons.on( 'click', function( e ) {
				e.preventDefault();
				var shortcode = $( this ).data( 'shortcode' );
				copy_to_clipboard( shortcode );
			} );
		}

	} );


	function fallback_copy_to_clipboard( text ) {

		var textArea   = document.createElement( 'textarea' );
		textArea.value = text;

		// Avoid scrolling to bottom
		textArea.style.top      = '0';
		textArea.style.left     = '0';
		textArea.style.position = 'fixed';

		document.body.appendChild( textArea );
		textArea.focus();
		textArea.select();

		try {
			document.execCommand( 'copy' );
		} catch (err) {
			console.error( 'Fallback: Oops, unable to copy', err );
		}

		document.body.removeChild( textArea );
	}

	function copy_to_clipboard( text ) {

		if ( ! navigator.clipboard ) {
			fallback_copy_to_clipboard( text );
			return;
		}

		navigator.clipboard.writeText( text ).then( function() {
			alert( 'Copied' );
		}, function( err ) {
			console.error( 'Async: Could not copy text', err );
		} );
	}

} )( jQuery, window, document, navigator );
