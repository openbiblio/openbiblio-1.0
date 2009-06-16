<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");

	session_cache_limiter(null);

  $tab = "admin";
  $nav = "lookupHosts";
  $focus_form_name = "editForm";
  $focus_form_field = "name";

  require_once(REL(__FILE__, "../functions/inputFuncs.php"));
  require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>

<div id="listDiv">
<form id="showForm" name="showForm" class="form">
<h5 id="updateMsg"></h5>
<h1><span id="listHdr" class="title"></span></h1>
<table id="showList" name="showList" class="primary striped">
	<thead>
  <tr>
    <th colspan="1"  class="colHead">&nbsp;</th>
    <th class="colHead"><?php echo T("lookup_hostsSeqNo"); ?></th>
    <th class="colHead"><?php echo T("lookup_hostsActive"); ?></th>
    <th class="colHead"><?php echo T("lookup_hostsHost"); ?></th>
    <th class="colHead"><?php echo T("lookup_hostsName"); ?></th>
    <th class="colHead"><?php echo T("lookup_hostsDb"); ?></th>
    <th class="colHead"><?php echo T("lookup_hostsUser"); ?></th>
    <th class="colHead"><?php echo T("lookup_hostsPw"); ?></th>
  </tr>
	</thead>
	<tbody>
	  <!--to be generated and filled in by Javascript and Server-->
	</tbody>
	<tfoot>
  <tr>
  	<!-- spacer used to slightly seperate button from form body -->
    <td><input type="hidden" id="xxx" name="xxx" value=""></td>
  </tr>
	<tr>
	  <td colspan="8" class="primary btnField">
			<input type="button" id="newBtn" value="<?php echo T("lookup_hostsAddNewBtn"); ?>" class="button" />
		</td>
	</tr>
	</tfoot>
</table>
</form>
</div>


<div id="editDiv">
<form id="hostForm" name="editForm" class="form">
<h1><span id="hostHdr" class="title"></span></h1>
<h5 id="reqdNote" class="reqd"><sup>*</sup><?php echo T("lookup_rqdNote"); ?></h5>
<table id="editTbl" class="primary">
	<thead>
  </thead>
  <tbody>
  <tr>
    <td class="primary lblFld">
      <label for="name" class="reqd"><sup>*</sup><?php echo T("lookup_hostsName"); ?>
    </td>
    <td class="primary inptFld">
      <?php printInputText("name",32,32,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="host" class="reqd"><sup>*</sup><?php echo T("lookup_hostsHost"); ?>
    </td>
    <td class="primary inptFld">
      <?php printInputText("host",32,32,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="db" class="reqd"><sup>*</sup><?php echo T("lookup_hostsDb"); ?>
    </td>
    <td class="primary inptFld">
      <?php printInputText("db",16,16,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="seq" class="reqd"><sup>*</sup><?php echo T("lookup_hostsSeqNo"); ?></label>
    </td>
    <td class="primary inptFld">
      <?php printInputText("seq",3,3,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="active"><?php echo T("lookup_hostsActive"); ?></label>
    </td>
    <td class="primary inptFld">
      <input type="checkbox" id="active" name="active" value="y"
        <?php if (isset($postVars["active"])) echo H($postVars["active"]); ?> >
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="user"><?php echo T("lookup_hostsUser"); ?>
    </td>
    <td class="primary inptFld">
      <?php printInputText("user",10,10,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="pw"><?php echo T("lookup_hostsPw"); ?>
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
</form>
</div>

<div id="msgDiv"><fieldSet id="msgArea"></fieldset></div>

<?php include("../shared/footer.php"); ?>
