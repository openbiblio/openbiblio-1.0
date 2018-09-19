<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
	$doing_install = true;
	require_once("../shared/common.php");

	Page::header_install(array('nav'=>'', 'title'=>'OpenBiblio Installation'));
?>
	<fieldset id="progress">
		<ul id="progressList">
		</ul>
	</fieldset>
	
	<fieldset id="action">
		<section id="plsWait">
			<h3 id="waitMsg">Building OpenBiblio tables</h3>
			<img src="<?php echo "/images/please_wait.gif"; ?>" />
			<span id="waitText">please wait...</span>
		</section>

		<section id="dbPblms">
			<!--pre id="connectErr"></pre>
			Please make sure the following has been done before re-running this install script.
			<ol>
				<li>create OpenBiblio database
					(<a href="../install_instructions.html#step4">step 4</a> of the
					install instructions)</li>
				<li>create OpenBiblio database user
					(<a href="../install_instructions.html#step5">step 5</a> of the
					install instructions)</li>
				<li>update openbiblio/dbParams.php with your new database
					username and password
					(<a href="../install_instructions.html#step8">step 8</a> of the install
					instructions)</li>
			</ol>
			See <a href="../install_instructions.html">Install Instructions</a> for more details.
			</p-->
		</section>
	
		<section id="versionOK">
			<p>Your OpenBiblio Installation is up to date.</p>
			<p>Nothing further is required.</p><br />
			<form role="form" method="get" action="../index.php">
				<input id="useBtn" type="submit" value="<?php echo T("Start Using OpenBiblio"); ?>" />
			</form>
		</section>

		<section id="newInstall">
			<p>New Install</p>
			<p class="note">If you wish test data installed, click the check box<br>
			   If not, leave it un-checked. (TEST DATA NOT YET AVAILABLE)
			</p>
			<form role="form" id="newForm">
				<table cellpadding=0 cellspacing=0 border=0>
				<!--tr>
					<td><label for="locale">Language:</label></td>
					<td><select id="locale"></select></td>
				</tr-->
				<tr>
					<th><label for="installTestData"><?php echo T("Install Test Data:"); ?></label></th>
					<td><input id="installTestData" type="checkbox" value="<?php echo T("Yes"); ?>" /></td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td><input id="newBtn" type="submit" value="<?php echo T("Install"); ?>" /></td>
				</tr>
				</table>
			</form>
		</section>
	
		<section id="updateDB">
			<p><?php echo T("The existing database is for version "); ?><span id="verTxt"></span>.<br />
				 <?php echo T("It needs to be upgraded to version "); ?> <?php echo H(OBIB_LATEST_DB_VERSION); ?>.
			</p>
			<br />
			<p class="error">WARNING - Please back up your database before updating.</p>
			<form role="form" name="updateForm">
				<input id="updtBtn" type="submit" value="<?php echo T("Update Now"); ?>">
			</form>
		</section>

		<section id="startOB">
			<p><?php echo T("OpenBiblio appears to be ready for use."); ?></p>
			<form role="form" name="startForm" >
				<input id="startBtn" type="submit" value="<?php echo T("Start OpenBiblio now"); ?>">
			</form>
		</section>
	</fieldset>

<?php
	//require_once($ThemeDirUrl."/footer.php");
	require_once("../shared/footer.php");
	
	require_once(REL(__FILE__, "../install/indexJs.php"));
