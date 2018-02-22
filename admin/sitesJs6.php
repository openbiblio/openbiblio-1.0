<script language="JavaScript" >
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
// JavaScript Document
"use strict";

class Sit extends Admin {
    constructor () {
    	var url = '../admin/adminSrvr.php',
    		form = $('#editForm'),
    		dbAlias = 'sites';
    	var hdrs = {'listHdr':<?php echo '"'.T("List of Sites").'"'; ?>,
    				'editHdr':<?php echo '"'.T("Edit Site").'"'; ?>,
    				'newHdr':<?php echo '"'.T("Add New Site").'"'; ?>,
    				};
    	var listFlds = {'name': 'text',
    					'code': 'text',
    					'city':'text',
    				   };
    	var opts = { 'focusFld':'name', 'keyFld':'siteid' };

	    super( url, form, dbAlias, hdrs, listFlds, opts );

    	this.noshows = [];

		$('#mergeBtn').on('click',null,this.doMergeSites);
		//list.getSiteList($('#fmSite'));
		//list.getSiteList($('#toSite'));
        //list.getPullDownList('State', $('#state'));
        //list.getPullDownList('Calendar', $('#calendar'));

        $('#country').val('xxxx');
    };

	async fetchList() {
		// using 'promise' technique to insure calls are processed in-turn
		await list.getSiteHoldings();
		await super.fetchList();

		await list.getSiteList($('#fmSite'));
		await list.getSiteList($('#toSite'));
		await list.getPullDownList('State', $('#state'));
		await list.getPullDownList('Calendar', $('#calendar'));
	};
    fetchHandler (dataAray) {
		super.fetchHandler(dataAray);
		//this.holdings = list.holdings
		var $rows = $('#showList tbody tr');

		// add holdings to each site display
		$rows.each(function (i){
			var siteid = $(this).find('input[type="hidden"]').val();
			let nmbr = list.holdings[siteid];
			let html = '<td>'+nmbr+'</td>';
			$(this).append(html);
		});
	};

    //fetchStates () {
    //};

    //fetchCalendars () {
    //};

	doMergeSites(e) {
    	$('#listDiv').hide();
    	$('#extraDiv').show();
		$('#mergeSiteBtn').enable();
		$('#fmSite').on('change', null, function () {
			let nmbr = list.holdings[$('#fmSite').val()];
			$('#limit').val(nmbr);
		});
	};

    doSubmitFields (e) {
    	var theBtn = e.target.id;
    	if (theBtn == 'mergeSiteBtn') {
			let fm = $('#fmSite').val();
			let to = $('#toSite').val();
			let maxHit = list.holdings[fm];
			let limit = $('#limit').val();
			if (this.validateMerge(fm, to, limit, maxHit)) {
    			e.preventDefault();
    			e.stopPropagation();
				$('#mergeSiteBtn').disable();
				this.merge(fm, to, limit);
			} else
				return false;
		} else {
			super.doSubmitFields(e);
		}
	};
	validateMerge(fm, to, limit, maxHit){
		let crntSite = <?php echo $_SESSION['current_site']; ?>;
		if (fm == crntSite) {
			alert('You may not remove items from logged-in site');
			return false;
		} else if (fm == to) {
			alert('Both sites cannot be the same');
			return false;
		} else if (limit > maxHit) {
			alert('Number to transfer cannot exceed holdings.');
		}
		return true;
	};
	merge(fm, to, limit) {
		let parms = "cat=sites&mode=mergeSites&fm="+fm+"&to="+to+"&limit="+limit;
    	//$.post(this.url, parms, $.proxy(this.mergeHandler, this), 'json');
    	$.post(this.url, parms, this.mergeHandler, 'json');
	};
	mergeHandler(response) {
		//console.log(response);
		super.showResponse(response);
	};

    doNewFields (e) {
        var localeCntry = navigator.language.slice(-2);
        //console.log("country is "+localeCntry);

        super.doNewFields.apply(this);
        //console.log('got here');
        $('#country').val(localeCntry);
    	$('#editDiv').show();
    }
}

$(document).ready(function () {
	var xxxx = new Sit();
});

</script>
