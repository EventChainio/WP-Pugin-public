(function( $ ) {
	'use strict';

    /**
	 * Auto-select all when clicking on shortcode text input
     */
	$(document).on('click', 'input.eventchain_shortcode', function(){
        this.setSelectionRange(0, this.value.length);
	});

})( jQuery );
