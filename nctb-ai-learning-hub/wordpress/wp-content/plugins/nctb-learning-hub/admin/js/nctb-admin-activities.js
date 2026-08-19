/* NCTB Lesson Activities Admin UI */
(function($) {
	'use strict';

	$(document).ready(function() {
		var $container = $('#nctb-activities-list');
		if (!$container.length) {
			return;
		}

		// Re-index all cards in DOM order
		function reindexCards() {
			$container.find('.nctb-activity-card').each(function(index) {
				var $card = $(this);
				$card.find('.nctb-act-index').text(index + 1);
				$card.find('[name^="nctb_activities"]').each(function() {
					var name = $(this).attr('name');
					if (name) {
						var updated = name.replace(/nctb_activities\[\d+\]/, 'nctb_activities[' + index + ']');
						$(this).attr('name', updated);
					}
				});
			});
		}

		// Update title preview on typing
		$container.on('input', '.nctb-input-title', function() {
			var val = $(this).val() || 'Untitled Activity';
			$(this).closest('.nctb-activity-card').find('.nctb-act-title-preview').text(val);
		});

		// Toggle expand / collapse
		$container.on('click', '.nctb-activity-head', function(e) {
			if ($(e.target).closest('.nctb-act-controls').length) {
				return;
			}
			$(this).closest('.nctb-activity-card').find('.nctb-activity-body').slideToggle(150);
		});

		// Move up
		$container.on('click', '.btn-move-up', function(e) {
			e.preventDefault();
			var $card = $(this).closest('.nctb-activity-card');
			var $prev = $card.prev('.nctb-activity-card');
			if ($prev.length) {
				$card.insertBefore($prev);
				reindexCards();
			}
		});

		// Move down
		$container.on('click', '.btn-move-down', function(e) {
			e.preventDefault();
			var $card = $(this).closest('.nctb-activity-card');
			var $next = $card.next('.nctb-activity-card');
			if ($next.length) {
				$card.insertAfter($next);
				reindexCards();
			}
		});

		// Delete card
		$container.on('click', '.btn-delete-card', function(e) {
			e.preventDefault();
			if (confirm('Are you sure you want to remove this activity block?')) {
				$(this).closest('.nctb-activity-card').remove();
				reindexCards();
			}
		});

		// Add new block
		$('#btn-add-activity').on('click', function(e) {
			e.preventDefault();
			var selectedType = $('#nctb-new-activity-type').val();
			var typeText = $('#nctb-new-activity-type option:selected').text();
			var index = $container.find('.nctb-activity-card').length;

			var template = $('#nctb-activity-template').html();
			if (!template) return;

			var html = template
				.replace(/{{INDEX}}/g, index)
				.replace(/{{TYPE}}/g, selectedType)
				.replace(/{{TYPE_LABEL}}/g, typeText)
				.replace(/{{TITLE}}/g, typeText)
				.replace(/{{CONTENT}}/g, '');

			var $newCard = $(html);
			$newCard.find('select.nctb-select-type').val(selectedType);
			$container.append($newCard);
			reindexCards();
			$newCard.find('.nctb-activity-body').show();
		});

		// Expand / Collapse all
		$('#btn-toggle-all-activities').on('click', function(e) {
			e.preventDefault();
			var $bodies = $container.find('.nctb-activity-body');
			if ($bodies.filter(':visible').length > 0) {
				$bodies.slideUp(150);
			} else {
				$bodies.slideDown(150);
			}
		});
	});
})(jQuery);
