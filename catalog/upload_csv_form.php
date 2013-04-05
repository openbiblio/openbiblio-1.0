<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
  $tab = "cataloging";
  $nav = "upload_csv";

	include(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../functions/marcFuncs.php"));
	require_once(REL(__FILE__, "../model/MediaTypes.php"));
	require_once(REL(__FILE__, "../model/Collections.php"));

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>
<h3 id="searchHdr"><?php echo T('CSVImport'); ?></h3>

<form enctype="multipart/form-data" action="../catalog/upload_csv.php" method="post">
	<fieldset>
		<label for="radio"><?php echo T("CSVloadTest"); ?>: 
			<?php echo T("CSVloadTestTrue"); ?>
				<input type="radio" value="true" name="test" checked>  
		<?php echo T("CSVloadTestFalse"); ?>
			<input type="radio" value="false" name="test">
		</label> <br />
		<label for="csv_data"><?php echo T("CSVloadTestFileUpload"); ?>: 
			<input type="file" id="csv_data" name="csv_data">
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
		  <tr>
		  	<td colspan="3">&nbsp;</td>
  			<td colspan="1"><input type="submit" value="<?php echo T("Upload File"); ?>" class="button"></td>
  			<td colspan="1">&nbsp;</td>
		  </tr>
		</table>
	</fieldset>
</form>

<section class="note">
<p><?php echo T("CSVinputDescr"); ?></p>

<p><?php echo T("CSVimportAdvise"); ?></p>

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
    <td><pre>245$a</pre></td>
    <td>(MARC) <?php printUsmarcText("245", "a", $marcTags, $marcSubflds, FALSE); ?></td>
    <td><?php echo T("Mandatory"); ?></td>
  </tr>
  <tr>
    <td><pre>100$a</pre></td>
    <td>(MARC) <?php printUsmarcText("100", "a", $marcTags, $marcSubflds, FALSE); ?></td>
    <td><?php echo T("Mandatory"); ?></td>
  </tr>
  <tr>
    <td><pre>Coll.</pre></td>
    <td><?php echo T("Collection") ?></td>
    <td><?php echo T("CSVoptionalDefault") ?></td>
  </tr>
  <tr>
    <td><pre>mType</pre></td>
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
</section>

<?php include("../shared/footer.php"); ?>
