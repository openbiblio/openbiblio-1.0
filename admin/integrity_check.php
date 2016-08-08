<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$nav = "integrity";

	//require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../classes/Integrity.php"));


	$fix = false;
	if (isset($_POST['fix']) and $_POST['fix']) {
		$fix = true;
	}
	$integrity = new Integrity;
	$errors = $integrity->check_el($fix);

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
	<h3><?php echo T("Checking Database Integrity"); ?></h3>

	<fieldset id="errList">
<?php
	if (empty($errors)) {
		echo '<p>'.T("No errors found").'</p>';
	} else {
		echo '<ul>';
		foreach ($errors as $e) {
			echo '<li>'.$e->toStr().'</li>';
		}
		echo '</ul>';
	}
?>

	<table>
	<tr>
		<td>
			<form method="post" action="../admin/integrity_check.php">
				<input type="submit" class="button" value="<?php echo T("Recheck"); ?>" />
			</form>
		</td>

<?php if (!empty($errors)) { ?>
		<td>
			<form method="post" action="../admin/integrity_check.php">
				<input type="hidden" name="fix" value="1" />
				<input type="submit" class="button" value="<?php echo T("Try to Fix Errors"); ?>" />
			</form>
		</td>
<?php } ?>

	</tr>
	</table>
	</fieldset>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
?>	

</body>
</html>
