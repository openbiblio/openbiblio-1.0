<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$nav = "themes";

	require_once(REL(__FILE__, "../model/Themes.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	$themes = new Themes;
	$tl = $themes->getSelect();
?>

<h3><?php echo T("Themes"); ?></h3>

<form name="editthemeidform" method="post" action="../admin/theme_use.php">
<fieldset>
<legend><?php echo T("Change Theme In Use:"); ?></legend>
<table class="primary">
	<!--tr>
		<th nowrap="yes" align="left">
			<?php echo T("Change Theme In Use:"); ?>
		</th>
	</tr-->
	<tr>
		<td nowrap="true" class="primary">
			<?php echo T("Choose a New Theme:"); ?>
			<?php echo inputfield('select', 'themeid', Settings::get('themeid'), NULL, $tl); ?>
			<input type="submit" value="<?php echo T("Update"); ?>" class="button" />
		</td>
	</tr>
</table>
</fieldset>
</form>

<a href="../admin/theme_new_form.php"><?php echo T("Add New Theme"); ?></a><br />
<fieldset>
<legend><?php echo T("Available Themes"); ?></legend>
<table class="primary">
	<tr>
		<th colspan="3" valign="top">
			<sup>*</sup><?php echo T("Function"); ?>
		</th>
		<th valign="top">
			<?php echo T("Theme Name"); ?>
		</th>
		<th valign="top">
			<?php echo T("Usage"); ?>
		</th>
	</tr>
	<tbody class="striped">
	<?php
		$row_class = "primary";
		$current = Settings::get('themeid');

		foreach ($tl as $id=>$name) {
	?>
	<tr>
		<td valign="top" class="<?php echo $row_class; ?>">
			<a href="../admin/theme_edit_form.php?themeid=<?php echo HURL($id); ?>" class="<?php echo $row_class;?>"><?php echo T("edit"); ?></a>
		</td>
		<td valign="top" class="<?php echo $row_class; ?>">
			<a href="../admin/theme_new_form.php?themeid=<?php echo HURL($id); ?>" class="<?php echo $row_class;?>"><?php echo T("copy"); ?></a>
		</td>
		<td valign="top" class="<?php echo $row_class; ?>">
			<?php if ($id == $current) { echo T("del"); } else { ?>
				<a href="../admin/theme_del_confirm.php?themeid=<?php echo HURL($id); ?>&amp;name=<?php echo HURL($name); ?>" class="<?php echo $row_class; ?>"><?php echo T("del"); ?></a>
			<?php } ?>
		</td>
		<td valign="top" class="<?php echo $row_class; ?>">
			<?php echo H($name); ?>
		</td>
		<td valign="top" class="<?php echo $row_class; ?>">
			<?php if ($id == $current) { echo T("in use"); } else { echo "&nbsp;"; } ?>
		</td>
	</tr>

<?php } ?>

	</tbody>
</table>
<br />
<p class="note">
<sup>*</sup><?php echo T("Note:"); ?><br />
<?php echo T("No delete on active theme"); ?></p>
</fieldset>

<?php

	Page::footer();
