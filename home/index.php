<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  $tab = "home";
  $nav = "home";

  require_once("../shared/header.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
?>
<h1><?php echo $loc->getText("indexHeading"); ?></h1>

<?php
# echo $loc->getText("searchResults",array("items"=>0))."<br>";
?>

<?php echo $loc->getText("indexIntro"); ?>

<br><br>
<table class="primary">
  <tr>
    <th><?php echo $loc->getText("indexTab"); ?></th><th align="left"><?php echo $loc->getText("indexDesc"); ?></th>
  </tr>
  <tr>
    <td align="center" valign="top" class="primary"><?php echo $loc->getText("indexCirc"); ?><br><br>
      <img src="../images/circ.png" border="0" width="30" height="30"></td>
    <td class="primary"><?php echo $loc->getText("indexCircDesc1"); ?>
      <ul>
        <li><?php echo $loc->getText("indexCircDesc2"); ?></li>
        <li><?php echo $loc->getText("indexCircDesc3"); ?></li>
        <li><?php echo $loc->getText("indexCircDesc4"); ?></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td align="center"  valign="top" class="primary"><?php echo $loc->getText("indexCat"); ?><br><br>
      <img src="../images/catalog.png" border="0" width="30" height="30"><br><br></td>
    <td valign="top" class="primary"><?php echo $loc->getText("indexCatDesc1"); ?>
      <ul>
        <li><?php echo $loc->getText("indexCatDesc2"); ?></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td align="center"  valign="top" class="primary"><?php echo $loc->getText("indexAdmin"); ?><br><br>
      <img src="../images/admin.png" border="0" width="30" height="30"></td>
    <td class="primary"><?php echo $loc->getText("indexAdminDesc1"); ?>

      <ul>
        <li><?php echo $loc->getText("indexAdminDesc2"); ?></li>
        <li><?php echo $loc->getText("indexAdminDesc3"); ?></li>
        <li><?php echo $loc->getText("indexAdminDesc4"); ?></li>
        <li><?php echo $loc->getText("indexAdminDesc5"); ?></li>
        <li><?php echo $loc->getText("indexAdminDesc6"); ?></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td align="center"  valign="top" class="primary"><?php echo $loc->getText("indexReports"); ?><br><br>
      <img src="../images/reports.png" border="0" width="30" height="30"><br><br></td>
    <td class="primary" valign="top"><?php echo $loc->getText("indexReportsDesc1"); ?>

      <ul>
        <li><?php echo $loc->getText("indexReportsDesc2"); ?></li>
        <li><?php echo $loc->getText("indexReportsDesc3"); ?></li>
      </ul>
    </td>
  </tr>
</table>

<?php include("../shared/footer.php"); ?>
