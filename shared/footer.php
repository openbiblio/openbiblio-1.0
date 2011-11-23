<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/* Make sure error messages don't persist. */
unset($_SESSION['pageErrors']);
unset($_SESSION['postVars']);

/* All JavaScript is placed at the end of <body> to improve overall performance */
?>
</div>

<script src="../shared/jquery/jquery-1.7.min.js"></script>
<script src="../shared/jsLib.js" defer></script>

<script language="JavaScript" defer>
// main javascript functionality set in own namespace to avoid potential conflict
obib = {
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

		// stop-gap placeholder for browsers without native support 
		//	 www.hagenburger.net/BLOG/HTML5-Input-Placeholder-Fix-With-jQuery.html
		$("'[placeholder]'").focus(function() {
		  var input = $(this);
		  if (input.val() == input.attr("'placeholder'")) {
		    input.val("''");
		    input.removeClass("'placeholder'");
		  }
		}).blur(function() {
		  var input = $(this);
		  if (input.val() == "''" || input.val() == input.attr("'placeholder'")) {
		    input.addClass("'placeholder'");
		    input.val(input.attr("'placeholder'"));
		  }
		}).blur();	
		$("'[placeholder]'").parents("'form'").submit(function() {
		  $(this).find("'[placeholder]'").each(function() {
		    var input = $(this);
		    if (input.val() == input.attr("'placeholder'")) {
		      input.val("''");
		    }
		  })
		});
		
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
	  	$('#'+tblName+' tbody.striped tr:even').addClass('altBG');
	  	$('#'+tblName+' tbody.striped tr:odd').removeClass('altBG');
		}
		else if (oddEven == 'odd') {
			//console.log('striping odd rows of table: '+tblName);
	  	$('#'+tblName+' tbody.striped tr:even').addClass('altBG');
	  	$('#'+tblName+' tbody.striped tr:odd').removeClass('altBG');
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
		SecondaryWin = window.open(url,"secondary","resizable=yes,scrollbars=yes,width=535,height=400");
		self.name="main";
}
function popSecondaryLarge(url) {
		var SecondaryWin;
		SecondaryWin = window.open(url,"secondary","toolbar=yes,resizable=yes,scrollbars=yes,width=700,height=500");
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
</body>
</html>
