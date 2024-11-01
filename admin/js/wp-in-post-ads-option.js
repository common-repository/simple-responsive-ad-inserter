(function( $ ) {
	'use strict';

	$(function() {

		$(document).on('click', '.nav-tab', function(e){
			e.preventDefault();
			var $this = $(this),
				target = $this.attr('href');
			if ( !$this.hasClass('nav-tab-active') ) {
				$('.nav-tab.nav-tab-active').removeClass('nav-tab-active');
				$('.tab-content.active').removeClass('active');
				
				$this.addClass('nav-tab-active');
				target.replace('#', '');
				$(target).addClass('active');
			}
		});

		// Checkbox toggles
		function wpipaCheckboxToggles( el ) {
			var $this = el,
				targetSelector = '[data-checkbox="'+$this.attr('id')+'"]';
			if ( $this.prop('checked') ) {
				$( targetSelector ).closest('.form-field').addClass('active').removeClass('hidden');
			} else {
				$( targetSelector ).closest('.form-field').addClass('hidden').removeClass('active');
			}
		}

		$('.wpipa-checkbox-toggle').each(function() {
			wpipaCheckboxToggles( $(this) );
		});
		
		$(document).on('change', '.wpipa-checkbox-toggle', function(e) {
			wpipaCheckboxToggles( $(this) );
		});

		// Select which shows/hides options based on its value
		function wpipaShowHideChildOptions( el ) {
			var $this = $(el),
				tempValue = $this.val(),
				targetSelector = '[data-parent-select-id="'+$this.attr('id')+'"]',
				activeSelector = '[data-parent-select-value="'+tempValue+'"]';

			$( targetSelector ).closest('.form-field').removeClass('wpipa-active');

			if ( tempValue && activeSelector ) {

				$( activeSelector ).closest('.form-field').addClass('wpipa-active');
			}
		}

		$('select.wpipa-has-child-opt').each(function() {
			wpipaShowHideChildOptions( $(this) );
		});

		$(document).on('change', 'select.wpipa-has-child-opt', function(e) {
			wpipaShowHideChildOptions( $(this) );
		});

		function wpipaShowHideInsideChildOptions( el ) {
			var $this = $(el),
				tempValue = $this.val(),
				targetSelector = '[data-parent-inside-select-id="'+$this.attr('id')+'"]',
				activeSelector = '[data-parent-inside-select-value="'+tempValue+'"]';

			$( targetSelector ).closest('.form-field').addClass('hidden').removeClass('active');

			if ( tempValue && activeSelector ) {

				$( activeSelector ).closest('.form-field').addClass('active').removeClass('hidden');
			}
		}

		$('select.wpipa-has-child-opt-inside').each(function() {
			wpipaShowHideInsideChildOptions( $(this) );
		});

		$(document).on('change', 'select.wpipa-has-child-opt-inside', function(e) {
			wpipaShowHideInsideChildOptions( $(this) );
		});

		// Color Picker
		$('.wpipa-color-picker').wpColorPicker();
	});

	//$( window ).load(function() {
	//});

})( jQuery );