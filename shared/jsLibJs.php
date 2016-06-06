<script language="JavaScript" defer>
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
<?php
	if (!$doing_install) {
			require_once(REL(__FILE__, "../model/Validations.php"));
	}
?>
// JavaScript Document
"use strict";

/**
 * misc support JavaScript utility functions
 * @author Fred LaPlante
 */

/**
 * jQuery plugins for openBiblio
 */

(function($){
//-------------------------------------------------------------------
// element enable/disable - 'jQuery in Action', p12, 22Aug2008 - fl
$.fn.disable = function () {
	return this.each(function () {
					if (typeof this.disabled != "undefined") this.disabled = true;
				 });
};
$.fn.enable = function () {
	return this.each(function () {
					if (typeof this.disabled != "undefined") this.disabled = false;
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
};
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
})(jQuery);

/**
 * legacy javascript that I can't find a jQuery equivalent of
 * - needs to be made into a jQuery plugin as above
 */

var flos = {
//-------------------------------------------------------------------
<?php
	## required for inptFld() below, may be used by others ##
	if (!$doing_install) {
	  	$db = new Validations;
			$set = $db->getAll('description');
			echo "	patterns: {\n";
			//while ($row = $set->fetch_assoc()) {
            //while($row = $set->fetch(PDO::FETCH_ASSOC)) {
            foreach ($set as $row) {
				echo '		"'.$row['code'].'":"'.$row['pattern'].'",'."\n";
			}
			echo "	},\n\n";
	}
?>

// javascript version of OpenBiblio PHP function 2Aug2013 - fl
inptFld: function (type, name, value, attrs, data) {
	var s = "";
	if (!attrs) attrs = {};
	if (!attrs['id']) attrs['id'] = name;

	switch (type) {
	// FIXME radio
	case 'select':
		s += '<select name="'+name+'" ';
		$.each(attrs, function (key, val) {
			s += key+'="'+val+'" ';
		});
		s += ">\n";
		if (data) {
			$.each(data, function (val, desc) {
				s += '<option value="'+val+'" ';
				if (value == val) s += ' selected ';
				s += ">"+desc+"</option>\n";
			});
		}
		$s += "</select>\n";
		break;
	case 'textarea':
		s += '<textarea name="'+name+'" ';
		$.each(attrs, function (key, val) {
			s += key+'="'+val+'" ';
		});
		s += ">"+data+"</textarea>";
		break;
	case 'checkbox':
		s += '<input type="checkbox" name="'+name+'" ';
		s += 'value="'+value+'" ';
		if (value == data) s += ' checked ';
		$.each(attrs, function (key, val) {
			s += key+'="'+val+'" ';
		});
		s += "/>";
		break;
	//case 'number': attrs['pattern'] = '/\d*/'; handleInput(); break;
	//case 'date': attrs['pattern'] = flos.patterns.date; handleInput(); break;
	//case 'year': attrs['pattern'] = flos.patterns.year; handleInput(); break;
	//case 'tel': attrs['pattern'] = flos.patterns.tel; handleInput(); break;
	//case 'zip': attrs['pattern'] = flos.patterns.tel; handleInput(); break;
	//case 'url': attrs['pattern'] = flos.patterns.url; handleInput(); break;
	//case 'email': attrs['pattern'] = flos.patterns.email; handleInput(); break;
	default:
		attrs['pattern'] = flos.patterns[attrs['validation_cd']];
//console.log('inptFld(): attrs');
//console.log(attrs);
		handleInput();
		break;
	}
	return s;
	/* ------------a part of above inptFld() ------------------- */
	function handleInput () {
		s += '<input type="'+type+'" name="'+name+'" ';
		if (value != "") {
			s += 'value="'+value+'" ';
		}
		$.each(attrs, function (k,v) {
			if (k == 'required') {
				s += 'required aria-required="true" ';
			} else {
				s += k+'="'+v+'" ';
			}
		});
		s += "/>";
		if (attrs['required']) {
			s += '<span class="reqd">*</span>';
		}
	}
	/* ------------------------------------------------------- */
},

/* --------------------------------- */
    // return an array to satisfy a given 'n-m' range
    // from a StackOverflow answer by polkovnikov.ph Nov 19 '14 at 2:10
    range: function (limitStr) {
        var lowEnd = Number(limitStr.split('-')[0]);
        var highEnd = Number(limitStr.split('-')[1]);
        var arr = new Array();
        while(lowEnd <= highEnd) {
            arr.push(lowEnd++);
        }
        return arr;
    },

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
		var sel, rslt;
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
</script>
