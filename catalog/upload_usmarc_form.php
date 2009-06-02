<?php
/**********************************************************************************
 *   Copyright(C) 2002 David Stevens
 *
 *   This file is part of OpenBiblio.
 *
 *   OpenBiblio is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   OpenBiblio is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with OpenBiblio; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 **********************************************************************************
 */

  $tab = "cataloging";
  $nav = "upload_usmarc";

  include("../shared/read_settings.php");
  include("../shared/logincheck.php");
  include("../shared/header.php");
  
  require_once("../classes/UsmarcTagDm.php");
  require_once("../classes/UsmarcTagDmQuery.php");
  require_once("../classes/UsmarcSubfieldDm.php");
  require_once("../classes/UsmarcSubfieldDmQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../catalog/inputFuncs.php");
  require_once("../functions/inputFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

?>

<form enctype="multipart/form-data" action="../catalog/upload_usmarc.php" method="post">
<?php echo $loc->getText("MarcUploadTest"); ?>: <?php echo $loc->getText("MarcUploadTestTrue"); ?><input type="radio" value="true" name="test" checked>  <?php echo $loc->getText("MarcUploadTestFalse"); ?><input type="radio" value="false" name="test"><br />
<?php echo $loc->getText("MarcUploadTestFileUpload"); ?>: <input type="file" name="usmarc_data"><br><br>

<hr />
<b>Defaults:</b>
<table border=0>
<tr><td><?php echo $loc->getText("biblioFieldsCollection"); ?>:</td><td><?php printSelect("collectionCd","collection_dm",$postVars); ?></td></tr>
<tr><td><?php echo $loc->getText("biblioFieldsMaterialTyp"); ?>:</td><td><?php printSelect("materialCd","material_type_dm",$postVars); ?></td></tr>
<tr><td><?php echo $loc->getText("biblioFieldsOpacFlg"); ?>:</td><td><SELECT name=opac id=opac><option value=Y>Yes</option><option value=N SELECTED>No</option></select></td></tr>
<tr><td colspan=2><input type=hidden name=userid id=userid value="<? echo $HTTP_SESSION_VARS["userid"]?>"></td></tr>
</table>
<input type="submit" value="Upload File" class="button">
</form>

<?php include("../shared/footer.php"); ?>
