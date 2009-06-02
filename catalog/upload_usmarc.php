<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

$tab = "cataloging";
$nav = "upload_usmarc";

# Big uploads take a while
set_time_limit(120);

require_once(REL(__FILE__, "../model/Biblios.php"));
require_once(REL(__FILE__, "../classes/Marc.php"));
require_once(REL(__FILE__, "../classes/MarcQuery.php"));
require_once(REL(__FILE__, "../classes/Cart.php"));

require_once(REL(__FILE__, "../shared/logincheck.php"));

if (count($_FILES) == 0) {
  header("Location: ../catalog/upload_usmarc_form.php");
  exit();
}

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

$f = @fopen($_FILES["usmarc_data"]["tmp_name"], rb);
assert($f);
$p = new MarcParser();
$biblios = new Biblios();
$cart = new Cart('bibid');
$nrecs = 0;

$opac_flg = (isset($_POST['opac']) && $_POST['opac'] == 'Y') ? 'Y' : 'N';

while($buf = fread($f, 8192)) {
  $err = $p->parse($buf);
  if (is_a($err, 'MarcParseError')) {
    echo '<p class="error">'.T("Bad MARC record, giving up: %err%", array('err'=>$err->toStr())).'</p>';
    break;
  }
  foreach ($p->records as $rec) {
    if ($_POST["test"]=="true") {
      echo '<p><pre>';
      echo $rec->getMnem();
      echo '</pre></p>';
      continue;
    }
    $biblio = array(
    	'last_change_userid' => $_SESSION["userid"],
    	'material_cd' => $_POST["materialCd"],
    	'collection_cd' => $_POST["collectionCd"],
    	'opac_flg' => $opac_flg,
    	'marc' => $rec,
    );
    $bibid = $biblios->insert($biblio);
    $cart->add($bibid);
    $nrecs += 1;
  }
  $p->records = array();
}
fclose($f);

echo '<p>'.T("Records imported: %rec%", array('rec'=>$nrecs)).'</p>';
echo '<p>'.T("Records added to %url%Cart", array('url'=>'<a href="../shared/req_cart.php?tab='.HURL($tab).'">')).'</a></p>';

Page::footer();
