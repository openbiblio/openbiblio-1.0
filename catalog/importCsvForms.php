<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  session_cache_limiter(null);

	include(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../functions/marcFuncs.php"));
	require_once(REL(__FILE__, "../model/MediaTypes.php"));
	require_once(REL(__FILE__, "../model/Collections.php"));
	require_once(REL(__FILE__, "../model/CopyStatus.php"));

  $focus_form_name = "specForm";
  $focus_form_field = "imptSrce";
	$tab = "cataloging";
	$nav = "csvImport";
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>

<h3 id="searchHdr"><?php echo T("CSVImport"); ?></h3>

<section id="intro">
	<form role="form" id="specForm" name="specForm" enctype="multipart/form-data" method="POST" >
		<input type="hidden" id="mode" name="mode" value="csvPreview" \>
		<input type=hidden name="userid" id="userid" value="<?php echo H($_SESSION["userid"])?>">

		<fieldset>
			<!--label><?php echo T("CSVloadTest"); ?>: 
				<?php //echo T("CSVloadTestTrue"); ?>
					<input type="radio" id="testTrue" name="test" value="true" checked \>  
				<?php //echo T("CSVloadTestFalse"); ?>
					<input type="radio" id="testFalse" name="test" value="false" \>
			</label> <br /-->
			<label><?php echo T("CSVInputFile"); ?>:
				<input type="file" id="imptSrce" name="imptSrce" required aria-required="true" autofocus \>
			</label>
		</fieldset>
	
		<fieldset>
			<legend><?php echo T("Options"); ?></legend>
			<table border=0>
			<tbody>
			  <tr>
					<td colspan="3"><label for="collectionCd"><?php echo T("Collection"); ?>:</label></td>
					<td colspan="2">
						<?php
						$cols = new Collections;
						echo inputfield('select', "collectionCd", $cols->getDefault(), NULL, $cols->getSelect());
						?>
					</td>
			  </tr>
			  
				<tr>
					<td colspan="3"><label for="materialCd"><?php echo T("Media Type"); ?>:</label></td>
					<td colspan="2">
						<?php
						$medTypes = new MediaTypes;
						echo inputfield('select', "materialCd", $medTypes->getDefault(), NULL, $medTypes->getSelect());
						?>
					</td>
				</tr>
			  
			  <tr>
					<td colspan="3"><label for="opacFlg"><?php echo T("biblioFieldsOpacFlg"); ?>:</label></td>
			    <td colspan="2">
			      <select name="opacFlg" id="opacFlg">
			        <option value="Y" SELECTED><?php echo T("AnswerYes"); ?></option>
			        <option value="N"><?php echo T("AnswerNo"); ?></option>
			      </select>
			    </td>
			  </tr>
			  
			  <tr>
					<td colspan="3">
						<label for="autoFlg"><?php echo T("Auto Barcode"); ?>
							is <?php echo ($_SESSION['item_autoBarcode_flg'] == 'Y'?T("ON"):T("OFF")); ?>.
							&nbsp;Make Item Copies?
						</label></td>
			    <td colspan="2">
			    	<select id="cpyAction" name="cpyAction">
			    		<option value="0"><?php echo T("Never"); ?></option>
			    		<option value="1"><?php echo T("Only if Barcode present"); ?></option>
			    		<option value="2" selected ><?php echo T("Always"); ?></option>
			    	</select>
			    </td>
			  </tr>
			  
			  <tr>
					<td colspan="3"><label for="copyText"><?php echo T("ImportCopyDescription"); ?>:</label></td>
			    <td colspan="2">
			      <input type=text name="copyText" id="copyText" size=20 maxsize=256 value="<?php echo T("CSVImport"); ?>" />
			    </td>
			  </tr>

			  <tr>
					<td colspan="3"><label for="code"><?php echo T("Copy Status"); ?>:</label></td>
			    <td colspan="2">
						<?php
						$cpyStatus = new CopyStatus;
						echo inputfield('select', "code", $cpyStatus->getDefault(), NULL, $cpyStatus->getSelect());
						?>
			    </td>
			  </tr>

			  <tr>
					<td colspan="3" nowrap><label for="showAll"><?php echo T("CSVshowAllFiles"); ?>:</label></td>
			    <td colspan="2">
			      <select name="showAll" id="showAll">
			        <option value="Y"><?php echo T("AnswerYes"); ?></option>
			        <option value="N" SELECTED><?php echo T("AnswerNo"); ?></option>
			      </select>
			    </td>
			  </tr>
			  </tbody>
			  
			  <tfoot>
			  <tr>
			  	<td colspan="3"><input type="button" id="helpBtn" value="<?php echo T("Help"); ?>" class="button" /></td>
	  			<td colspan="1"><input type="submit" id="imptBtn" value="<?php echo T("ScanCSVFile"); ?>" class="button" /></td>
	  			<td colspan="1">&nbsp;</td>
			  </tr>
			  </tfoot>
			  
			</table>
		</fieldset>
	</form>
	
	<div class="help">
		<ul>
			<li><?php echo T("CSVinputDescr"); ?></li>
			<li><?php echo T("CSVimportAdvise"); ?></li>
		</ul>
		<table border=1>
		  <tr>
		    <th><?php echo T("CSVcolumnHeading"); ?></th>
		    <th><?php echo T("CSVcolumnDescription"); ?></th>
		    <th><?php echo T("CSVcolumnComment"); ?></th>
		  </tr>
		  <tr>
		    <td><pre>Call1</pre></td>
		    <td><?php echo T("CSVCallNumber"); ?></td>
		    <td><?php echo T("CSVCallNrDescription"); ?></td>
		  </tr>
		  <tr>
		    <td><pre>barCo</pre></td>
		    <td><?php echo T("biblioCopyNewBarcode"); ?></td>
		    <td><?php echo T("CSVbarCoDescription"); ?></td>
		  </tr>
		  <tr>
		    <td><pre>coll</pre></td>
		    <td><?php echo T("Collection") ?></td>
		    <td><?php echo T("CSVoptionalDefault") ?></td>
		  </tr>
		  <tr>
		    <td><pre>media</pre></td>
		    <td><?php echo T("Media Type") ?></td>
		    <td><?php echo T("CSVoptionalDefault") ?></td>
		  </tr>
		  <tr>
		    <td><pre>showO</pre></td>
		    <td><?php echo T("Show in OPAC"); ?></td>
		    <td><?php echo T("CSVoptionalDefault") ?></td>
		  </tr>
		</table>
		<p wordwrap><?php echo T("CSVimportMoreMARC"); ?></p>
	</div>
</section>	<!-- intro -->

<section id="review" style="display:none;">
	<fieldset>
		<legend><?php echo T("CSVHeadings"); ?></legend>
		<table>
			<thead>
		  <tr>
		    <th><?php echo T("CSVTargets"); ?></th>
		    <th><?php echo T("CSVComments"); ?></th>
		  </tr>
		  </thead>
			<tbody id="colHeads"></tbody>
		</table>
	</fieldset>

  <fieldset>
  	<legend>Import File Records</legend>
  	<div id="csvErrs">
  	</div>
  	<br />
	  <table>
	  	<thead>
	  	<tr>
				<th><?php echo T("Data Tag") ?></th>
				<th><?php echo T("Data Subfield") ?></th>
				<th><?php echo T("Data") ?></th>
			</tr>
			</thead>
			<tbody id="csvRcrds">
			</tbody>
		  <tfoot>
		  <tr>
		  	<td colspan="1">&nbsp;</td>
  			<td colspan="1">
					<button id="revuBkupBtn" type="button" class="button bkupBtn"><?php echo T("Go Back"); ?></button>
					<button id="Post2DbBtn" type="button" class="button bkupBtn"><?php echo T("Import Data"); ?></button>
				</td>
  			<td colspan="1">&nbsp;</td>
		  </tr>
		  </tfoot>		</table>
	</fieldset>
</section>	<!-- review -->

<section id="rslts" style="display:none;">
	<fieldset>
		<legend>Import Results</legend>
		<div id="csvImportRslts">
		</div>
  	<button id="rsltBkupBtn" class="button bkupBtn" type="button"><?php echo T("Go Back"); ?></button>
	</fieldset>
</section>	

<?php
    require_once(REL(__FILE__,'../shared/footer.php'));
	include_once(REL(__FILE__,'../shared/ajaxFileUpload/ajaxfileupload.js'));
	require_once(REL(__FILE__, "../catalog/importCsvJs.php"));
?>	
</body>
</html>
