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
	$nav = "marcImport";
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>

<h3 id="searchHdr"><?php echo T("MARCImport"); ?></h3>

<section id="intro">
	<form role="form" id="specForm" name="specForm" action="../catalog/importServer.php" enctype="multipart/form-data" method="POST" >
		<input type="hidden" id="mode" name="mode" value="marcPreview" \>
		<input type=hidden name="userid" id="userid" value="<?php echo H($_SESSION["userid"])?>">

		<fieldset>
			<label><?php echo T("Test Load"); ?>:
				<?php echo T("MARCloadTestTrue"); ?>
					<input type="radio" id="testTrue" name="test" value="true" checked \>
				<?php echo T("MARCloadTestFalse"); ?>
					<input type="radio" id="testFalse" name="test" value="false" \>
			</label> <br />
			<label><?php echo T("MARCInputFile"); ?>:
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
						//$cols = new Collections;
						//echo inputfield('select', "collectionCd", $cols->getDefault(), NULL, $cols->getSelect());
						?>
						<select id="collectionCd"> </select>
					</td>
			  </tr>
			  
				<tr>
					<td colspan="3"><label for="materialCd"><?php echo T("Media Type"); ?>:</label></td>
					<td colspan="2">
						<?php
						//$medTypes = new MediaTypes;
						//echo inputfield('select', "materialCd", $medTypes->getDefault(), NULL, $medTypes->getSelect());
						?>
						<select id="materialCd"> </select>
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
			      <input type=text name="copyText" id="copyText" size=20 maxsize=256 value="<?php echo T("MARCImport"); ?>" />
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

			  </tbody>
			  
			  <tfoot>
			  <tr>
			  	<td colspan="3"><input type="button" id="helpBtn" value="<?php echo T("Help"); ?>" class="button" /></td>
	  			<td colspan="1"><input type="submit" id="imptBtn" value="<?php echo T("Upload File"); ?>" class="button" /></td>
	  			<td colspan="1">&nbsp;</td>
			  </tr>
			  </tfoot>
			  
			</table>
		</fieldset>
	</form>
	
</section>	<!-- intro -->

<section id="rslts" style="display:none;">
	<fieldset>
		<legend>Import Results</legend>
		<div id="mrcImportRslts">
		</div>
  	<button id="rsltBkupBtn" class="button bkupBtn" type="button"><?php echo T("Go Back"); ?></button>
	</fieldset>
</section>	

<?php
    require_once(REL(__FILE__,'../shared/footer.php'));
//	require_once(REL(__FILE__,'../shared/txtFileUpload/ajaxFileUploader.js'));
//	require_once(REL(__FILE__,'../shared/simpleUpload.min.js'));
	require_once(REL(__FILE__,'../catalog/importMarcJs.php'));
?>	
  <script src="../shared/simpleUpload.min.js"></script>

</body>
</html>
