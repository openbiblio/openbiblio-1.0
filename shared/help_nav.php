<?php
?>
	<h3 class="staff_head">
			<?php echo T("Help"); ?>
	</h3>
	<a href="javascript:window.close()"><?php echo T("Close Window"); ?></a>
	<hr />
	<?php $link = "../shared/help.php" . "?page="; ?>
	<ul id="nav">
	  <li><a href="<?php echo $link; ?>contents"><?php echo T("Contents"); ?></a></li>
	  <li><a href="<?php echo $link; ?>circulation" class="header"><?php echo T("Circulation"); ?></a>
	    <ul>
				<li><a href="<?php echo $link; ?>mbrSrch"><?php echo T("Member Search"); ?></a></li>
				<li><a href="<?php echo $link; ?>newMbr"><?php echo T("New Member"); ?></a></li>
				<li><a href="<?php echo $link; ?>bookings"><?php echo T("Bookings"); ?></a></li>
				<li><a href="<?php echo $link; ?>checkin"><?php echo T("Check In"); ?></a></li>
			</ul>
		</li>
	  <li><a href="<?php echo $link; ?>cataloging" class="header"><?php echo T("Cataloging"); ?></a>
	    <ul>
				<li><a href="<?php echo $link; ?>itemSrch"><?php echo T("Item Search"); ?></a></li>
				<li><a href="<?php echo $link; ?>newItem"><?php echo T("New Item"); ?></a></li>
				<li><a href="<?php echo $link; ?>reqCart"><?php echo T("Request Cart"); ?></a></li>
				<li><a href="<?php echo $link; ?>images"><?php echo T("Browse Images"); ?></a></li>
				<li><a href="<?php echo $link; ?>marcInpt"><?php echo T("MARC Import"); ?></a></li>
				<li><a href="<?php echo $link; ?>blkDelt"><?php echo T("Bulk Delete"); ?></a></li>
			</ul>
    </li>
	  <li><?php echo T("Reports"); ?></li>
	  <li><?php echo T("Admin"); ?></li>
	  <li><?php echo T("Tools"); ?></li>
	</ul>
	
	<hr />
	<a href="javascript:self.print();" class="alt1"><?php echo T("Print"); ?></a><br />
