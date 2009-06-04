<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "cataloging";
  $nav = "biblio/images";
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  require_once(REL(__FILE__, "../model/BiblioImages.php"));

  $bibid = $_REQUEST['bibid'];

  $bibimages = new BiblioImages;
  $res = $bibimages->getByBibid($bibid);
  $images = $res->toArray();
  
  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
  echo '<table>';
  for ($i=0; $i < count($images); $i++) {
    $img = $images[$i];
    echo '<tr><td rowspan="2" valign="center">';
    if (array_key_exists($i-1, $images)) {
      echo '<a href="../catalog/image_manage_action.php?action=reposition';
      echo '&amp;bibid='.HURL($img['bibid']);
      echo '&amp;imgurl='.HURL($img['imgurl']);
      echo '&amp;position='.HURL($images[$i-1]['position']).'">'.T("Raise").'</a><br />';
    }
    echo '<a href="../catalog/image_manage_action.php?action=delete';
    echo '&amp;bibid='.HURL($img['bibid']);
    echo '&amp;imgurl='.HURL($img['imgurl']).'">'.T("Delete").'</a><br />';
    if (array_key_exists($i+1, $images)) {
      echo '<a href="../catalog/image_manage_action.php?action=reposition';
      echo '&amp;bibid='.HURL($img['bibid']);
      echo '&amp;imgurl='.HURL($img['imgurl']);
      echo '&amp;position='.HURL($images[$i+1]['position']+1).'">'.T("Lower").'</a>' ;
    }
    echo '</td><td align="center" valign="center">';
    if ($img['url']) {
      echo '<a href="'.H($img['url']).'">';
    }
    echo '<img src="'.H($img['imgurl']).'" alt="'.H($img['caption']).'" />';
    if ($img['url']) {
      echo '</a>';
    }
    echo '</td></tr><tr><td align="center">';
?>
    <form method="post" action="../catalog/image_manage_action.php">
      <input type="hidden" name="bibid" value="<?php echo H($img['bibid']) ?>" />
      <input type="hidden" name="imgurl" value="<?php echo H($img['imgurl']) ?>" />
      <input type="hidden" name="action" value="update_caption" />
      <input type="text" name="caption" value="<?php echo H($img['caption']) ?>" /><br />
      <input type="submit" name="Change Caption" value="<?php echo T("Change Caption"); ?>" class="button" />
    </form>
    </td></tr>
<?php
  }
?>
</table>
<p>
<a href="../catalog/image_upload_form.php?bibid=<?php echo HURL($bibid); ?>"><?php echo T("Add New"); ?>...</a>
</p>

<?php

  Page::footer();
