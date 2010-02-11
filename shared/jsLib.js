// JavaScript Document
// jQuery plugins for openBiblio
//
//-------------------------------------------------------------------
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

//-------------------------------------------------------------------
// replace existing content with new - 'jQuery in Action', p77, 15Jul2009 - fl
$.fn.replaceWith = function (html) {
	return this.after(html).remove();
};

//-------------------------------------------------------------------
// <select> empty / load - 'jQuery in Action', p246, 1Jul2009 - fl
$.fn.emptySelect = function () {
	return this.each(function () {
		if (this.tagName=='SELECT') this.options.length=0;
	});
}
$.fn.loadSelect = function (optionsDataArray) {
	return this.emptySelect().each(function() {
		if (this.tagName=='SELECT') {
			var selectElement = this;
			$.each(optionsDataArray,function(index,optiondata) {
				var option = new Option(optionsData.caption, optionsData.value);
				if ($.browser.msie) {
					selectElement.add(option);
				} else {
				  selectElement.add(option,null);
				}
			});
		}
	});
};

//------------------------------------------------------------------------------
// legacy javascript that I can't find a jQuery equivalent of
// - needs to be made into a jQuery plugin as above
flos = {
/* --------------------------------- */
	// left pad a string 'str' with char 'ch' to width 'wid', 22Aug2008 - fl
	pad: function (str, wid, ch) {
	  var cStr = new String(ch);
	  for (var i=0; i<wid; i++)
	    cStr = cStr+ch;
		return (cStr+str).substr(str.length+1);
	},
/* --------------------------------- */
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

		if (!theBox)alert('cant find select box with ID='+boxId);
		var opts = theBox[0].options;
		if (exactMatch == null) exactMatch = true;
		for (var i=0; i<opts.length; i++) {
			if (opts[i].selected) opts[i].selected=false;
			if (exactMatch == 'useId') {
			  if (opts[i].value == optText) {
					opts[i].selected = true;
					return;
				}
			} else if (exactMatch == true) {
				if (opts[i].text.replace(/ /g,'') == optText.replace(/ /g,'')) {
					opts[i].selected = true;
					return;
				}
			} else {
			  if (optText == '-') return;
				if (opts[i].text.indexOf(optText)>0) {
					opts[i].selected = true;
					return;
				}
		  }
		}
	},
/* --------------------------------- */
	stripJunk: function (str, bag) {
		// Removes all characters from string 'str' which do NOT appear in string 'bag'.
   	var i;
    var returnString = "";

    // Search through string's characters one by one.
    // If character is in bag, append to returnString.
    for (i = 0; i < str.length; i++) {
        var c = str.charAt(i);
        if (bag.indexOf(c) != -1) returnString += c;
    }
    return returnString;
	}
}
