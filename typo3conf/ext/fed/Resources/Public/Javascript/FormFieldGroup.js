(function(jQuery){
	jQuery.fn.formFieldGroup = function(options) {
		var defaults = {
			amount : 1,
			maximum: 5
		};
		var options = jQuery.extend(defaults, options);

			// main loop - intialize all selected elements
		return this.each(function() {
			var element = jQuery(this);
			var selector = element.find('.form-field-group-count');
			selector.change(function() {
				var children = element.find('.form-field-group-container');
				var requestedAmount = parseInt(jQuery(this).val());
				if (requestedAmount > children.length) {
						// add groups from buffer
					var toBeAdded = requestedAmount - children.length;
					while (toBeAdded > 0) {
						var newContent = jQuery(options.buffer.shift()).not('.form-field-group-exclude');
						element.append(newContent);
						toBeAdded--;
					};
				} else if (requestedAmount < children.length) {
						// substract groups from DOM in reverse order, prepend to buffer
					var toBeRemoved = children.length - requestedAmount;
					while (toBeRemoved > 0) {
						var removedContent = children[children.length - toBeRemoved];
						removedContent.remove();
						options.buffer.push(removedContent);
						toBeRemoved--;
					};
				};
			});
		});
	};
})(jQuery);
