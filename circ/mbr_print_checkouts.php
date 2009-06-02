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

  $tab = "circulation";
  $nav = "view";
  $focus_form_name = "barcodesearch";
  $focus_form_field = "barcodeNmbr";

  require_once("../shared/read_settings.php");
  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/Member.php");
  require_once("../classes/MemberQuery.php");
  require_once("../classes/BiblioSearch.php");
  require_once("../classes/BiblioSearchQuery.php");
  require_once("../classes/DmQuery.php");
  require_once("../shared/get_form_vars.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Retrieving get var
  #****************************************************************************
  $mbrid = $HTTP_GET_VARS["mbrid"];
  if (isset($HTTP_GET_VARS["msg"])) {
    $msg = "<font class=\"error\">".stripslashes($HTTP_GET_VARS["msg"])."</font><br><br>";
  } else {
    $msg = "";
  }

  #****************************************************************************
  #*  Loading a few domain tables into associative arrays
  #****************************************************************************
  $dmQ = new DmQuery();
  $dmQ->connect();
  if ($dmQ->errorOccurred()) {
    $dmQ->close();
    displayErrorPage($dmQ);
  }
  $dmQ->execSelect("mbr_classify_dm");
  $mbrClassifyDm = $dmQ->fetchRows();
  $dmQ->execSelect("material_type_dm");
  $materialTypeDm = $dmQ->fetchRows();
  // reseting row to top of same result set to get image_file.  This avoids having to do another select.
  $dmQ->resetResult();
  $materialImageFiles = $dmQ->fetchRows("image_file");
  $dmQ->close();

  #****************************************************************************
  #*  Search database for member
  #****************************************************************************
  $mbrQ = new MemberQuery();
  $mbrQ->connect();
  if ($mbrQ->errorOccurred()) {
    $mbrQ->close();
    displayErrorPage($mbrQ);
  }
  if (!$mbrQ->execSelect($mbrid)) {
    $mbrQ->close();
    displayErrorPage($mbrQ);
  }
  $mbr = $mbrQ->fetchMember();
  $mbrQ->close();

  #**************************************************************************
  #*  Show member checkouts
  #**************************************************************************
?>
<html>
<head>
<style type="text/css">
  <?php include("../css/style.php");?>
</style>
<meta name="description" content="OpenBiblio Library Automation System">
<title>Checkouts for <?php echo $mbr->getFirstLastName();?></title>

</head>
<body bgcolor="<?php echo OBIB_PRIMARY_BG;?>" topmargin="5" bottommargin="5" leftmargin="5" rightmargin="5" marginheight="5" marginwidth="5" onLoad="self.focus();self.print();">

<font class="primary">
<table class="primary" width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td width="100%" class="noborder" valign="top">
      <h1><?php echo $loc->getText("mbrPrintCheckoutsTitle",array("mbrName"=>$mbr->getFirstLastName())); ?></h1>
    </td>
    <td class="noborder" valign="top" nowrap="yes"><font class="small"><a href="javascript:window.close()">Close Window</font></a>&nbsp;&nbsp;</font></td>
  </tr>
</table>
<br>
<table class="primary" width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td class="noborder" valign="top"><?php echo $loc->getText("mbrPrintCheckoutsHdr1");?></td>
    <td width="100%" class="noborder" valign="top"><?php echo $today = date("F j, Y, g:i a");?></td>
  </tr>
  <tr>
    <td class="noborder" valign="top" nowrap><?php echo $loc->getText("mbrPrintCheckoutsHdr2");?></td>
    <td class="noborder" valign="top"><?php echo $mbr->getFirstLastName();?></td>
  </tr>
  <tr>
    <td class="noborder" valign="top" nowrap><?php echo $loc->getText("mbrPrintCheckoutsHdr3");?></td>
    <td class="noborder" valign="top"><?php echo $mbr->getBarcodeNmbr();?></td>
  </tr>
  <tr>
    <td class="noborder" valign="top" nowrap><?php echo $loc->getText("mbrPrintCheckoutsHdr4");?></td>
    <td class="noborder" valign="top"><?php echo $mbrClassifyDm[$mbr->getClassification()];?></td>
  </tr>
</table>
<br>
<table class="primary">
  <tr>
    <td class="primary" valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrViewOutHdr1"); ?>
    </th>
    <td class="primary" valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrViewOutHdr2"); ?>
    </th>
    <td class="primary" valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrViewOutHdr4"); ?>
    </th>
    <td class="primary" valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrViewOutHdr5"); ?>
    </th>
    <td class="primary" valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrViewOutHdr6"); ?>
    </th>
    <td class="primary" valign="top" align="left">
      <?php print $loc->getText("mbrViewOutHdr7"); ?>
    </th>
  </tr>

<?php
  #****************************************************************************
  #*  Search database for BiblioStatus data
  #****************************************************************************
  $biblioQ = new BiblioSearchQuery();
  $biblioQ->connect();
  if ($biblioQ->errorOccurred()) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }
  if (!$biblioQ->query(OBIB_STATUS_OUT,$mbrid)) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }
  if ($biblioQ->getRowCount() == 0) {
?>
  <tr>
    <td class="primary" align="center" colspan="6">
      <?php print $loc->getText("mbrViewNoCheckouts"); ?>
    </td>
  </tr>
<?php
  } else {
    while ($biblio = $biblioQ->fetchRow()) {
?>
  <tr>
    <td class="primary" valign="top" nowrap>
      <?php echo $biblio->getStatusBeginDt();?>
    </td>
    <td class="primary" valign="top" nowrap>
      <img src="../images/<?php echo $materialImageFiles[$biblio->getMaterialCd()];?>" width="20" height="20" border="0" align="middle" alt="<?php echo $materialTypeDm[$biblio->getMaterialCd()];?>">
      <?php echo $materialTypeDm[$biblio->getMaterialCd()];?>
    </td>
    <td class="primary" valign="top" >
      <?php echo $biblio->getTitle();?>
    </td>
    <td class="primary" valign="top" >
      <?php echo $biblio->getAuthor();?>
    </td>
    <td class="primary" valign="top" nowrap="yes">
      <?php echo $biblio->getDueBackDt();?>
    </td>
    <td class="primary" valign="top" >
      <?php echo $biblio->getDaysLate();?>
    </td>
  </tr>
<?php
    }
  }
  $biblioQ->close();
?>

</table>

