

function checkboxChanged (checkbox) {
	resetPage();
	var query = buildSearchQuery(checkbox.form);
	my_paz.search(query, 1000, null, null);
}


function buildSearchQuery (form) {
	var GOKs = [];
	
	$('fieldset', form).each( function (index) {
			if ( $('legend>label :checked', this)[0] 
					&& $('legend>label :checked', this)[0].value !== 'CHILDREN') {
				addSearchTermsToList($('legend>label :checked', this)[0].value, GOKs);
			}
			else {
				$('ul :checked', this).each( function (index) {
						addSearchTermsToList(this.value, GOKs);
					}
				);
			}
		}
	);
	
	var LKLQueryString = '(lkl=' + GOKs.join(' or lkl=') + ') and mak<>T*';
	
	return LKLQueryString;
}

function addSearchTermsToList (termsString, list) {
	var terms = termsString.split(',')
	for (var termIndex in terms) {
		var term = terms[termIndex];
		if (term && term !== '') {
			list.push(term);
		}
	}
}
