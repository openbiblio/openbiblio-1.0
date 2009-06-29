<?php
	// this program is an adjunct to Biblio_New_Form.php, a part of OpenBiblio,
	// and is intended to add Z39.50 and/or SRU search capabilities to it.
	//
	// This code was developed by Fred LaPlante, Mercer, Maine, U.S.A.
	// and is placed in the public domain for the benifit of all.
	// Please credit the Author in any derivative work.

	##### works with OpenBiblio 0.4.0 thru 0.6.1

/**
 * LookUp - Library of Congress Lookup mod for OpenBiblio
 *
 * Current functionality:
 *
 *  * Lookup data from library of congress
 *  * Automatically fill bibliography information
 *
 * @package	OpenBiblio 0.6.0 - lookup
 * @version	see customHead.php
 * @author	Fred LaPlante
 * @date
 * @license     http://www.gnu.org/licenses/lgpl.txt Lesser GNU Public License
 *
 * @copyright Copyright &copy; 2004,5,6,7,8,9 All Rights Reserved.
 * @filesource
 *
 *    "Fred LaPlante" <flaplante@flos-inc.com>
 *
 */
  require_once("../shared/common.php");

  session_cache_limiter(null);

	$tab = "cataloging";
	$nav = "lookup";
  $focus_form_name = "lookupForm";
  $focus_form_field = "lookupVal";

  require_once(REL(__FILE__, "../functions/inputFuncs.php"));
  require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>
	<h1><span id="searchHdr" class="title"></span></h1>
	<div id="searchDiv">
		<form id="lookupForm" name="lookupForm" >
		<table class="primary">
		<thead>
		<tr>
		  <th class="colLbl">Text or Number to search for:</th>
		  <th class="colLbl">Which is a:</th>
		</tr>
		</thead>
		<tbody>
		<tr id="fldset1">
		  <td class="primary inptFld">
				<input type="text" size="30" id="lookupVal" name="lookupVal" class='criteria' value="" />
			</td>
		  <td class="primary inptFld">
    		<select id="srchBy" name="srchBy" class='criteria' >
					<option value="7" selected><?php echo T("lookup_isbn");?></option>
					<option value="8"><?php echo T("lookup_issn");?></option>
					<option value="9"><?php echo T("lookup_lccn");?></option>
					<option value="4"><?php echo T("lookup_title");?></option>
					<option value="1016"><?php echo T("lookup_keyword");?></option>
				</select>
			</td>
		</tr>
		
		<tr>
			<td class="primary"><?php echo T("lookup_andOpt");?></td>
		</tr>
		<tr id="fldset2">
		  <td class="primary inptFld">
				<input type="text" size="30" id="lookupVal2" name="lookupVal2" class='criteria' value="" />
			</td>
		  <td class="primary inptFld">
				<select id="srchBy2" name="srchBy2" class='criteria' >
					<option value="0" selected></option>
					<option value="1004"><?php echo T("lookup_author");?></option>
					<option value="1016"><?php echo T("lookup_keyword");?></option>
				</select>
			</td>
		</tr>
		
		<tr>
			<td class="primary"><?php echo T("lookup_andOpt");?></td>
		</tr>
		<tr id="fldset3">
			<td class="primary inptFld">
				<input type="text" size="30" id="lookupVal3" name="lookupVal3" class='criteria' value="" />
			</td>
		  <td class="primary inptFld">
    		<select id="srchBy3" name="srchBy3" class='criteria' >
					<option value="0" selected></option>
					<option value="1018"><?php echo T("lookup_publisher");?></option>
					<option value="59"><?php echo T("lookup_pubLoc");?></option>
					<option value="31"><?php echo T("lookup_pubDate");?></option>
					<option value="1016"><?php echo T("lookup_keyword");?></option>
				</select>
			</td>
		</tr>
		
		<tr>
			<td class="primary"><?php echo T("lookup_andOpt");?></td>
		</tr>
		<tr id="fldset4">
			<td class="primary inptFld">
				<input type="text" size="30" id="lookupVal4" name="lookupVal4" class='criteria' value="" />
			</td>
		  <td class="primary inptFld">
    		<select id="srchBy4" name="srchBy4" class='criteria' >
					<option value="0" selected>
					<option value="59"><?php echo T("lookup_pubLoc");?></option>
					<option value="1018"><?php echo T("lookup_publisher");?></option>
					<option value="31"><?php echo T("lookup_pubDate");?></option>
					<option value="1016"><?php echo T("lookup_keyword");?></option>
				</select>
			</td>
		</tr>
		
		<tr>
			<td class="primary"><?php echo T("lookup_andOpt");?></td>
		</tr>
		<tr id="fldset5">
			<td class="primary inptFld">
				<input type="text" size="30" id="lookupVal5" name="lookupVal5" class='criteria' value="" />
			</td>
		  <td class="primary inptFld">
    		<select id="srchBy5" name="srchBy5" class='criteria' >
					<option value="0" selected>
					<option value="31"><?php echo T("lookup_pubDate");?></option>
					<option value="1018"><?php echo T("lookup_publisher");?></option>
					<option value="59"><?php echo T("lookupPub_loc");?></option>
					<option value="1016"><?php echo T("lookup_keyword");?></option>
				</select>
			</td>
		</tr>

		<tr>
			<td>
				<input type="hidden" id="mode" name="mode" value="search" />
			</td>
		</tr>
		</tbody>

		<tfoot>
		<tr>
		  <td colspan="2" class="primary btnFld" >
				<input type="button" id="srchBtn" name="srchBtn" class="button"
							 value="<?php echo T("lookup_search");?>" />
			</td>
		</tr>
		</tfoot>
		</table>
		</form>
	</div>
	
	<div id="waitDiv">
		<table class="primary">
		<tr>
	  	<th colspan="1"><?php echo T("lookup_patience");?></th>
		</tr>
		<tr>
		  <td colspan="1" class="primary"><span id="waitText"></span></td>
		</tr>
		<tr>
	    <td align="center" colspan="1" class="primary">
	      <fieldset>
	        <?php echo T("lookup_resetInstr");?>
	      </fieldset>
			</td>
		</tr>
		</table>
	</div>

	<div id="retryDiv">
		<table class="primary">
		<tr>
			<th colspan="3" id="retryHead"></th>
		</tr>
		<tr>
			<td colspan="3" id="retryMsg" class="primary"></td>
		</tr>
		<tr>
	    <td colspan="3" class="primary btnFld">
				<input id="retryBtn" type="button" class="button"
							 value="<?php echo T("lookup_goBack");?>" />
			</td>
		</tr>
		</table>
	</div>

	<div id="choiceDiv">
		<input id="choiceBtn1" type="button" class="button btnFld"
					 value="<?php echo T("lookup_goBack");?>" />
	  <span id="choiceSpace">
	  	Search Results go here
	  </span>
		<input id="choiceBtn2" type="button" class="button btnFld"
					 value="<?php echo T("lookup_goBack");?>" />
	</div>

	<div id="selectionDiv">
	  <?php
			$helpPage = "biblioEdit";
			$cancelLocation = "../lookup2/lookup.php";
  		$focus_form_name = "newbiblioform";
  		$focus_form_field = "materialCd";

			require_once(REL(__FILE__, "../shared/get_form_vars.php"));
  		//$loc = new Localize(OBIB_LOCALE,$tab);
  		//$headerWording=$loc->getText("biblioNewFormLabel");
  		
  		## we use original biblio edit screen, but will replace existing 'Cancel' button
   		print '<form name="newbiblioform" method="POST" action="../catalog/biblio_new.php" >';
			include(REL(__FILE__,"../catalog/biblio_fields.php"));
		?>
	</div>
<?php
	## needed for all cases
	include("../shared/footer.php");
?>
