if (typeof FED == 'undefined') {
	FED = {};
};

FED.FormValidator = {

	forms : null,

	fields : null,

	method : 'all',

	triggeredByField : null,

	validate : function() {
		var source = FED.FormValidator.triggeredByField = jQuery(this);
		var form = source.parents('form');
		var json = jQuery.parseJSON(form.attr('rel'));
		var dataSet = {};
		var collected = FED.FormValidator.getData(form, json);
		collected.action = json.action;
		dataSet[json.prefix] = {
			"data": collected
		};
		if (FED.FormValidator.method == 'all') {
			FED.FormValidator.fields.addClass('loading');
		} else if (FED.FormValidator.method == 'field') {
			FED.FormValidator.triggeredByField.addClass('loading');
		};
		var result = jQuery.ajax({
			"type": 'post',
			"url": json.link,
			"data": dataSet,
			"complete": function(response, status) {
				var result = response.responseText;
				var data = jQuery.parseJSON(result);
				if (result == '1') {
					if (json.autosubmit) {
						form.submit();
					} else {
						FED.FormValidator.triggeredByField.removeClass('f3-form-error').removeClass('loading');
						return true;
					};
				} else if (typeof data == 'object') {
					FED.FormValidator.triggeredByField.removeClass('f3-form-error').removeClass('loading');
					FED.FormValidator.highlightErrorFields(form, json, data);
				} else {
					FED.FormValidator.triggeredByField.removeClass('f3-form-error').removeClass('loading');
					//console.warn('Unsupported return type: ' + typeof data);
				};
			}
		});
	},

	highlightErrorFields : function(form, config, errors) {
		if (FED.FormValidator.method == 'all') {
			FED.FormValidator.cleanFields(form);
		};
		for (var objectName in errors) {
			var propertyErrors = errors[objectName];
			for (var propertyName in propertyErrors) {
				var fieldName = config.prefix + '[' + objectName + '][' + propertyName + ']';
				var field = jQuery('[name="' + fieldName + '"]');
				if (FED.FormValidator.method == 'all') {
					if (field) {
						field.addClass('f3-form-error');
					};
				} else if (FED.FormValidator.method == 'field') {
					if (field && field.attr('name') == FED.FormValidator.triggeredByField.attr('name')) {
						field.addClass('f3-form-error').removeClass('loading');
					};
				};
			};
		};
	},

	getData : function(form, config) {
		var fieldName =	config.prefix + '[__hmac]';
		var hmac = form.find('[name="' + fieldName + '"]').val();
		var serialized = hmac.substring(-40);
		var unserialized = unserialize(serialized);
		var path = [];
		var data = this.getObjectData(form, unserialized, config.prefix, path);
		return data;
	},

	getObjectData : function(form, node, prefix, path, lastProperty) {
		var dataSet = {};
		var pathSet = [];
		for (var property in node) {
			path.push(property);
			if (typeof node[property] == 'object' || typeof node[property] == 'array') {
				dataSet[property] = this.getObjectData(form, node[property], prefix, path, property);
			} else {
				var value = this.getFieldValueByPath(prefix, path);
				if (parseInt(property) > 0 || property == '0') {
					if (parseInt(value) > 0) {
						dataSet[lastProperty] = this.getFieldValueByPath(prefix, jQuery(path).slice(0, path.length - 1));
					};
				} else {
					dataSet[property] = this.getFieldValueByPath(prefix, path);
				};
			};
			path.pop();
		};
		return dataSet;
	},

	getFieldValueByPath : function(prefix, path) {
		var name = prefix;
		for (var i=0; i<path.length; i++) {
			var part = path[i];
			name += '[' + part + ']';
		};
		var selector = '[name="' + name + '"]';
		var field = jQuery(selector);
		return field.val();
	},

	cleanFields : function(form) {
		FED.FormValidator.fields.removeClass('f3-form-error').removeClass('loading');
	}

};

jQuery(document).ready(function() {
	FED.FormValidator.forms = jQuery('.fed-validator');
	FED.FormValidator.fields = FED.FormValidator.forms.find(':input, textarea');
	if (jQuery('.fed-validator').hasClass('fed-validate-all')) {
		FED.FormValidator.method = 'all';
	} else if (jQuery('.fed-validator').hasClass('fed-validate-field')) {
		FED.FormValidator.method = 'field';
	};
	FED.FormValidator.fields.each(function() {
		jQuery(this).blur(FED.FormValidator.validate);
	});
});
