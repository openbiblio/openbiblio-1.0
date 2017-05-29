<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$nav = "collections";
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
    <h3><?php echo T("Collections"); ?></h3>

    <div id="listDiv" style="display: none;">
        <h5 id="updateMsg"></h5>
        <form id="showForm" name="showForm">
        <input type="button" class="newBtn" value="<?php echo T("Add New"); ?>" />
        <fieldset>
        <table id="showList">
        <thead>
        	<tr>
        	    <th colspan="1">&nbsp;</th>
        		<th valign="top"><?php echo T("Code"); ?></th>
        		<th valign="top"><?php echo T("Description"); ?></th>
        		<th valign="top"><?php echo T("Type"); ?></th>
        		<th valign="top"><?php echo T("Item Count"); ?></th>
        		<th valign="top"><?php echo T("Default"); ?></th>
        	</tr>
        </thead>
        <tbody class="striped">
        	<tr><td colspan="4"><?php echo T("No collections have been defined."); ?> </td></tr>
        </tbody>
        </table>
        </fieldset>
        <input type="submit" class="newBtn" value="<?php echo T("Add New"); ?>" />
        </form>
    </div>

    <div id="editDiv" style="display: none;">
        <form id="editForm" name="editForm">
        <h5 id="reqdNote">*<?php echo T("Required note"); ?></h5>
        <fieldset>
        	<legend id="editHdr"></legend>
        	<ul id="editTbl">
            <li>
              <label for="description"><?php echo T("Description"); ?>:</label>
              <input id="description" name="description" type="text" size="32" required aria-required="true" />
        			<span class="reqd">*</span>
        		</li>
        		<li>
        			<label for "onHand"><?php echo T("Item Count"); ?>:</label><span id="onHand">0</span>
        		</li>
            <li>
              <label for="type"><?php echo T("Collection Type"); ?>:</label>
              <select id="type" name="type" > </select>
            </li>
            <li>
              <label><?php echo T("Default"); ?>:</label>
              <label for="default_Y">Y:<label>
              <input id="default_Y" name="default_flg" type="radio" value="Y" required aria-required="true" />
              <label for="default_N">N:</label>
              <input id="default_N" name="default_flg" type="radio" value="N" checked required aria-required="true" />
        			<span class="reqd">*</span>
            </li>
            <li>
              <label for="due_date_calculator" class="circOnly"><?php echo T("Due date calculator"); ?>:</label>
              <select id="due_date_calculator" name="due_date_calculator" class="circOnly"> </select>
        			<span class="reqd circOnly">*</span>
            </li>
            <li class="calculator-simple calculator-at_midnight calculator-before_we_close">
              <label for="days_due_back" class="circOnly"><?php echo T("Days Due Back"); ?>:</label>
              <input id="days_due_back" name="days_due_back" class="circOnly" type="number" size="3" min="0" max="365" required aria-required="true" />
        			<span class="reqd circOnly">*</span>
            </li>
            <li class="calculator-simple calculator-at_midnight calculator-before_we_close">
              <label for="minutes_due_back" class="circOnly"><?php echo T("Minutes due back"); ?>:</label>
              <input id="minutes_due_back" name="minutes_due_back" class="circOnly" type="number" size="4" min="0" max="1439" required aria-required="true" />
        			<span class="reqd circOnly">*</span>
            </li>
            <li class="calculator-before_we_close">
              <label for="pre_closing_padding" class="circOnly"><?php echo T("Pre-closing padding"); ?>:</label>
              <input id="pre_closing_padding" name="pre_closing_padding" class="circOnly" type="number" size="4" min="0" max="60" />
            </li>
            <li>
              <label for="important_date" class="circOnly"><?php echo T("Important date"); ?>:</label>
              <input id="important_date" name="important_date" class="circOnly" type="datetime"  />
            </li>
            <li>
              <label for="important_date_purpose" class="circOnly"><?php echo T("Important date purpose"); ?>:</label>
              <select id="important_date_purpose" name="important_date_purpose" class="circOnly" ></select>
            </li>
            <li>
              <label for="regular_late_fee" class="circOnly"><?php echo T("Regular late fee"); ?>:</label>
              <input id="regular_late_fee" name="regular_late_fee" class="circOnly" type="number" size="5" min="0" max="99.99" required aria-required="true" />
        			<span class="reqd circOnly">*</span>
        		</li>
            <li>
            <li>
              <label for="number_of_minutes_between_fee_applications" class="circOnly"><?php echo T("Minutes between fee applications"); ?>:</label>
              <input id="number_of_minutes_between_fee_applications" name="number_of_minutes_between_fee_applications" class="circOnly" type="number" size="4" min="0" max="1439" required aria-required="true" />
        			<span class="reqd circOnly">*</span>
            </li>
            <li>
              <label for="number_of_minutes_in_grace_period" class="circOnly"><?php echo T("Minutes in grace period"); ?>:</label>
              <input id="number_of_minutes_in_grace_period" name="number_of_minutes_in_grace_period" class="circOnly" type="number" size="4" min="0" max="60" />
            </li>
              <label for="restock_threshold" class="distOnly"><?php echo T("Restock amount"); ?>:</label>
              <input id="restock_threshold" name="restock_threshold" class="distOnly" type="number" size="2" min="1" max="99" required aria-required="true" />
        			<span class="reqd distOnly">*</span>
        		</li>
            <li>
        			<input type="hidden" id="mode" name="mode" value="">
        			<input type="hidden" id="cat" name="cat" value="collect">
        			<input type="hidden" id="code" name="code" value="">
        		</li>
        	</ul>
        	<ul id="btnRow">
            <li><input type="submit" id="addBtn" class="actnBtns" value="<?php echo T("Add"); ?>" /></li>
            <li><input type="submit" id="updtBtn" class="actnBtns" value="<?php echo T("Update"); ?>" /></li>
            <li><input type="button" id="cnclBtn" value="<?php echo T("Cancel"); ?>" /></li>
            <li><input type="submit" id="deltBtn" class="actnBtns" value="<?php echo T("Delete"); ?>" /></li>
        	</ul>
        </fieldset>
        </form>

    <div id="msgDiv" style="display: none;"><fieldSet id="userMsg"></fieldset></div>

    <p class="note">
    	<?php echo T("Note");?>:<br /><?php echo T("collectionsListNoteMsg"); ?>
    </p>
    <br />
    <p class="note circOnly">
    	<?php echo T("Note"); ?>:<br /><?php echo T("Setting zero days no checkout"); ?>
    </p>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	
	//require_once(REL(__FILE__, "../classes/AdminJs.php"));
	//require_once(REL(__FILE__, "collectionsJs.php"));
	require_once(REL(__FILE__, "../classes/JSAdmin.php"));
	require_once(REL(__FILE__, "../admin/collectionsJs6.php"));
?>
</body>
</html>
