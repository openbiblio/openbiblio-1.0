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

  $focus_form_name = "specForm";
  $focus_form_field = "imptSrce";
	$tab = "cataloging";
	$nav = "csvImport";
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>

<h3 id="searchHdr"><?php echo T('CSVImport'); ?></h3>

<section id="intro">
	<form id="specForm" name="specForm" enctype="multipart/form-data" method="POST" >
		<fieldset>
			<label><?php echo T("CSVloadTest"); ?>: 
				<?php echo T("CSVloadTestTrue"); ?>
					<input type="radio" id="testTrue" name="test" value="true" checked \>  
				<?php echo T("CSVloadTestFalse"); ?>
					<input type="radio" id="testFalse" name="test" value="false" \>
			</label> <br />
			<input type="hidden" id="mode" name="mode" value="csvPreview" \>
			<label><?php echo T("CSVloadTestFileUpload"); ?>: 
				<input type="file" id="imptSrce" name="imptSrce" required aria-required="true" autofocus \>
			</label>
		</fieldset>
	
		<fieldset>
			<legend><?php echo T("Defaults"); ?></legend>
			<table border=0>
			<tbody>
			  <tr>
					<td colspan="3"><label for="collectionCd"><?php echo T("Collection:"); ?></label></td>
					<td colspan="2">
						<?php
						$cols = new Collections;
						echo inputfield('select', "collectionCd", $cols->getDefault(), NULL, $cols->getSelect());
						?>
					</td>
			  </tr>
			  
				<tr>
					<td colspan="3"><label for="materialCd"><?php echo T("Media Type:"); ?></label></td>
					<td colspan="2">
						<?php
						$medTypes = new MediaTypes;
						echo inputfield('select', "materialCd", $medTypes->getDefault(), NULL, $medTypes->getSelect());
						?>
					</td>
				</tr>
			  
			  <tr>
					<td colspan="3"><label for="opacFlg"><?php echo T("biblioFieldsOpacFlg"); ?></label></td>
			    <td colspan="2">
			      <SELECT name="opacFlg" id="opacFlg">
			        <option value=Y SELECTED><?php echo T("AnswerYes"); ?></option>
			        <option value=N><?php echo T("AnswerNo"); ?></option>
			      </select>
			    </td>
			  </tr>
			  
			  <tr>
					<td colspan="3"><label for="showAll"><?php echo T("CSVshowAllFiles"); ?></label></td>
			    <td colspan="2">
			      <SELECT name="showAll" id="showAll">
			        <option value=Y><?php echo T("AnswerYes"); ?></option>
			        <option value=N SELECTED><?php echo T("AnswerNo"); ?></option>
			      </select>
			    </td>
			  </tr>
			  
			  <tr>
					<td colspan="3"><label for="copyText"><?php echo T("CSVcopyDescription"); ?></label></td>
			    <td colspan="2">
			      <input type=text name="copyText" id="copyText" size=32 maxsize=256 value="CSV Import">
			      <input type=hidden name="userid" id="userid" value="<?php echo H($_SESSION["userid"])?>">
			    </td>
			  </tr>
			  </tbody>
			  <tfoot>
			  <tr>
			  	<td colspan="3">&nbsp;</td>
	  			<td colspan="1"><input type="submit" id="imptBtn" value="<?php echo T("Upload File"); ?>" class="button" /></td>
	  			<td colspan="1">&nbsp;</td>
			  </tr>
			  </tfoot>
			</table>
		</fieldset>
	</form>
	
	<div class="note">
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
	    <td>Show in OPAC</td>
	    <td><?php echo T("CSVoptionalDefault") ?></td>
	  </tr>
	</table>
	
	<p><?php echo T("CSVimportMoreMARC"); ?></p>
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
  	<legend>Line # <span id="LineNo">$lineNmbr</span></legend>
	  <table>
	  	<thead>
	  	<tr>
				<th><?php echo T("Data Tag") ?></th>
				<th><?php echo T("Date Subfield") ?></th>
				<th><?php echo T("Data") ?></th>
			</tr>
			</thead>
			<tbody id="errs">
			</tbody>
			<tbody id="rcrds">
			</tbody>
		  <tfoot>
		  <tr>
		  	<td colspan="1">&nbsp;</td>
  			<td colspan="1"><button id="revuBkupBtn" type="button" class="button bkupBtn"><?php echo T("Backup"); ?></button></td>
  			<td colspan="1">&nbsp;</td>
		  </tr>
		  </tfoot>		</table>
	</fieldset>
</section>	<!-- review -->

<section id="rslts" style="display:none;">
	<fieldset>
		<legend>Record # <span id="LineNo"></span></legend>
  	<button id="rsltBkupBtn" class="button bkupBtn" type="button"><?php echo T("Backup"); ?></button></td>
	</fieldset>
</section>	

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	include_once(REL(__FILE__,'../shared/ajaxFileUpload/ajaxfileupload.js'));
	require_once(REL(__FILE__, "importCsvJs.php"));
?>	
