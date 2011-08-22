<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	$doing_install = true;
	require_once("../shared/common.php");

	Page::header_install(array('nav'=>'', 'title'=>'OpenBiblio Installation'));
	
?>
<fieldset>
	<section id="dbPblms">
		<fieldset>
		<p>The connection to the database failed with the following error.
		<pre id="connectErr"></pre>
		Please make sure the following has been done before running this
		install script.
		<ol>
			<li>create OpenBiblio database
				(<a href="../install_instructions.html#step4">step 4</a> of the
				install instructions)</li>
			<li>create OpenBiblio database user
				(<a href="../install_instructions.html#step5">step 5</a> of the
				install instructions)</li>
			<li>update openbiblio/database_constants.php with your new database
				username and password
				(<a href="../install_instructions.html#step8">step 8</a> of the install
				instructions)</li>
		</ol>
		See <a href="../install_instructions.html">Install Instructions</a> for more details.
		</p>
		</fieldset>
	</section>
	
	<section id="versionOK">
		<fieldset>
			<p>Your OpenBiblio Installation is up to date.</p>
			<p>No action is required.</p><br />
			<form method="get" action="../circ/index.php">
				<input id="useBtn" type="submit" value="Start Using OpenBiblio" />
			</form>
		</fieldset>
	</section>

	<section id="newInstall">
		<fieldset>
			<legend>New Install</legend>
			<form id="newForm">
				<table cellpadding=0 cellspacing=0 border=0>
					<tr>
						<td><label for="locale">Language:</label></td>
						<td><select id="locale"></select></td>
					</tr>
					<tr>
						<td><label for="installTestData">Install Test Data:</label></td>
						<td><input id="installTestData" type="checkbox" value="yes" /></td>
					</tr>
					<tr>
						<td><input id="newBtn" type="submit" value="Install" /></td>
					</tr>
				</table>
			</form>
		</fieldset>
	</section>
	
	<section id="plsWait">
		<fieldset>
		<table>
		<tr>
			<th colspan="1">Building OpenBiblio tables</th>
			<td rowspan="3"><img src="<?php echo REL(__FILES__,"../images/please_wait.gif"); ?>" /></td>
		</tr>
		<tr>
		  <td colspan="1"><span id="waitText">please wait...</span></td>
		</tr>
		<tr>
	    <td align="center" colspan="1">
	      <fieldset><?php echo T("lookup_resetInstr");?></fieldset>
			</td>
		</tr>
		</table>
		</fieldset>
	</section>

	<section id="updateDB">
			<p>It looks like we need to update  database version <span id="verTxt">($version);</span>
				to version <?php echo H(OBIB_LATEST_DB_VERSION); ?>:</p>
			<fieldset>
				<p class="error">WARNING - Please back up your database before updating.</p>
				<form name="updateForm" method="POST" action="../install/update.php">
					<input id="updtBtn" type="submit" value="Update">
				</form>
			</fieldset>
	</section>

	<section id="startOB">
		<fieldset>
			<p>OpenBiblio appears ready for use.</p>
				<form name="startForm" method="POST" action="../index.php">
					<input id="startBtn" type="submit" value="Start OpenBiblio now">
				</form>
		</fieldset>
	</section>
</fieldset>

<?php
	require_once($ThemeDirUrl."/footer.php");
	
	require_once(REL(__FILE__, "indexJs.php"));
?>	
