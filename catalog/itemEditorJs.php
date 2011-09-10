<script language="JavaScript" >
//------------------------------------------------------------------------------
// newItem Javascript
ie = {
	init: function () {
		ie.itemSubmitBtn = $('#itemSubmitBtn');
	  ie.itemSubmitBtnClr = ie.itemSubmitBtn.css('color');
	  //$('tbody#marcBody input.reqd').bind('change',null,ie.validate);
	},

	disableItemSubmitBtn: function () {
	  ie.itemSubmitBtn.css('color', '#888888');
		ie.itemSubmitBtn.disable();
	},
	enableItemSubmitBtn: function () {
	  ie.itemSubmitBtn.css('color', ie.itemSubmitBtnClr);
		ie.itemSubmitBtn.enable();
	},
/*
	validate: function () {
		// verify all 'required' fields are populated
		var errs = 0;
		$('tbody#marcBody .reqd').each(function () {
		  var $fld = $('#'+this.id);
		  var testVal = $.trim($fld.val());  //assure no whitespace to mess things up
		  if ((testVal == 'undefined') || ((testVal == '') && (testVal == "<?php echo T('REQUIRED FIELD'); ?>"))) {
				$('label[for="'+this.id+'"]').addClass('error');
				$fld.addClass('error').val("<?php echo T('REQUIRED FIELD'); ?>");
				errs++;
			} else {
				$('label[for="'+this.id+'"]').removeClass('error');
				$fld.removeClass('error')
			}
		});
		if (errs > 0) {
		  ie.disableItemSubmitBtn()
			return false;
		} else {
		  ie.enableItemSubmitBtn()
		  return true;
		}
	}
*/
};
// this package normally initialized by parent such as .../catalog/new_itemJs.php
// only initialize here if used in standalone fasion
//if ($ !== undefined) $(document).ready(ie.init);

</script>
