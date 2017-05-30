<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/* Make sure error messages don't persist. */
unset($_SESSION['pageErrors']);
unset($_SESSION['postVars']);

?>
	<!-- =================================================== -->
	<!-- Common user message area for all pages, hidden when empty -->
	<div id="msgDiv">
		<span id="userMsg"></span>
	</div>


</main>  <!-- closes off <main id="Content"> -->

<!-- All JavaScript should be placed at the end of <body> to improve overall performance

		note: jQuery was here, moved to header-top so it would be available to
		HTML/JS conditional modules loaded in line as required.
-->

<?php
    include_once ("../shared/jsLibJs.php");     // misc support functions
    include_once ("../shared/listJs.php");     // provide content for pull-down lists
?>

<script language="JavaScript" >
"use strict";

// common javascript utility functions set in own namespace to avoid potential conflict
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
			//console.log('setting focus to '+obib.focusFormName);
		}
		
		// bind the confirmLink routine to all <a> tags on the current form
		/* suggest this should be in code local to desired function unless widely used -- FL */
		if (obib.confirmLinks) {    // defined by php above init()
			$('a').on('click',null,obib.confirmLink);
		}
	},

	// common user message handler(s) for #msgDiv at top of this file
	showMsg: function (msg) {
		$('span#userMsg').html(msg).removeClass('error').addClass('info');
		$('#msgDiv').show();
	},
	showError: function (msg) {
		msg = 'Error: '+msg;
		$('span#userMsg').html(msg).removeClass('info').addClass('error');
		$('#msgDiv').show();
	},
	hideMsg: function (howFast='slow') {
		$('span#userMsg').html('');
		if (howFast == 'now') {
			$('#msgDiv').hide(); // instantaneous, immediate
		} else {
			$('#msgDiv').hide(howFast);
		}
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

	//is this still required? No references found in any file. FL 29 May 2017
	confirmLink: function(e) {
		if (modified) {
			return confirm("<?php echo addslashes(T("This will discard any changes you've made on this page.  Are you sure?")) ?>");
		} else {
			return true;
		}
	}


}
// hold off above javascript until DOM is fully loaded; images, etc, may not all be loaded yet.
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

<?php
 ## ##################################
 ## adds suport for plugin custom footers  - fl, 2016
 ## ##################################
		$list = getPlugIns('foot.foot');
		for ($x=0; $x<count($list); $x++) {
			include($list[$x]);
		}
 ## ##################################
