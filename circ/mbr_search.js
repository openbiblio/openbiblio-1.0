// based on a function from PhpMyAdmin
function setCheckboxes()
{
	var checked = document.forms['selection'].elements['all'].checked;
	var elts = document.forms['selection'].elements['id[]'];
	if (typeof(elts.length) != 'undefined') {
		for (var i = 0; i < elts.length; i++) {
			elts[i].checked = checked;
		}
	} else {
		elts.checked = checked;
	}
	return true;
}
