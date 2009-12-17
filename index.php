<?php
	require_once("shared/common.php");
	require_once(REL(__FILE__, "functions/inputFuncs.php"));	
	require_once(REL(__FILE__, "model/Sites.php"));
		
	if(empty($_SESSION['current_site'])) $_SESSION['current_site'] = Settings::get('library_name');
	
	$sites_table = new Sites;		
	$sites = $sites_table->getSelect(true);
	if(sizeof($sites) == 1) header("Location: circ/index.php");

	switch($_REQUEST['action']){
		case 'Logon': header("Location: circ/index.php"); break;
		case 'OPAC': 
			if($_REQUEST['selectSite'] != 'all'){
				$_SESSION['current_site'] =  $_REQUEST['selectSite'];
				header("Location: opac/index.php");
			}
	}

?>

<body>
		<div id="content">
			<h1><? echo T('Welcome to the libary');?></h1>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="phrasesearch">
					<fieldset>
					<legend><?php T('Please select the library') ?></legend>
					<table class="primary">
						<tbody>
							<tr>
							<td class="primary" nowrap="true">
								<?php echo T('Please select the library:'); ?>
							</td><td>
								<?php echo inputfield('select', 'selectSite', 'all', NULL, $sites); 	?>								
								<input type="hidden" value="action" name="action"/>
								<input class="button" name="action" type="submit" value="OPAC"/>
							</td></tr>
							<tr>
								<td colspan="2"><hr/></td>
							</tr>
							<tr>
								<td>
									<? echo T('Clock here to logon to the staff interface'); ?>
								</td>
								<td>
									<input class="button" name="action" type="submit" value="Logon"/>
								</td>
							</td>															
							</tr>
						</tbody>
					</table>
				</fieldset>
			</form>			
		</div>
</body>

