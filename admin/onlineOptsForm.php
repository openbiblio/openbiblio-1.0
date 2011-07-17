<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");

  $tab = "admin";
  $nav = "onlineOpts";
  $focus_form_name = "editForm";
  $focus_form_field = "protocol";
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>

<h3 id="editHdr"></h3>

<p id="updateMsg" class="error"></p>
<br />
<form id="editForm" name="editForm">
<fieldset>
<table id="optsShow" name="optsShow">
	<tbody class="striped">
  <tr>
    <th><label for="protocol"><?php echo T("Online Protocol"); ?></label></th>
    <td>
      <select id="protocol" name="protocol">
        <option value="SRU" selected><?php echo T('SRU');?></option>
        <option value="YAZ"><?php echo T('YAZ');?></option>
      </select>
    </td>
  </tr>
  <tr>
    <th><label for="maxHits"><?php echo T("Maximum Hits"); ?></label></th>
    <td>
      <input id="maxHits" name="maxHits" type="number" min="1" max="999" size="3" required aria-required="true" />
    </td>
  </tr>
  <tr>
    <th><label for="timeout"><?php echo T("Timeout"); ?></label></th>
    <td>
      <input id="timeout" name="timeout" type="number" min="10" max="120" size="2" required aria-required="true" />
    </td>
  </tr>
  <tr>
    <th><label for="keepDashes"><?php echo T("Keep Dashes"); ?></label></th>
    <td>
      <input id="keepDashes" name="keepDashes" type="checkbox" value="y" />
    </td>
  </tr>
  <tr>
    <th><label for="callNmbrType"><?php echo T("Call Nmbr Type"); ?></label></th>
    <td>
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
    <th><label for="autoDewey"><?php echo T("Auto Dewey"); ?></label></th>
    <td>
      <input id="autoDewey" name="autoDewey" type="checkbox" value="y" />
    </td>
  </tr>
  <tr>
    <th><label for="defaultDewey"><?php echo T("Default Dewey"); ?></label></th>
    <td>
      <input id="defaultDewey" name="defaultDewey" type="text" size="10" />
    </td>
  </tr>
  <tr>
    <th><label for="autoCutter"><?php echo T("Auto Cutter"); ?></label></th>
    <td>
      <input id="autoCutter" name="autoCutter" type="checkbox" value="y" />
    </td>
  </tr>
  <tr>
    <th><label for="cutterType"><?php echo T("Cutter Type"); ?></label></th>
    <td>
      <select id="cutterType" name="cutterType">
        <option value="   ">   </option>
        <option value="LoC">LoC</option>
        <option value="CS3">CS3</option>
      </select>
    </td>
  </tr>
  <tr>
    <th><label for="cutterWord"><?php echo T("Cutter Word"); ?></label></th>
    <td>
      <input id="cutterWord" name="cutterWord" type="text" size="10" />
    </td>
  </tr>
  <tr>
    <th><label for="noiseWords"><?php echo T("Noise Words"); ?></label></th>
    <td>
      <input id="noiseWords" name="noiseWords" type="text" size="30" maxsize="100" />
    </td>
  </tr>
  <tr>
    <th><label for="autoCollect"><?php echo T("Auto Collection"); ?></label></th>
    <td>
      <input id="autoCollect" name="autoCollect" type="checkbox" value="y" />
    </td>
  </tr>
  <tr>
    <th><label for="fictionName"><?php echo T("Fiction Name"); ?></label></th>
    <td>
      <input id="fictionName" name="fictionName" type="text" size="10" />
    </td>
  </tr>
  <tr>
    <th><label for="fictionCode"><?php echo T("Fiction Code"); ?></label></th>
    <td>
      <input id="fictionCode" name="fictionCode" type="text" size="10" />
    </td>
  </tr>
  <tr>
    <th><label for="fictionLoC"><?php echo T("Fiction Codes - LoC"); ?></label></th>
    <td>
      <input id="fictionLoC" name="fictionLoC" type="text" size="30" maxsize="50" />
    </td>
  </tr>
  <tr>
    <th>
      <label for="fictionDew"><?php echo T("Fiction Codes - Dewey"); ?></label>
    </th>
    <td>
      <input id="fictionDew" name="fictionDew" type="text" size="30" maxsize="50" />
    </td>
  </tr>
  </tbody>
  <tfoot>
  <tr>
    <td><input type="hidden" id="mode" name="mode" value=""></td>
  </tr>
  <tr>
    <td colspan="2" class="primary btnFld">
      <input type="submit" id="updtBtn" name="updtBtn" class="button" value="<?php echo T("Update"); ?>" />
    </td>
  </tr>
	</tfoot>
</table>
</fieldset>
</form>

<div id="msgDiv"><fieldSet id="msgArea"></fieldset></div>

<?php
	require_once("../themes/".Settings::get('theme_dir_url')."/footer.php");
	
	require_once(REL(__FILE__, "onlineOptsJs.php"));
?>	
