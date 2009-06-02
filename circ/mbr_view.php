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
  require_once("../classes/BiblioStatus.php");
  require_once("../classes/BiblioStatusQuery.php");
  require_once("../classes/DmQuery.php");
  require_once("../shared/get_form_vars.php");

  #****************************************************************************
  #*  Checking for get vars.  Go back to form if none found.
  #****************************************************************************
  if (count($HTTP_GET_VARS) == 0) {
    header("Location: ../circ/mbr_search_form.php");
    exit();
  }

  #****************************************************************************
  #*  Retrieving get var
  #****************************************************************************
  $mbrid = $HTTP_GET_VARS["mbrid"];

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
  $mbrName = urlencode($mbr->getFirstName()." ".$mbr->getLastName());

  #**************************************************************************
  #*  Show search results
  #**************************************************************************
  require_once("../shared/header.php");

?>

<table class="primary">
  <tr><td class="noborder" valign="top">
  <br>
<table class="primary">
  <tr>
    <th align="left" colspan="2" nowrap="yes">
      Member Information:
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      Name:
    </td>
    <td valign="top" class="primary">
      <?php echo $mbr->getLastName();?>, <?php echo $mbr->getFirstName();?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      Address:
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
      Card Number:
    </td>
    <td valign="top" class="primary">
      <?php echo $mbr->getBarcodeNmbr();?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      Classification:
    </td>
    <td valign="top" class="primary">
      <?php echo $mbrClassifyDm[$mbr->getClassification()];?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      Phone:
    </td>
    <td valign="top" class="primary">
      <?php
        if ($mbr->getHomePhone() != "") {
          echo "H:".$mbr->getHomePhone()." ";
        }
        if ($mbr->getWorkPhone() != "") {
          echo "W:".$mbr->getWorkPhone();
        }
      ?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      School Grade:
    </td>
    <td valign="top" class="primary">
      <?php echo $mbr->getSchoolGrade();?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      School Teacher:
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
?>
Checkout Stats:
<table class="primary">
  <tr>
    <th align="left">
      Material
    </th>
    <th align="left">
      Count
    </th>
    <th align="left" nowrap="yes">
      Limit
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


<!--****************************************************************************
    *  Search form
    **************************************************************************** -->
<form name="barcodesearch" method="POST" action="../circ/checkout.php">
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      Bibliography Check Out:
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Barcode Number:
      <?php printInputText("barcodeNmbr",18,18,$postVars,$pageErrors); ?>
      <input type="hidden" name="mbrid" value="<?php echo $mbrid;?>">
      <input type="hidden" name="classification" value="<?php echo $mbr->getClassification();?>">
      <input type="submit" value="Check Out">
    </td>
  </tr>
</table>
</form>

<h1>Bibliographies Currently Checked Out:</h1>
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      Checked Out
    </th>
    <th valign="top" nowrap="yes" align="left">
      Material
    </th>
    <th valign="top" nowrap="yes" align="left">
      Barcode
    </th>
    <th valign="top" nowrap="yes" align="left">
      Title
    </th>
    <th valign="top" nowrap="yes" align="left">
      Author
    </th>
    <th valign="top" nowrap="yes" align="left">
      Due Back
    </th>
    <th valign="top" align="left">
      Days Late
    </th>
  </tr>

<?php
  #****************************************************************************
  #*  Search database for BiblioStatus data
  #****************************************************************************
  $statQ = new BiblioStatusQuery();
  $statQ->connect();
  if ($statQ->errorOccurred()) {
    $statQ->close();
    displayErrorPage($statQ);
  }
  if (!$statQ->execSelect("out",$mbrid)) {
    $statQ->close();
    displayErrorPage($statQ);
  }
  if ($statQ->getRowCount() == 0) {
?>
    <td class="primary" align="center" colspan="7">
      No bibliographies are currently checked out.
    </td>
<?php
  } else {
    while ($stat = $statQ->fetchBiblioStatus()) {
?>
  <tr>
    <td class="primary" valign="top" nowrap="yes">
      <?php echo $stat->getStatusBeginDt();?>
    </td>
    <td class="primary" valign="top">
      <img src="../images/<?php echo $materialImageFiles[$stat->getMaterialCd()];?>" width="20" height="20" border="0" align="middle" alt="<?php echo $materialTypeDm[$stat->getMaterialCd()];?>">
      <?php echo $materialTypeDm[$stat->getMaterialCd()];?>
    </td>
    <td class="primary" valign="top" >
      <?php echo $stat->getBarcodeNmbr();?>
    </td>
    <td class="primary" valign="top" >
      <a href="../shared/biblio_view.php?bibid=<?php echo $stat->getBibid();?>"><?php echo $stat->getTitle();?></a>
    </td>
    <td class="primary" valign="top" >
      <?php echo $stat->getAuthor();?>
    </td>
    <td class="primary" valign="top" nowrap="yes">
      <?php echo $stat->getDueBackDt();?>
    </td>
    <td class="primary" valign="top" >
      <?php echo $stat->getDaysLate();?>
    </td>
  </tr>
<?php
    }
  }
  $statQ->close();
?>

</table>


<?php require_once("../shared/footer.php"); ?>
