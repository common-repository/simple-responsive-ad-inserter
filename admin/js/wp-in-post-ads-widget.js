(function( $ ) {

	'use strict';

	$(function() {

		function wpipaProcessPostSelectDataForSelect2( ajaxData, page, query ) {

			var items=[];

			for (var thisId in ajaxData) {
				var newItem = {
					'id': ajaxData[thisId]['id'],
					'text': ajaxData[thisId]['title']
				};
				items.push(newItem);
			}
			return { results: items };
		}

		$('#widgets-right input.wpipa-multi-select').each(function() {

			var $this = $(this);

			$this.select2( {
				placeholder: 'Enter Ad Title',
				multiple: true,
				minimumInputLength: 2,
				ajax: {
					url: ajaxurl,
					dataType: 'json',
					data: function (term, page) {
						return {
							q: term,
							action: 'wpipa_get_ads',
						};
					},
					results: wpipaProcessPostSelectDataForSelect2
				},
				initSelection: function(element, callback) {

					var ids=$(element).val();
					if ( ids !== "" ) {
						$.ajax({
							url: ajaxurl,
							dataType: "json",
							data: {
								action: 'get_post_titles',
								post_ids: ids
							},
							
						}).done(function(response) {
							
							var processedData = wpipaProcessPostSelectDataForSelect2( response );
							callback( processedData.results );
						});
					}
				},
			});
		});

		$(document).on('widget-updated widget-added', function(e) {

			$('#widgets-right input.wpipa-multi-select').each(function() {

				var $this = $(this);

				$this.select2( {
					placeholder: 'Enter Ad Title',
					multiple: true,
					minimumInputLength: 2,
					ajax: {
						url: ajaxurl,
						dataType: 'json',
						data: function (term, page) {
							return {
								q: term,
								action: 'wpipa_get_ads',
							};
						},
						results: wpipaProcessPostSelectDataForSelect2
					},
					initSelection: function(element, callback) {

						var ids=$(element).val();
						console.log(ids);
						if ( ids !== "" ) {
							$.ajax({
								url: ajaxurl,
								dataType: "json",
								data: {
									action: 'get_post_titles',
									post_ids: ids
								},
								
							}).done(function(response) {
								
								var processedData = wpipaProcessPostSelectDataForSelect2( response );
								callback( processedData.results );
							});
						}
					},
				});
			});
		});
	});

})( jQuery );