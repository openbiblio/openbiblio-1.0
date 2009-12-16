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
	<h3><?php echo T('Add New Item'); ?></h3>

	<div id="searchDiv">
	  <input type="button" id="manualBtn" class="button" value="Manual Entry" />
	  <br />
		<form id="lookupForm" name="lookupForm" action="" >
		<fieldset>
		<legend>On-Line Search</legend>
		<table class="primary">
		<thead>
		<tr>
		  <th class="colLbl">What to search for:</th>
		  <th class="colLbl">Which is a:</th>
		</tr>
		</thead>
		<tbody>
		<?php
		  $isbnTxt    = T("lookup_isbn");
		  $issnTxt    = T("lookup_issn");
		  $lccnTxt    = T("lookup_lccn");
		  $titleTxt   = T("lookup_title");
		  $authorTxt  = T("lookup_author");
			$keywordTxt = T("lookup_keyword");
			$pubDateTxt = T("lookup_pubDate");
			$pubNameTxt = T("lookup_publisher");
			$pubLocTxt  = T("lookup_pubLoc");
		?>
		<tr id="fldset1">
		  <td class="primary inptFld">
				<?php echo inputfield('text','lookupVal','',array('class'=>'criteria')); ?>
			</td>
		  <td class="primary inptFld">
				<?php
				echo inputfield('select','srchBy','7',array('class'=>'criteria'),
						 array('7'=>"$isbnTxt",'8'=>"$issnTxt",'9'=>"$lccnTxt",'4'=>"$titleTxt",'1016'=>"$keywordTxt"));
				?>
			</td>
		</tr>
		
		<tr>
			<td class="primary"><?php echo T("lookup_andOpt");?></td>
		</tr>
		<tr id="fldset2">
		  <td class="primary inptFld">
				<?php echo inputfield('text','lookupVal2','',array('class'=>'criteria')); ?>
			</td>
		  <td class="primary inptFld">
				<?php
				echo inputfield('select','srchBy2','0',array('class'=>'criteria'),
						 array('0'=>' ','1004'=>"$authorTxt",'1016'=>"$keywordTxt"));
				?>
			</td>
		</tr>
		
		<tr>
			<td class="primary"><?php echo T("lookup_andOpt");?></td>
		</tr>
		<tr id="fldset3">
			<td class="primary inptFld">
				<?php echo inputfield('text','lookupVal3','',array('class'=>'criteria')); ?>
			</td>
		  <td class="primary inptFld">
				<?php
				echo inputfield('select','srchBy3','0',array('class'=>'criteria'),
						 array('0'=>' ','1018'=>"$pubNameTxt",'59'	=>"$pubLocTxt",'31'	=>"$pubDateTxt",'1016'=>"$keywordTxt"));
				?>
			</td>
		</tr>
		
		<tr>
			<td class="primary"><?php echo T("lookup_andOpt");?></td>
		</tr>
		<tr id="fldset4">
			<td class="primary inptFld">
				<?php echo inputfield('text','lookupVal4','',array('class'=>'criteria')); ?>
			</td>
		  <td class="primary inptFld">
				<?php
				echo inputfield('select','srchBy4','0',array('class'=>'criteria'),
						 array('0'=>' ','59'	=>"$pubLocTxt",'1018'=>"$pubNameTxt",'31'	=>"$pubDateTxt",'1016'=>"$keywordTxt"));
				?>
			</td>
		</tr>
		
		<tr>
			<td class="primary"><?php echo T("lookup_andOpt");?></td>
		</tr>
		<tr id="fldset5">
			<td class="primary inptFld">
				<?php echo inputfield('text','lookupVal5','',array('class'=>'criteria')); ?>
			</td>
		  <td class="primary inptFld">
				<?php
				echo inputfield('select','srchBy5','0',array('class'=>'criteria'),
						 array('0'=>' ','31'	=>"$pubDateTxt",'1018'=>"$pubNameTxt",'59'	=>"$pubLocTxt",'1016'=>"$keywordTxt"));
				?>
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
				<input type="submit" id="srchBtn" name="srchBtn" class="button"
							 value="<?php echo T("lookup_search");?>" />
			</td>
		</tr>
		</tfoot>
		</table>
		</fieldset>
		<p id="errMsgTxt"></p>
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
	  <form action="">
		<table class="primary">
		<tr>
			<th colspan="3" id="retryHead"></th>
		</tr>
		<tr>
			<td colspan="3" id="retryMsg" class="primary"></td>
		</tr>
		<tr>
	    <td colspan="3" class="primary btnFld">
				<input id="retryBtn" type="submit" class="button"
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

<div id="divTest">
<form id="frmTest">
</form>
</div>

	<div id="selectionDiv">
   	<!--form id="newbiblioform" name="newbiblioform" method="POST" action="chng-wrap.php" -->
  	<!-- submit action will now be handled by javascript code -->
   	<form id="newbiblioform" name="newbiblioform" action="" >
			<?php
//  		## we use original biblio edit screen, but will replace existing 'Cancel' button
//   		//<form name="newbiblioform" method="POST" action="../catalog/biblio_new.php" >
//
//			$cancelLocation = "../lookup2/lookup.php";
//  		$focus_form_name = "newbiblioform";
//  		$focus_form_field = "materialCd";
//
//			require_once(REL(__FILE__, "../shared/get_form_vars.php"));
//
			include(REL(__FILE__,"../catalog/biblio_fields.php"));
			?>
		</form>
	</div>

<div id="copyEditorDiv">
	<?php include_once(REL(__FILE__,"../catalog/biblio_copy_editor.php"));?>
</div>

<?php
	## needed for all cases
	include("../shared/footer.php");
?>
