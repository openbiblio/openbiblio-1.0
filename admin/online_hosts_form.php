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

	require_once(REL(__FILE__, "online_hosts_js.php"));

?>
<h3 id="listHdr"><?php echo T('Online Hosts'); ?></h3>

<div id="listDiv">
<h5 id="updateMsg"></h5>
<br />
<form id="showForm" name="showForm" class="form">
<input type="button" class="newBtn button" value="<?php echo T("Add New"); ?>" class="button" />
<fieldset>
<table id="showList" name="showList" class="primary">
	<thead>
  <tr>
    <th colspan="1"  class="colHead">&nbsp;</th>
    <th class="colHead"><?php echo T("Sequence"); ?></th>
    <th class="colHead"><?php echo T("Active"); ?></th>
    <th class="colHead"><?php echo T("Host URL"); ?></th>
    <th class="colHead"><?php echo T("Name"); ?></th>
    <th class="colHead"><?php echo T("Database"); ?></th>
    <th class="colHead"><?php echo T("Userid"); ?></th>
    <th class="colHead"><?php echo T("Password"); ?></th>
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
<input type="button" class="newBtn button" value="<?php echo T("Add New"); ?>" class="button" />
</form>
</div>


<div id="editDiv">
<form id="hostForm" name="editForm" class="form">
<!--h1><span id="hostHdr" class="title"><?php echo T('Host Editor'); ?></span></h1-->
<h5 id="reqdNote" class="reqd"><sup>*</sup><?php echo T("Required note"); ?></h5>
<fieldset>
<legend><?php echo T('Host Editor'); ?></legend>
<table id="editTbl" class="primary">
	<thead>
  </thead>
  <tbody>
  <tr>
    <td class="primary lblFld">
      <label for="name" class="reqd"><sup>*</sup><?php echo T("Name"); ?>:</label>
    </td>
    <td class="primary inptFld">
      <?php printInputText("name",32,32,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="host" class="reqd"><sup>*</sup><?php echo T("Host URL"); ?>:</label>
    </td>
    <td class="primary inptFld">
      <?php printInputText("host",32,32,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="db" class="reqd"><sup>*</sup><?php echo T("Database"); ?>:</label>
    </td>
    <td class="primary inptFld">
      <?php printInputText("db",16,16,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="seq" class="reqd"><sup>*</sup><?php echo T("Sequence"); ?>:</label>
    </td>
    <td class="primary inptFld">
      <?php printInputText("seq",3,3,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="active"><?php echo T("Active"); ?>:</label>
    </td>
    <td class="primary inptFld">
      <input type="checkbox" id="active" name="active" value="y"
        <?php if (isset($postVars["active"])) echo H($postVars["active"]); ?> >
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="user"><?php echo T("Userid"); ?>:</label>
    </td>
    <td class="primary inptFld">
      <?php printInputText("user",10,10,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="pw"><?php echo T("Password"); ?>:</label>
    </td>
    <td class="primary inptFld">
      <?php printInputText("pw",10,10,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td><input type="hidden" id="mode" name="mode" value=""></td>
    <td><input type="hidden" id="id" name="id" value=""></td>
  </tr>
  <tfoot>
  <tr>
    <td colspan="1" class="primary" align="left">
			<input type="button" id="addBtn" value="Add" class="button" />
			<input type="button" id="updtBtn" value="Update" class="button" />
			<input type="button" id="cnclBtn" value="Cancel" class="button" />
    </td>
    <td colspan="1" class="primary" align="right">
			<input type="button" id="deltBtn" value="Delete" class="button" />
    </td>
  </tr>
  </tfoot>
  </tbody>

</table>
</fieldset>
</form>
</div>

<div id="msgDiv"><fieldSet id="msgArea"></fieldset></div>
