<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "working";
	$nav = "dbChkr";
	//require_once(REL(__FILE__, "../shared/logincheck.php"));

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
    <h3 id="listHdr"><?php echo T("Check Database Integrity"); ?></h3>

    <div id="editDiv">
        <fieldset id="entry">
            <form>
            	<p><?php echo T("integrityMsg");?></p>
            	<input type="button" id="chkNowBtn" value="<?php echo T("Check Now"); ?>" />
            </form>
        </fieldset>
    </div>

    <div id="rsltDiv">
	   <fieldset id="errList">
		    <ul id="rslts">
				<?php echo T("Checking..."); ?>
			</ul>

    		<form>
    			<input type="button" id="chkAgnBtn" value="<?php echo T("Recheck"); ?>" />
    			<input type="hidden" id="dummy" />
    			<input type="button" id="fixBtn" value="<?php echo T("Try to Fix Errors"); ?>" />
    		</form>
	   </fieldset>
    </div>

    <div id="msgDiv" style="display: none;"><fieldSet id="msgArea"></fieldset></div>

<?php
    require_once(REL(__FILE__,'../shared/footer.php'));

	require_once(REL(__FILE__, "../working/dbChkrJs.php"));
?>
</body>
</html>
