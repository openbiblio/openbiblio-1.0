<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/* Make sure error messages don't persist. */
unset($_SESSION['pageErrors']);
unset($_SESSION['postVars']);

?>
</div>  <!-- closing of div Content -->

<!-- All JavaScript is placed at the end of <body> to improve overall performance

		note: jQuery was here, moved to header-top so it would be available to
		HTML/JS conditional modules loaded in line as required.
-->

<?php include_once ("../shared/jsLibJs.php"); ?>

<script language="JavaScript" >
"use strict";

// main javascript functionality set in own namespace to avoid potential conflict
var obib = {
	<?php
		echo "focusFormName:  '$focus_form_name',\n";
		echo "focusFormField:	'$focus_form_field',\n";
		if (isset($confirm_links) and $confirm_links) {
			echo "confirmLinks:		$confirm_links,\n";
		}
	?>

	init: function() {
		obib.reStripe();
	  // set focus to specified field in all pages
		if ((obib.focusFormName.length > 0) && (obib.focusFormField.length > 0)) {
		  $('#'+obib.focusFormField).focus();
		}
		
		// suggest this should be in code local to desired function unless widely used -- Fred
		// bind the confirmLink routine to all <a> tags on the current form
		if (obib.confirmLinks) {
			$('a').on('click',null,obib.confirmLink);
		}
	},

	//-------------------------
	/*
	 * This function parses ampersand-seperated name=value argument pairs from
	 * the query string of the URL.  It stores the name=value pairs in
	 * properties of an object and returns that object.  Use it like this:
	 *
	 * var args = urlArgs();  // parse args from URL
	 * var q = args.q || "";   // use arguement, if defined, or a default value
	 * var n = args.n ? parseInt(args.n) : 10;
	 *
	 * adapted "JavaScript: the Definitive Guide", by David Flanagan, 6th ed, p.344
	 */
	urlArgs: function(url) {
		var args = {};                            // start wit empty object
		var query = url || location.search.substring(1); // get query string minus '?'
		var pairs = query.split('&');             // split at ampersands
		for (var i=0; i<pairs.length; i++) {      // for each fragment
			var pos = pairs[i].indexOf('=');        // look for name=value
			if (pos == -1) continue;                // if not found, skip it
			var name = pairs[i].substring(0,pos);   // extract the name
			var value = pairs[i].substring(pos+1);  // extract the value
			value = decodeURIComponent(value);      // decode the value
			args[name] = value                      // store as a property
		}
		return args;
	},

	//-------------------------
	reStripe: function(which) {
		// re-stripe all tables so classed on all pages
	  	$('table tbody.striped tr:even').addClass('altBG');
	  	$('table tbody.striped tr:odd').removeClass('altBG');
	},
	reStripe2: function(tblName, oddEven) {
		// re-stripe specified table
		if (oddEven == 'even') {
			//console.log('striping even rows of table: '+tblName);
	  	$('#'+tblName+'>tbody.striped tr:even').addClass('altBG');
	  	$('#'+tblName+'>tbody.striped tr:odd').removeClass('altBG');
		}
		else if (oddEven == 'odd') {
			//console.log('striping odd rows of table: '+tblName);
	  	$('#'+tblName+'>tbody.striped tr:even').addClass('altBG');
	  	$('#'+tblName+'>tbody.striped tr:odd').removeClass('altBG');
	 }
	},
	
	//-------------------------
	confirmLink: function(e) {
		if (modified) {
			return confirm("<?php echo addslashes(T("This will discard any changes you've made on this page.  Are you sure?")) ?>");
		} else {
			return true;
		}
	}
}
// hold off javascript until DOM is fully loaded; images, etc, may not all be loaded yet.
$(document).ready(obib.init);

function popSecondary(url) {
		var SecondaryWin;
		SecondaryWin = window.open(url,"secondary","resizable=yes,scrollbars=yes,width=620,height=400");
		self.name="main";
}
function popSecondaryLarge(url) {
		var SecondaryWin;
		SecondaryWin = window.open(url,"secondary","toolbar=yes,resizable=yes,scrollbars=yes,width=800,height=500");
		self.name="main";
}
function backToMain(URL) {
		var mainWin;
		mainWin = window.open(URL,"main");
		mainWin.focus();
		this.close();
}
var modified = false;

</script>
