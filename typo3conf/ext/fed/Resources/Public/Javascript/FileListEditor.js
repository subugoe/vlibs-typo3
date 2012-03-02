(function(jQuery){
	jQuery.fn.fileListEditor = function(options) {
		var defaults = {
			buttons : {
				browse: true,
				start: true,
				stop: true
			},
			unique_names : false
		};
		var options = jQuery.extend(defaults, options);

			// main loop - intialize all selected elements
		return this.each(function() {
			var addFile = function(up, file, info) {
				if (file instanceof Array) {
					if (file.length == 0) {
						return false;
					};
					var index = 0;
					while (index < file.length) {
						addFile(up, file[index], info);
						index++;
					};
					return true;
				};

				setTimeout(function() {
					uploader.removeFile(file);
				}, 0);
				tableHeader.append("<tr class='plupload_delete ui-state-default plupload_file'>" +
					"<td class='plupload_cell plupload_file_name'><span>" + file.name + "</span></td>" +
					"<td class='plupload_cell plupload_file_status'>Uploaded</td>" +
					"<td class='plupload_cell plupload_file_size'>" + plupload.formatSize(file.size) + "</td>" +
					"<td class='plupload_cell'><div class='ui-icon ui-icon-circle-minus remove'></div></td>" +
					"</tr>");
				if (!file.existing) {
					options.files.push(file);
				};
				updateField();
			};
			var updateField = function() {
				var i;
				var files = [];
				for (i=0; i<options.files.length; i++) {
					files.push(options.files[i].name);
				};
				field.val(files.join(','));
			};
			var element = jQuery(this).plupload(options);
			var field = jQuery('#' + element.attr('id') + '-field');
			var uploader = element.plupload('getUploader');
			var tableHeader = element.find('.plupload_filelist:first');
			for (var i=0; i<options.files.length; i++) {
				addFile(uploader, options.files[i]);
			};
			tableHeader.attr('id', options.editorId);
			tableHeader.find('.plupload_filelist_header').append('<td class="plupload_cell plupload_file_delete"></td>');
			jQuery(this).find('.remove').live('click', function() {
				var row = jQuery(this).parents('tr:first');
				var filename = row.find('.plupload_file_name span').html().trim();
				if (filename.length < 1) {
					return false;
				};
				var files = [];
				for (var i=0; i<options.files.length; i++) {
					if (options.files[i].name != filename) {
						files.push(options.files[i]);
					};
				};
				options.files = files;
				row.fadeOut(250);
				updateField();
			});
			uploader.bind('FileUploaded', function(up, file, info) {
				addFile(up, file, info);
			});
		});
	};
})(jQuery);
