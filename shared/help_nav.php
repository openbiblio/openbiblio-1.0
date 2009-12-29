<?php
?>
	<h3 class="staff_head">
			<?php echo T("Help"); ?>
	</h3>
	<a href="javascript:window.close()"><?php echo T("Close Window"); ?></a>
	<hr />
	
	<table id="main" height="100%" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td id="help_sidebar">
			<?php if (!isset($_GET["page"])) {
				echo "&raquo; ".T("Contents");
			} else { ?>
				<a href="../shared/help.php" class="alt1"><?php echo T("Contents"); ?></a>
			<?php } ?>
			<br />
			<a href="javascript:self.print();" class="alt1"><?php echo T("Print"); ?></a><br />
		</td>
		<td id="content">
		</td>
	</tr>
	</table>

