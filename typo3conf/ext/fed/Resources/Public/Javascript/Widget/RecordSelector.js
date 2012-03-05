if (typeof searchEnabled == 'undefined') {
	var searchEnabled = false;
};

var recordSelector = function(id, name, multiple, removeIcon, searchProperty, allUrl) {
	var parent = this;
	this.id = id;
	this.allUrl = allUrl;
	this.multiple = multiple;
	this.name = name;
	this.removeIcon = removeIcon;
	this.searchProperty = searchProperty;
	this.onSelect = function(event, value) {
		var target = jQuery('#' + parent.id + 'field');
		if (!parent.multiple) {
			target.val(value.item.id);
		} else {
			var exists = false;
			jQuery('#' + parent.id + ' .selection-list li').each(function() {
				if (parseInt(jQuery(this).attr('data-id')) == parseInt(value.item.id)) {
					exists = true;
				};
			});
			if (exists == false) {
				parent.createListItem(value.item.id, value.item.label);
			};
		};
	};
	this.onListSelect = function(event) {
		var id = parseInt(jQuery(this).attr('data-id'));
		var label = jQuery(this).html();
		var value = {
			item: {
				id: id,
				label: label
			}
		};
		parent.onSelect(event, value);
	};
	this.onClose = function(event) {
		var target = jQuery('#' + parent.id + 'search');
		target.val('');
	};
	this.addSelection = function() {
		var itemName = jQuery('#' + parent.id + 'search').val();
		parent.createListItem(null, itemName);
	};
	this.createListItem = function(id, name) {
		if (id) {
			jQuery('#' + parent.id + ' .selection-list').append('<li data-id="' + id + '">'
				+ name + '<a href="javascript:;">' + parent.removeIcon + '</a>'
				+ '<input type="hidden" name="' + parent.name + '[]" value="' + id + '" /></li>');
		} else {
			jQuery('#' + parent.id + ' .selection-list').append('<li data-id="' + id + '">'
				+ name + '<a href="javascript:;">' + parent.removeIcon + '</a>'
				+ '<input type="hidden" name="' + parent.name + '[][' + parent.searchProperty + ']" value="' + name + '" /></li>');
		};
		if (searchEnabled) {
			refreshResults();
		};
	};

	this.hideFullList = function() {
		var listElement = jQuery('#' + parent.id + 'list .list-element');
		listElement.fadeOut();
		jQuery('#' + parent.id + 'listbutton').click(parent.displayFullList);
	};
	this.displayFullList = function() {
		jQuery('#' + parent.id + 'listbutton').unbind('click', parent.displayFullList);
		jQuery('#' + parent.id + 'listbutton').click(parent.hideFullList);
		var listElement = jQuery('#' + parent.id + 'list .list-element');
		if (listElement.html() == '...') {
			listElement.html('').addClass('loading').fadeIn();
			jQuery.ajax({
				async: true,
				url: parent.allUrl,
				complete: function(response) {
					var json = jQuery.parseJSON(response.responseText);
					listElement.append('<ul>');
					for (var k in json) {
						var name = json[k][parent.searchProperty];
						if (name == '') {
							name = '(Id ' + json[k].uid + ')';
						};
						var listItem = jQuery('<li data-id="' + json[k].uid + '">' + name + '</li>').click(parent.onListSelect);
						listElement.append(listItem);
					};
					listElement.append('</ul>');
					listElement.removeClass('loading');
				}
			});
		};
	};
	if (multiple) {
		jQuery('#' + id).find('a').live('click', function() {
			jQuery(this).parent().remove();
			if (searchEnabled) {
				refreshResults();
			};
		});
	};
	jQuery('#' + parent.id + 'button').click(parent.addSelection);
	jQuery('#' + parent.id + 'listbutton').click(parent.displayFullList);
	jQuery('#' + parent.id + 'listbutton').unbind('click', parent.hideFullList);
};