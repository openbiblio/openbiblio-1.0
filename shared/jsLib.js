// JavaScript Document
// jQuery plugins for openBiblio
// element enable/disable - 'jQuery in Action', p12, 22Aug2008 - fl
$.fn.disable = function () {
	return this.each(function () {
					if (typeof this.disabled != "undefined)") this.disabled = true;
				 });
};
$.fn.enable = function () {
	return this.each(function () {
					if (typeof this.disabled != "undefined)") this.disabled = false;
				 });
};
//------------------------------------------------------------------------------
// flos-lib stuff for lookup
// get/set selected value or text
flos = {
	getSelectBox: function  (boxId, getText/*boolean*/) {
		if (typeof(boxId) === 'string')
			sel = $('#'+boxId);
		else
	   	sel = boxId;
		var choice = sel[0].selectedIndex;
		if(getText)
			rslt = sel[0].options[choice].text;
		else
			rslt = sel[0].options[choice].value;
		return rslt;
	},
/* --------------------------------- */
	setSelectBox: function  (boxId,optText,exactMatch) {
		var theBox;
		if (typeof(boxId) === 'string')
			theBox = $('#'+boxId);
		else
	   	theBox = boxId;
		//console.log('theBox is '+theBox);

		if (!theBox)alert('cant find select box with ID='+boxId);
		var opts = theBox[0].options;
		if (exactMatch == null) exactMatch = true;
		//console.log('in select box: '+boxId+'; match='+exactMatch+'; look for '+optText);
		for (var i=0; i<opts.length; i++) {
			//console.log('want:'+optText+'; have:'+opts[i].text+' ('+opts[i].value+'); #'+i+' of '+opts.length);
			if (opts[i].selected) opts[i].selected=false;
			if (exactMatch == 'useId') {
				//console.log('with useId"');
			  if (opts[i].value == optText) {
					opts[i].selected = true;
					return;
				}
			} else if (exactMatch == true) {
				//console.log('w/exact, want:'+optText+'; have:'+opts[i].text+' ('+opts[i].value+'); #'+i+' of '+opts.length);
				if (opts[i].text.replace(/ /g,'') == optText.replace(/ /g,'')) {
					opts[i].selected = true;
					return;
				}
			} else {
				//console.log('w/???');
				//alert('want:'+optText+'; have:'+opts[i].text+' ('+opts[i].value+'); #'+i+' of '+opts.length);
			   if (optText == '-') return;
				if (opts[i].text.indexOf(optText)>0) {
					opts[i].selected = true;
					return;
				}
		  }
		}
	}
}
