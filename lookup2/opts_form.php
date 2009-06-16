<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");

  session_cache_limiter(null);

  $tab = "admin";
  $nav = "lookupOpts";
  $focus_form_name = "editForm";
  $focus_form_field = "protocol";

  require_once(REL(__FILE__, "../functions/inputFuncs.php"));
  require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>

<form id="editForm" name="editForm" class="form">
<h5 id="updateMsg"></h5>
<h1><span id="editHdr" class="title"></span></h1>
<table id="optsShow" name="optsShow" class="primary">
	<tbody>
  <tr>
    <td class="primary lblFld">
      <label for="protocol"><?php echo T("lookup_optsProtocol"); ?></label>
    </td>
    <td valign="top" class="primary">
      <select id="protocol" name="protocol">
        <option value="   ">   </option>
        <option value="YAZ">YAZ</option>
        <option value="SRU">SRU</option>
      </select>
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="maxHits"><?php echo T("lookup_optsMaxHits"); ?></label>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("maxHits",10,10,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="keepDashes"><?php echo T("lookup_optsKeepDashes"); ?></label>
    </td>
    <td valign="top" class="primary">
      <input type="checkbox" id="keepDashes" name="keepDashes" value="y"
        <?php //if (isset($postVars["keepDashes"])) echo H($postVars["keepDashes"]); ?> >
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="callNmbrType"><?php echo T("lookup_optsCallNmbrType"); ?></label>
    </td>
    <td class="primary inptFld">
      <select id="callNmbrType" name="callNmbrType">
        <option value="   "  >     </option>
        <option value="LoC"  >LoC  </option>
        <option value="Dew"  >Dew  </option>
        <option value="UDC"  >UDC  </option>
        <option value="local">local</option>
      </select>
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="autoDewey"><?php echo T("lookup_optsAutoDewey"); ?></label>
    </td>
    <td class="primary inptFld">
      <input type="checkbox" id="autoDewey" name="autoDewey" value="y"
        <?php //if (isset($postVars["autoDewey"])) echo H($postVars["autoDewey"]); ?> >
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="defaultDewey"><?php echo T("lookup_optsDefaultDewey"); ?></label>
    </td>
    <td class="primary inptFld">
      <?php printInputText("defaultDewey",10,10,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="autoCutter"><?php echo T("lookup_optsAutoCutter"); ?></label>
    </td>
    <td class="primary inptFld">
      <input type="checkbox" id="autoCutter" name="autoCutter" value="y"
        <?php //if (isset($postVars["autoCutter"])) echo H($postVars["autoCutter"]); ?> >
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
     <label for="cutterType"><?php echo T("lookup_optsCutterType"); ?></label>
    </td>
    <td class="primary inptFld">
      <select id="cutterType" name="cutterType">
        <option value="   ">   </option>
        <option value="LoC">LoC</option>
        <option value="CS3">CS3</option>
      </select>
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="cutterWord"><?php echo T("lookup_optsCutterWord"); ?></label>
    </td>
    <td class="primary inptFld">
      <?php printInputText("cutterWord",10,10,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="autoCollect"><?php echo T("lookup_optsAutoCollection"); ?></label>
    </td>
    <td class="primary inptFld">
      <input type="checkbox" id="autoCollect" name="autoCollect" value="y"
        <?php //if (isset($postVars["autoCollect"])) echo H($postVars["autoCollect"]); ?> >
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="fictionName"><?php echo T("lookup_optsFictionName"); ?></label>
    </td>
    <td class="primary inptFld">
      <?php printInputText("fictionName",10,10,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="fictionCode"><?php echo T("lookup_optsFictionCode"); ?></label>
    </td>
    <td class="primary inptFld">
      <?php printInputText("fictionCode",10,10,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="fictionLoC"><?php echo T("lookup_optsLocFictionCodes"); ?></label>
    </td>
    <td class="primary inptFld">
      <?php printInputText("fictionLoC",30,50,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td class="primary lblFld">
      <label for="fictionDew"><?php echo T("lookup_optsDewFictionCodes"); ?></label>
    </td>
    <td class="primary inptFld">
      <?php printInputText("fictionDew",30,50,$postVars,$pageErrors); ?>
    </td>
  </tr>
  </tbody>
  <tfoot>
  <tr>
    <td><input type="hidden" id="mode" name="mode" value=""></td>
  </tr>
  <tr>
    <td align="center" colspan="2" class="primary">
      <input type="button" id="updtBtn" name="updtBtn" class="button"
			 value="<?php echo T("lookup_optsUpdtBtn"); ?>" />
    </td>
  </tr>
	</tfoot>
</table>
</form>
<div id="msgDiv"><fieldSet id="msgArea"></fieldset></div>

<?php include("../shared/footer.php"); ?>
