<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$nav = "staff";

	require_once(REL(__FILE__, "../model/Staff.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	$staff = new Staff;

?>
<h3><?php echo T("Staff Members"); ?></h3>
<a href="../admin/staff_new_form.php?reset=Y"><?php echo T("Add New Staff Member"); ?></a><br /><br />
<fieldset>
<table class="primary">
	<thead>
	<tr>
		<th colspan="3" rowspan="2" valign="top">
			<?php echo T("Function"); ?>
		</th>
		<th rowspan="2" valign="top" nowrap="yes">
			<?php echo T("Last Name"); ?>
		</th>
		<th rowspan="2" valign="top" nowrap="yes">
			<?php echo T("First Name"); ?>
		</th>
		<th rowspan="2" valign="top">
			<?php echo T("Userid"); ?>
		</th>
		<th colspan="6">
			<?php echo T("Authorization"); ?>
		</th>
		<th rowspan="2" valign="top">
			<?php echo T("Suspended"); ?>
		</th>
	</tr>
	<tr>
		<th>
			<?php echo T("Circ"); ?>
		</th>
		<th>
			<?php echo T("Member"); ?>
		</th>
		<th>
		 <?php echo T("Catalog"); ?>
		</th>
		<th>
		 <?php echo T("Reports"); ?>
		</th>
		<th>
			<?php echo T("Admin"); ?>
		</th>
		<th>
			<?php echo T("Tools"); ?>
		</th>
	</tr>
	</thead>
	
	<tbody class="striped">
	<?php
		$row_class = "primary";
		$staff_list = $staff->getAll('last_name');
		while ($s = $staff_list->next()) {
	?>
	<tr>
		<td valign="top" class="<?php echo $row_class; ?>">
			<a href="../admin/staff_edit_form.php?UID=<?php echo HURL($s['userid']); ?>" class="<?php echo $row_class;?>"><?php echo T("edit"); ?></a>
		</td>
		<td valign="top" class="<?php echo $row_class; ?>">
			<a href="../admin/staff_pwd_reset_form.php?UID=<?php echo HURL($s['userid']); ?>" class="<?php echo $row_class; ?>"><?php echo T("pwd"); ?></a>
		</td>
		<td valign="top" class="<?php echo $row_class; ?>">
			<a href="../admin/staff_del_confirm.php?UID=<?php echo HURL($s['userid']); ?>&amp;LAST=<?php echo HURL($s['last_name']); ?>&amp;FIRST=<?php echo HURL($s['first_name']); ?>" class="<?php echo $row_class; ?>"><?php echo T("del"); ?></a>
		</td>
		<td valign="top" class="<?php echo $row_class; ?>">
			<?php echo H($s['last_name']);?>
		</td>
		<td valign="top" class="<?php echo $row_class; ?>">
			<?php echo H($s['first_name']);?>
		</td>
		<td valign="top" class="<?php echo $row_class; ?>">
			<?php echo H($s['username']);?>
		</td>
		<td valign="top" class="<?php echo $row_class; ?>">
			<?php echo H($s['circ_flg']);?>
		</td>
		<td valign="top" class="<?php echo $row_class; ?>">
			<?php echo H($s['circ_mbr_flg']);?>
		</td>
		<td valign="top" class="<?php echo $row_class; ?>">
			<?php echo H($s['catalog_flg']);?>
		</td>
		<td valign="top" class="<?php echo $row_class; ?>">
			<?php echo H($s['reports_flg']);?>
		</td>
		<td valign="top" class="<?php echo $row_class; ?>">
			<?php echo H($s['admin_flg']);?>
		</td>
		<td valign="top" class="<?php echo $row_class; ?>">
			<?php echo H($s['tools_flg']);?>
		</td>
		<td valign="top" class="<?php echo $row_class; ?>">
			<?php echo H($s['suspended_flg']); ?>
		</td>
	</tr>
	<?php } ?>
	</tbody>
</table>
</fieldset>
<?php

	Page::footer();
