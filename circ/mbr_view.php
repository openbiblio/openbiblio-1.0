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

  require_once("../shared/common.php");
  require_once("../functions/inputFuncs.php");
  require_once("../functions/formatFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/Member.php");
  require_once("../classes/MemberQuery.php");
  require_once("../classes/BiblioSearch.php");
  require_once("../classes/BiblioSearchQuery.php");
  require_once("../classes/BiblioHold.php");
  require_once("../classes/BiblioHoldQuery.php");
  require_once("../classes/MemberAccountQuery.php");
  require_once("../classes/DmQuery.php");
  require_once("../shared/get_form_vars.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Checking for get vars.  Go back to form if none found.
  #****************************************************************************
  if (count($_GET) == 0) {
    header("Location: ../circ/index.php");
    exit();
  }

  #****************************************************************************
  #*  Retrieving get var
  #****************************************************************************
  $mbrid = $_GET["mbrid"];
  if (isset($_GET["msg"])) {
    $msg = "<font class=\"error\">".stripslashes($_GET["msg"])."</font><br><br>";
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
  $dmQ->execSelect("biblio_status_dm");
  $biblioStatusDm = $dmQ->fetchRows();
  $dmQ->execSelect("material_type_dm");
  $materialTypeDm = $dmQ->fetchRows();
  // reseting row to top of same result set to get image_file.  This avoids having to do another select.
  $dmQ->resetResult();
  $materialImageFiles = $dmQ->fetchRows("image_file");
  $dmQ->close();

  #****************************************************************************
  #*  Check for outstanding balance due
  #****************************************************************************
  $acctQ = new MemberAccountQuery();
  $acctQ->connect();
  if ($acctQ->errorOccurred()) {
    $acctQ->close();
    displayErrorPage($acctQ);
  }
  $balance = $acctQ->getBalance($mbrid);
  if ($acctQ->errorOccurred()) {
    $acctQ->close();
    displayErrorPage($acctQ);
  }
  $acctQ->close();
  $balMsg = "";
  if ($balance != 0) {
    $balText = moneyFormat($balance,2);
    $balMsg = "<font class=\"error\">".$loc->getText("mbrViewBalMsg",array("bal"=>$balText))."</font><br><br>";
  }

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
  #*  Show member information
  #**************************************************************************
  require_once("../shared/header.php");
?>

<?php echo $balMsg ?>
<?php echo $msg ?>

<table class="primary">
  <tr><td class="noborder" valign="top">
  <br>
<table class="primary">
  <tr>
    <th align="left" colspan="2" nowrap="yes">
      <?php print $loc->getText("mbrViewHead1"); ?>
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <?php print $loc->getText("mbrViewName"); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo $mbr->getLastName();?>, <?php echo $mbr->getFirstName();?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      <?php print $loc->getText("mbrViewAddr"); ?>
    </td>
    <td valign="top" class="primary">
      <?php
        if ($mbr->getAddress1() != "") echo $mbr->getAddress1()."<br>\n";
        if ($mbr->getAddress2() != "") echo $mbr->getAddress2()."<br>\n";
        if ($mbr->getCity() != "") {
          echo $mbr->getCity().", ".$mbr->getState()." ".$mbr->getZip();
          if ($mbr->getZipExt() != 0) {
            echo "-".$mbr->getZipExt()."<br>\n";
          } else {
            echo "<br>\n";
          }
        }
      ?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      <?php print $loc->getText("mbrViewCardNmbr"); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo $mbr->getBarcodeNmbr();?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      <?php print $loc->getText("mbrViewClassify"); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo $mbrClassifyDm[$mbr->getClassification()];?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      <?php print $loc->getText("mbrViewPhone"); ?>
    </td>
    <td valign="top" class="primary">
      <?php
        if ($mbr->getHomePhone() != "") {
          echo $loc->getText("mbrViewPhoneHome").$mbr->getHomePhone()." ";
        }
        if ($mbr->getWorkPhone() != "") {
          echo $loc->getText("mbrViewPhoneWork").$mbr->getWorkPhone();
        }
      ?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      <?php print $loc->getText("mbrViewEmail"); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo $mbr->getEmail();?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      <?php print $loc->getText("mbrViewGrade"); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo $mbr->getSchoolGrade();?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      <?php print $loc->getText("mbrViewTeacher"); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo $mbr->getSchoolTeacher();?>
    </td>
  </tr>
</table>

  </td>
  <td class="noborder" valign="top">

<?php
  #****************************************************************************
  #*  Show checkout stats
  #****************************************************************************
  $dmQ = new DmQuery();
  $dmQ->connect();
  if ($dmQ->errorOccurred()) {
    $dmQ->close();
    displayErrorPage($dmQ);
  }
  $dmQ->execCheckoutStats($mbr->getMbrid());
  if ($dmQ->errorOccurred()) {
    $dmQ->close();
    displayErrorPage($dmQ);
  }
?>
<?php print $loc->getText("mbrViewHead2"); ?>
<table class="primary">
  <tr>
    <th align="left">
      <?php print $loc->getText("mbrViewStatColHdr1"); ?>
    </th>
    <th align="left">
      <?php print $loc->getText("mbrViewStatColHdr2"); ?>
    </th>
    <th align="left" nowrap="yes">
      <?php print $loc->getText("mbrViewStatColHdr3"); ?>
    </th>
  </tr>
<?php
  while ($dm = $dmQ->fetchRow()) {
?>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <?php echo $dm->getDescription(); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo $dm->getCount(); ?>
    </td>
    <td valign="top" class="primary">
      <?php if ($mbr->getClassification() == "a") {
        echo $dm->getAdultCheckoutLimit();
      } else {
        echo $dm->getJuvenileCheckoutLimit();
      } ?>
    </td>
  </tr>
<?php
  }
  $dmQ->close();
?>
  </table>
</td></tr></table>

<br>
<!--****************************************************************************
    *  Checkout form
    **************************************************************************** -->
<form name="barcodesearch" method="POST" action="../circ/checkout.php">
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrViewHead3"); ?>
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php print $loc->getText("mbrViewBarcode"); ?>
      <?php printInputText("barcodeNmbr",18,18,$postVars,$pageErrors); ?>
      <input type="hidden" name="mbrid" value="<?php echo $mbrid;?>">
      <input type="hidden" name="classification" value="<?php echo $mbr->getClassification();?>">
      <input type="submit" value="<?php print $loc->getText("mbrViewCheckOut"); ?>" class="button">
    </td>
  </tr>
</table>
</form>

<h1><?php print $loc->getText("mbrViewHead4"); ?>
  <font class="primary"> <a href="javascript:popSecondary('../circ/mbr_print_checkouts.php?mbrid=<?php echo $mbrid;?>')"><?php print $loc->getText("mbrPrintCheckouts"); ?></a></font>
</h1>
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrViewOutHdr1"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrViewOutHdr2"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrViewOutHdr3"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrViewOutHdr4"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrViewOutHdr5"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrViewOutHdr6"); ?>
    </th>
    <th valign="top" align="left">
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
    <td class="primary" align="center" colspan="7">
      <?php print $loc->getText("mbrViewNoCheckouts"); ?>
    </td>
  </tr>
<?php
  } else {
    while ($biblio = $biblioQ->fetchRow()) {
?>
  <tr>
    <td class="primary" valign="top" nowrap="yes">
      <?php echo $biblio->getStatusBeginDt();?>
    </td>
    <td class="primary" valign="top">
      <img src="../images/<?php echo $materialImageFiles[$biblio->getMaterialCd()];?>" width="20" height="20" border="0" align="middle" alt="<?php echo $materialTypeDm[$biblio->getMaterialCd()];?>">
      <?php echo $materialTypeDm[$biblio->getMaterialCd()];?>
    </td>
    <td class="primary" valign="top" >
      <?php echo $biblio->getBarcodeNmbr();?>
    </td>
    <td class="primary" valign="top" >
      <a href="../shared/biblio_view.php?bibid=<?php echo $biblio->getBibid();?>"><?php echo $biblio->getTitle();?></a>
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

<br>
<!--****************************************************************************
    *  Hold form
    **************************************************************************** -->
<form name="holdForm" method="POST" action="../circ/place_hold.php">
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrViewHead5"); ?>
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php print $loc->getText("mbrViewBarcode"); ?>
      <?php printInputText("holdBarcodeNmbr",18,18,$postVars,$pageErrors); ?>
        <a href="javascript:popSecondaryLarge('../opac/index.php?lookup=Y')"><?php print $loc->getText("indexSearch"); ?></a>
      <input type="hidden" name="mbrid" value="<?php echo $mbrid;?>">
      <input type="hidden" name="classification" value="<?php echo $mbr->getClassification();?>">
      <input type="submit" value="<?php print $loc->getText("mbrViewPlaceHold"); ?>" class="button">
    </td>
  </tr>
</table>
</form>

<h1><?php print $loc->getText("mbrViewHead6"); ?></h1>
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrViewHoldHdr1"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrViewHoldHdr2"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrViewHoldHdr3"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrViewHoldHdr4"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrViewHoldHdr5"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrViewHoldHdr6"); ?>
    </th>
    <th valign="top" align="left">
      <?php print $loc->getText("mbrViewHoldHdr7"); ?>
    </th>
    <th valign="top" align="left">
      <?php print $loc->getText("mbrViewHoldHdr8"); ?>
    </th>
  </tr>
<?php
  #****************************************************************************
  #*  Search database for BiblioHold data
  #****************************************************************************
  $holdQ = new BiblioHoldQuery();
  $holdQ->connect();
  if ($holdQ->errorOccurred()) {
    $holdQ->close();
    displayErrorPage($holdQ);
  }
  if (!$holdQ->queryByMbrid($mbrid)) {
    $holdQ->close();
    displayErrorPage($holdQ);
  }
  if ($holdQ->getRowCount() == 0) {
?>
  <tr>
    <td class="primary" align="center" colspan="8">
      <?php print $loc->getText("mbrViewNoHolds"); ?>
    </td>
  </tr>
<?php
  } else {
    while ($hold = $holdQ->fetchRow()) {
?>
  <tr>
    <td class="primary" valign="top" nowrap="yes">
      <a href="../shared/hold_del_confirm.php?bibid=<?php echo $hold->getBibid();?>&copyid=<?php echo $hold->getCopyid();?>&holdid=<?php echo $hold->getHoldid();?>&mbrid=<?php echo $mbrid;?>"><?php print $loc->getText("mbrViewDel"); ?></a>
    </td>
    <td class="primary" valign="top" nowrap="yes">
      <?php echo $hold->getHoldBeginDt();?>
    </td>
    <td class="primary" valign="top">
      <img src="../images/<?php echo $materialImageFiles[$hold->getMaterialCd()];?>" width="20" height="20" border="0" align="middle" alt="<?php echo $materialTypeDm[$hold->getMaterialCd()];?>">
      <?php echo $materialTypeDm[$hold->getMaterialCd()];?>
    </td>
    <td class="primary" valign="top" >
      <?php echo $hold->getBarcodeNmbr();?>
    </td>
    <td class="primary" valign="top" >
      <a href="../shared/biblio_view.php?bibid=<?php echo $hold->getBibid();?>"><?php echo $hold->getTitle();?></a>
    </td>
    <td class="primary" valign="top" >
      <?php echo $hold->getAuthor();?>
    </td>
    <td class="primary" valign="top" >
      <?php echo $biblioStatusDm[$hold->getStatusCd()];?>
    </td>
    <td class="primary" valign="top" >
      <?php echo $hold->getDueBackDt();?>
    </td>
  </tr>
<?php
    }
  }
  $holdQ->close();
?>


</table>


<?php require_once("../shared/footer.php"); ?>
