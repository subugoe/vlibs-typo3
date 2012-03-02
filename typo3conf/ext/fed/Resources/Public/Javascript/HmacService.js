/**
 * HMAC service for Javascript
 *
 * @depends jQuery
 * @depends Serialize
 * @depends Utf8Encode
 * @depends Utf8Decode
 * @depends SHA1
 *
 * Allows HMAC checksums to be generated dynamically, to support dynamic
 * Extbase forms which still have request validation.
 *
 * To work this, simply insert a "FED Hasher" plugin on the page containing
 * your form and use FED.HMAC.generateRequestHash(formFieldObject) where
 * formFieldObject is a single- or multi-dimensional object where properties are
 * key names and values are either an integer "1" meaning "property is included"
 * or an array of related objects which also have properties which are property
 * names and values which are integer "1".
 *
 * Fire FED.HMAC.generateRequestHash(fields) whenever your field comp. changes
 * and manually update the __hmac field - or simply use
 * FED.HMAC.bind(selector) where selector can be a jQuery DOM target or an already
 * selected jQuery element. After this the class automatically creates a new
 * HMAC checksum just before allowing the form to submit.
 *
 * Since this is a potential security concern you should NEVER use this service
 * on unprotected pages.
 */

if (typeof FED == 'undefined') {
	var FED = {};
}

FED.HMAC = {

	hmacField : null,
	form : null,
	contentChecksum: null,

	generateRequestHash : function(formFieldObject) {
		hmac = serialize(hmac);
		var validatedString = jQuery.ajax({
			type : "POST",
			url : "?tx_fed_hash[action]=request",
			data : {
				"tx_fed_hash[subject]" : hmac
			},
			async : false
		}).responseText;
		hmac += validatedString;
		return hmac;
	},

	bind : function(selector) {
		if (typeof selector == 'undefined') {
			return false;
		};
		if (typeof selector == 'string') {
			selector = jQuery(selector);
		};
		var thisObject = this;
		thisObject.form = selector;
		thisObject.contentChecksum = sha1(selector.html());
		selector.submit(function() {
			var snapshot = thisObject.form.html();
			if (thisObject.contentChecksum != sha1(snapshot)) {
				var formFieldObject = thisObject.gatherFormFields();
				var requestHash = thisObject.generateRequestHash(formFieldObject);
				thisObject.hmacField.val(requestHash);
				thisObject.contentChecksum = sha1(snapshot);
			};
		});
		selector.find('input[type="hidden"]').each(function() {
			if (jQuery(this).attr('nane').indexOf('__hmac') > 0) {
				thisObject.hmacField = jQuery(this);
			};
		});
		return true;
	},

	gatherFormFields : function() {

	}

}