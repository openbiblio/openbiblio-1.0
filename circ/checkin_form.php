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
  $nav = "checkin";
  $focus_form_name = "barcodesearch";
  $focus_form_field = "barcodeNmbr";

  require_once("../shared/read_settings.php");
  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/BiblioStatus.php");
  require_once("../classes/BiblioStatusQuery.php");
  require_once("../shared/get_form_vars.php");
  require_once("../shared/header.php");

?>


<!--**************************************************************************
    *  Javascript to post checkin form
    ************************************************************************** -->
<script language="JavaScript" type="text/javascript">
<!--
function checkin(massCheckinFlg)
{
  document.checkinForm.massCheckin.value = massCheckinFlg;
  document.checkinForm.submit();
}
-->
</script>


<form name="barcodesearch" method="POST" action="../circ/shelving_cart.php">
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      Bibliography Check In:
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Barcode Number:
      <?php printInputText("barcodeNmbr",18,18,$postVars,$pageErrors); ?>
      <input type="hidden" name="mbrid" value="<?php echo $mbrid;?>">
      <input type="submit" value="Add to Shelving Cart">
    </td>
  </tr>
</table>
</form>

<?php
  if (isset($HTTP_GET_VARS["msg"])){
    echo "<font class=\"error\">";
    echo $HTTP_GET_VARS["msg"]."</font>";
  }
?>

<form name="checkinForm" method="POST" action="../circ/checkin.php">
<input type="hidden" name="massCheckin" value="N">
<a href="javascript:checkin('N')">Check in selected items</a> | 
<a href="javascript:checkin('Y')">Check in all items</a>
<table class="primary">
  <tr>
    <th valign="top" colspan="5" nowrap="yes" align="left">
      Current Shelving Cart List:
    </th>
  </tr>
  <tr>
    <th valign="top" nowrap="yes" align="left">
      &nbsp;
    </th>
    <th valign="top" nowrap="yes" align="left">
      Date Scanned
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
  if (!$statQ->execSelect("crt")) {
    $statQ->close();
    displayErrorPage($statQ);
  }
  if ($statQ->getRowCount() == 0) {
?>
    <td class="primary" align="center" colspan="5">
      No bibliographies are currently in shelving cart status.
    </td>
<?php
  } else {
    while ($stat = $statQ->fetchBiblioStatus()) {
?>
  <tr>
    <td class="primary" valign="top" align="center">
      <input type="checkbox" name="<?php echo $stat->getBibid();?>">
    </td>
    <td class="primary" valign="top" nowrap="yes">
      <?php echo $stat->getStatusBeginDt();?>
    </td>
    <td class="primary" valign="top" >
      <?php echo $stat->getBarcodeNmbr();?>
    </td>
    <td class="primary" valign="top" >
      <?php echo $stat->getTitle();?>
    </td>
    <td class="primary" valign="top" >
      <?php echo $stat->getAuthor();?>
    </td>
  </tr>
<?php
    }
  }
  $statQ->close();
?>
</table>
<a href="javascript:checkin('N')">Check in selected items</a> | 
<a href="javascript:checkin('Y')">Check in all items</a>
</form>


<?php require_once("../shared/footer.php"); ?>
