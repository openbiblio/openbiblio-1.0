<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "cataloging";
  $nav = "biblio/holds";
  require_once(REL(__FILE__, "../shared/logincheck.php"));

  require_once(REL(__FILE__, "../classes/Report.php"));

  #****************************************************************************
  #*  Get Status Message
  #****************************************************************************
  if (isset($_GET["msg"])) {
    $msg = '<p class="error">'.stripslashes($_GET["msg"]).'</p><br /><br />';
  } else {
    $msg = "";
  }

  $holds = Report::create(holds);
  $holds->init(array('bibid'=>$_GET['bibid']));

  #****************************************************************************
  #*  Show page
  #****************************************************************************
  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<h1><?php echo T("Hold Requests"); ?></h1>
<?php echo $msg ?>
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      <?php echo T("Function"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo T("Copy"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo T("Placed On Hold"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo T("Member"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo T("Status"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo T("Due Back"); ?>
    </th>
  </tr>
<?php


  if ($holds->count() == 0) {
?>
    <td class="primary" align="center" colspan="6">
      <?php echo T("No copies on hold"); ?>
    </td>
<?php
  } else {
    while ($hold = $holds->each()) {
?>
  <tr>
    <td class="primary" valign="top" nowrap="yes">
      <a href="../shared/hold_del_confirm.php?bibid=<?php echo HURL($hold['bibid']); ?>&amp;holdid=<?php echo HURL($hold['holdid']); ?>"><?php echo T("del"); ?></a>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($hold['barcode_nmbr']); ?>
    </td>
    <td class="primary" valign="top" nowrap="yes">
      <?php echo H($hold['hold_begin']); ?>
    </td>
    <td class="primary" valign="top" >
      <a href="../circ/mbr_view.php?mbrid=<?php echo HURL($hold['mbrid']); ?>&amp;reset=Y"><?php echo H($hold['member']); ?></a>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($hold['status']); ?>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($hold['due']); ?>
    </td>
  </tr>
<?php
    }
  }
?>

</table>

<?php

  Page::footer();
