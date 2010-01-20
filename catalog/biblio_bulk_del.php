<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = "cataloging";
$nav = "bulk_delete";
$restrictInDemo = true;
require_once(REL(__FILE__, "../shared/logincheck.php"));
require_once(REL(__FILE__, "../functions/inputFuncs.php"));
require_once(REL(__FILE__, "../model/Copies.php"));
require_once(REL(__FILE__, "../model/Biblios.php"));


function getForm($vars) {
	foreach (array_keys($vars) as $k) {
		if (isset($_REQUEST[$k])) {
			$vars[$k] = $_REQUEST[$k];
		}
	}
	return $vars;
}

$copies = new Copies;
$biblios = new Biblios;

$form = getForm(array(
	'posted' => false,
	'confirmed' => false,
	'del_items' => false,
	'del_copyids' => array(),
	'del_bibids' => array(),
	'barcodes' => '',
));
if (!$form['posted']) {
	showForm(array('del_items'=>1, 'barcodes'=>''));
} else {
	if ($form['confirmed']) {
		foreach ($form['del_copyids'] as $copyid) {
			$copies->deleteOne($copyid);
		}
		foreach ($form['del_bibids'] as $bibid) {
			$biblios->deleteOne($bibid);
		}
		$msg = T("%copy% copies and %item% items deleted.", array('copy'=>count($form['del_copyids']), 'item'=>count($form['del_bibids'])));
		header("Location: ../catalog/biblio_bulk_del.php?msg=".U($msg));
	} else {
		doConfirm($form['barcodes'], $form['del_items']);
	}
}

function doConfirm($barcode_list, $del_items) {
	global $copies;
	$barcodes = array();
	foreach (explode("\n", $barcode_list) as $b) {
		if (trim($b) != "") {
			$barcodes[] = trim($b);
		}
	}
	list($del_copyids, $bibids, $errors) = $copies->lookupBulk_el($barcodes);
	if ($errors) {
		showForm(array('del_items'=>$del_items, 'barcodes'=>$barcode_list), $errors);
		exit(0);
	}
	if ($del_items) {
		$del_bibids = $copies->lookupNoCopies($bibids, $del_copyids);
	} else {
		$del_bibids = array();
	}
	showConfirm($del_copyids, $del_bibids);
}

function showConfirm($del_copyids, $del_bibids) {
	global $tab, $nav;
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
	?>
	<style>
	  form#bulkDelForm {
			text-align: center;
			}
	</style>
	<form id="bulkDelForm" method="post" action="../catalog/biblio_bulk_del.php">;
	<fieldset>
		<p>
			<?php
			  $txt1 = H(count($del_copyids)); $txt2 = H(count($del_bibids));
				echo T('biblioBulkDelWantToDel',array('copy'=>"$txt1",'item'=>"$txt2"));
			?>
		</p>';

		<?php
			echo inputfield('hidden','posted','1');
			echo inputfield('hidden','confirmed','1');
			foreach ($del_copyids as $id) {
				echo inputfield('hidden','del_copyids[]',H($id));
			}
			foreach ($del_bibids as $id) {
				echo inputfield('hidden','del_bibids[]',H($id));
			}
		?>
	
		<input type="submit" class="button" value="<?php echo T("Delete");?>" />
		<a href="../catalog/biblio_bulk_del.php" class="small_button"><?php echo T("Cancel");?></a>
	</fieldset>
	</form>
	<?php
	Page::footer();
}

function showForm($vars, $errors=array()) {
	global $tab, $nav;
	$focus_form_name=bulk_delete;
	$focus_form_field=barcodes;
	
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
	
	echo "<h3>".T('Bulk Delete')."</h3>\n";
	if (isset($_REQUEST['msg'])) {
		echo '<p class="error">'.H($_REQUEST['msg']).'</p>';
	}
	foreach ($errors as $e) {
		echo '<p class="error">'.H($e->toStr()).'</p><br />';
	}
	?>
	
	<form name="bulk_delete" method="post" action="../catalog/biblio_bulk_del.php">
	<fieldset>
		<legend><?php echo T("Enter barcodes to delete below, one per line."); ?></legend>
		<!--input type="hidden" name="posted" value="1" /-->
		<?php echo inputfield('hidden','posted','1'); ?>
		<!--textarea name="barcodes" rows="12">'.H($vars['barcodes']).'</textarea-->
		<?php echo inputfield('textarea','barcodes','',array('rows'=>'12'),H($vars['barcodes'])); ?>
		<br />
		<label for="del_items">
			<!--input type="checkbox" name="del_items" value="1"
							<?php //if ($vars['del_items']) echo 'checked="checked" '; ?>
							/>'.T("Delete items if all copies are deleted.") -->
			<?php echo inputfield('checkbox','del_items','1','',($vars['del_items']?'1':'')); ?>
			<?php echo T("Delete items if all copies are deleted.") ?>
		</label>
		<br />
		<input type="submit" class="button" value="<?php echo T("Submit");?>" />
	</fieldset>
	</form>
	
	<?php
	Page::footer();
}
