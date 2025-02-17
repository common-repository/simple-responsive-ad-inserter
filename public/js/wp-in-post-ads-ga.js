/**
 * Plugin Name: Simple Ad Inserter
 * Author URI: http://plugin-boutique.com/simple-ad-inserter/
 */
(function( $ ) {
	'use strict';

	// Initialize GA
	if ( wpipaVars.ga_category && wpipaVars.ga_label ) {
		if ( "undefined" == typeof _gaq ) var _gaq = []; // _gaq Global setup
		$(".wpipa a").click(function () {
			_gaq.push(['_trackEvent', wpipaVars.ga_category, 'Click', wpipaVars.ga_label]);
			return true; // Propagate further up
		});
	}

})( jQuery );