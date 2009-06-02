<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "cataloging";
  $nav = "biblio/delete";
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  require_once(REL(__FILE__, "../model/Biblios.php"));
  require_once(REL(__FILE__, "../model/Copies.php"));
  require_once(REL(__FILE__, "../model/Holds.php"));

  $bibid = $_GET["bibid"];

  $biblios = new Biblios();
  $biblio = $biblios->getOne($bibid);
  $title = $biblio['marc']->getValue('245$a').' '.$biblio['marc']->getValue('245$b');

  #****************************************************************************
  #*  Check for copies and holds
  #****************************************************************************
  $copies = new Copies;
  $all_copies = $copies->getMatches(array('bibid'=>$bibid));
  $copyCount = $all_copies->count();

  $holds = new Holds();
  $all_holds = $holds->getMatches(array('bibid'=>$bibid));
  $holdCount = $all_holds->count();

  #**************************************************************************
  #*  Show confirm page
  #**************************************************************************
  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

  if (($copyCount > 0) or ($holdCount > 0)) {
?>
<center>
  <?php echo T("biblioDelConfirmWarn",array("copyCount"=>$copyCount,"holdCount"=>$holdCount)); ?>
  <br /><br />
  <a href="../shared/biblio_view.php?bibid=<?php echo $bibid; ?>"><?php echo T("Return to item information"); ?></a>
</center>

<?php
  } else {
    $delurl = '../catalog/biblio_del.php?bibid='.U($bibid).'&title='.U($title);
    if (isset($_REQUEST[rpt])) {
      $delurl .= '&rpt='.U($_REQUEST['rpt']);
    }
?>
<center>
<form name="delbiblioform" method="post" action="../shared/biblio_view.php?bibid=<?php echo $bibid;?>">
<?php echo T("biblioDelConfirmMsg",array("title"=>$title)); ?>
<br /><br />
      <input type="button" onclick="parent.location='<?php echo H($delurl) ?>'" value="<?php echo T("Delete"); ?>" class="button" />
      <input type="submit" value="<?php echo T("Cancel"); ?>" class="button" />
</form>
</center>
<?php
  }
  Page::footer();
