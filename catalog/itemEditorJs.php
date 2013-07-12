<script language="JavaScript" >
//------------------------------------------------------------------------------
// newItem Javascript
"use strict";

var ie = {
	init: function () {
		ie.itemSubmitBtn = $('#itemSubmitBtn');
	  ie.itemSubmitBtnClr = ie.itemSubmitBtn.css('color');
	  //$('tbody#marcBody input.reqd').on('change',null,ie.validate);
	},

	disableItemSubmitBtn: function () {
	  ie.itemSubmitBtn.css('color', '#888888');
		ie.itemSubmitBtn.disable();
	},
	enableItemSubmitBtn: function () {
	  ie.itemSubmitBtn.css('color', ie.itemSubmitBtnClr);
		ie.itemSubmitBtn.enable();
	},

};
// this package normally initialized by parent such as .../catalog/new_itemJs.php
// only initialize here if used in standalone fasion
//if ($ !== undefined) $(document).ready(ie.init);

</script>
