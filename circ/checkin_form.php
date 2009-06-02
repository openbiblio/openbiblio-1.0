<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  $tab = "circulation";
  $nav = "checkin";
  $helpPage = "checkin";
  $focus_form_name = "barcodesearch";
  $focus_form_field = "barcodeNmbr";

/*  function getmicrotime(){ 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec); 
  } 
  $startTm = getmicrotime();
*/

/*
  $endTm = getmicrotime();
  trigger_error ("read_settings: start=".$startTm." end=".$endTm." diff=".($endTm - $startTm),E_USER_NOTICE);
  $startTm = getmicrotime();
*/

  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/BiblioSearch.php");
  require_once("../classes/BiblioSearchQuery.php");
  require_once("../shared/get_form_vars.php");
  require_once("../shared/header.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

/*
  $endTm = getmicrotime();
  trigger_error ("Header: start=".$startTm." end=".$endTm." diff=".($endTm - $startTm),E_USER_NOTICE);
  $startTm = getmicrotime();
*/
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
      <?php echo $loc->getText("checkinFormHdr1"); ?>
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo $loc->getText("checkinFormBarcode"); ?>
      <?php printInputText("barcodeNmbr",18,18,$postVars,$pageErrors); ?>
        <a href="javascript:popSecondaryLarge('../opac/index.php?lookup=Y')"><?php echo $loc->getText("indexSearch"); ?></a>
      <input type="hidden" name="mbrid" value="<?php echo H($mbrid);?>">
      <input type="submit" value="<?php echo $loc->getText("checkinFormShelveButton"); ?>" class="button">
    </td>
  </tr>
</table>
</form>

<?php
  if (isset($_GET["msg"])){
    echo "<font class=\"error\">";
    echo H($_GET["msg"])."</font>";
  }
?>

<form name="checkinForm" method="POST" action="../circ/checkin.php">
<input type="hidden" name="massCheckin" value="N">
<a href="javascript:checkin('N')"><?php echo $loc->getText("checkinFormCheckinLink1"); ?></a> | 
<a href="javascript:checkin('Y')"><?php echo $loc->getText("checkinFormCheckinLink2"); ?></a><br><br>
<table class="primary">
  <tr>
    <th valign="top" colspan="5" nowrap="yes" align="left">
      <?php echo $loc->getText("checkinFormHdr2"); ?>
    </th>
  </tr>
  <tr>
    <th valign="top" nowrap="yes" align="left">
      &nbsp;
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("checkinFormColHdr1"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("checkinFormColHdr2"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("checkinFormColHdr3"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("checkinFormColHdr4"); ?>
    </th>
  </tr>

<?php
  #****************************************************************************
  #*  Search database for biblio copy data
  #****************************************************************************
  $biblioQ = new BiblioSearchQuery();
  $biblioQ->connect();
  if ($biblioQ->errorOccurred()) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }
  if (!$biblioQ->doQuery(OBIB_STATUS_SHELVING_CART)) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }
  if ($biblioQ->getRowCount() == 0) {
?>
    <td class="primary" align="center" colspan="5">
      <?php echo $loc->getText("checkinFormEmptyCart"); ?>
    </td>
<?php
  } else {
    while ($biblio = $biblioQ->fetchRow()) {
?>
  <tr>
    <td class="primary" valign="top" align="center">
      <input type="checkbox" name="bibid=<?php echo HURL($biblio->getBibid());?>&amp;copyid=<?php echo HURL($biblio->getCopyid());?>" value="copyid">
    </td>
    <td class="primary" valign="top" nowrap="yes">
      <?php echo H($biblio->getStatusBeginDt());?>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($biblio->getBarcodeNmbr());?>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($biblio->getTitle());?>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($biblio->getAuthor());?>
    </td>
  </tr>
<?php
    }
  }
  $biblioQ->close();
?>
</table>
<br>
<a href="javascript:checkin('N')"><?php echo $loc->getText("checkinFormCheckinLink1"); ?></a> | 
<a href="javascript:checkin('Y')"><?php echo $loc->getText("checkinFormCheckinLink2"); ?></a>
</form>


<?php require_once("../shared/footer.php");

/*
  $endTm = getmicrotime();
  trigger_error ("Footer: start=".$startTm." end=".$endTm." diff=".($endTm - $startTm),E_USER_NOTICE);
*/
 ?>
