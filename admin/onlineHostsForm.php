<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");

	session_cache_limiter(null);

  $tab = "admin";
  $nav = "onlineHosts";
  $focus_form_name = "editForm";
  $focus_form_field = "name";

  require_once(REL(__FILE__, "../functions/inputFuncs.php"));
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<h3 id="listHdr"><?php echo T("Online Hosts"); ?></h3>

<div id="listDiv" style="display: none;">
<h5 id="updateMsg"></h5>

<form role="form" id="showForm" name="showForm">
<input type="button" class="newBtn" value="<?php echo T("Add New"); ?>" />
<fieldset>
<legend id="listHdr"> </legend>
<table id="showList" name="showList">
	<thead>
  <tr>
    <th colspan="1">&nbsp;</th>
    <th><?php echo T("Sequence"); ?></th>
    <th><?php echo T("Active"); ?></th>
    <th><?php echo T("Host URL"); ?></th>
    <th><?php echo T("Name"); ?></th>
    <th><?php echo T("Database"); ?></th>
    <th><?php echo T("Userid"); ?></th>
    <th><?php echo T("Password"); ?></th>
  </tr>
	</thead>
	<tbody class="striped">
	  <!--to be generated and filled in by Javascript and Server-->
	</tbody>
	<tfoot>
  <tr>
  	<!-- spacer used to slightly seperate button from form body -->
    <td><input type="hidden" id="xxx" name="xxx" value=""></td>
  </tr>
	</tfoot>
</table>
</fieldset>
<input type="button" class="newBtn" value="<?php echo T("Add New"); ?>" />
</form>
</div>


<div id="editDiv" style="display: none;">
<form role="form" id="editForm" name="editForm">
<h5 id="reqdNote">*<?php echo T("Required note"); ?></h5>
<fieldset>
<legend id="fieldsHdr"><?php echo T("Host Editor"); ?></legend>
<table id="editTbl">
  <tbody>
  <tr>
    <td><label for="name"><?php echo T("Name"); ?>:</label></td>
    <td>
      <input id="name" name="name" type="text" size="50" required aria-required="true" />
			<span class="reqd">*</span>    
    </td>
  </tr>
  <tr>
    <td><label for="host"><?php echo T("Host URL"); ?>:</label></td>
    <td>
      <input id="host" name="host" type="text" size="50" required aria-required="true" />
			<span class="reqd">*</span>    
    </td>
  </tr>
  <tr>
    <td><label for="db"><?php echo T("Port"); ?>:</label></td>
    <td>
      <input id="port" name="port" type="number" size="5" min="11" pattern="[0-9]{1,5}" required aria-required="true" />
			<span class="reqd">*</span>    
    </td>
  </tr>
  <tr>
    <td><label for="db"><?php echo T("Database"); ?>:</label></td>
    <td>
      <input id="db" name="db" type="text" size="20" required aria-required="true" />
			<span class="reqd">*</span>    
    </td>
  </tr>
  <tr>
    <td><label for="db"><?php echo T("Syntax"); ?>:</label></td>
    <td>
      <input id="syntax" name="syntax" type="text" size="20" required aria-required="true" />
			<span class="reqd">*</span>    
    </td>
  </tr>
  <tr>
    <td><label for="db"><?php echo T("Service"); ?>:</label></td>
    <td>
		<select  id="service" name="service" >to be filled by server</select>
    </td>
  </tr>
  <tr>
    <td><label for="seq"><?php echo T("Sequence"); ?>:</label></td>
    <td>
      <input id="seq" name="seq" type="number" size="3" pattern="[0-9]{1,2}" required aria-required="true" />
			<span class="reqd">*</span>    
    </td>
  </tr>
  <tr>
    <td><label for="active"><?php echo T("Active"); ?> (Y/N):</label></td>
    <td>
      <!--input type="checkbox" id="active" name="active" value="Y" /-->
      <input id="active" name="active" type="text" size="1" pattern="[Y,y,N,n]" required aria-required="true" />
			<span class="reqd">*</span>    
    </td>
  </tr>
  <tr>
    <td><label for="user"><?php echo T("Userid"); ?>:</label></td>
    <td>
      <input id="user" name="user" type="text" size="20" />
    </td>
  </tr>
  <tr>
    <td><label for="pw"><?php echo T("Password"); ?>:</label></td>
    <td class="primary inptFld">
      <input id="pw" name="pw" type="text" size="20" />
    </td>
  </tr>
  <tr>
    <td><input type="hidden" id="cat" name="cat" value="hosts"></td>
    <td><input type="hidden" id="mode" name="mode" value=""></td>
    <td><input type="hidden" id="id" name="id" value=""></td>
  </tr>
  </tbody>

  <tfoot>
  <tr>
    <td colspan="2" align="left">
			<input type="submit" id="addBtn" class="actnBtns" value="<?php echo T("Add"); ?>" />
			<input type="submit" id="updtBtn" class="actnBtns" value="<?php echo T("Update"); ?>" />
			<input type="button" id="cnclBtn" value="<?php echo T("Cancel"); ?>" />
    </td>
    <td colspan="1" align="right">
			<input type="submit" id="deltBtn" class="actnBtns" value="<?php echo T("Delete"); ?>" />
    </td>
  </tr>
  </tfoot>
</table>
</fieldset>
</form>
</div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	
	require_once(REL(__FILE__, "../classes/JSAdmin.php"));
	require_once(REL(__FILE__, "../admin/onlineHostsJs.php"));
?>	
