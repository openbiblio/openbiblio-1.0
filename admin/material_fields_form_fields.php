<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
?>
<table class="primary">
<tr>
<th><?php echo T("Field"); ?></th>
<th><?php echo T("Value"); ?></th>
</tr>
<tr>
<td  align="right" class="primary"><?php T("Material Type");?></td>
<?php
  //$dmQ = new DmQuery();
  //$dmQ->connect();
  //$dm = $dmQ->get1("material_type_dm",$materialCd);
  //$material_type= $dm->getDescription();
  //$dmQ->close();
?>
<td  align="Left" class="primary">
<?php
echo H($material_type);
?>
</td></tr>
<tr>
<td align="right" class="primary"><?php echo T("Tag"); ?></td>
<td align="Left" class="primary">
<?php printInputText("tag",3,3,$postVars,$pageErrors); ?>
<!--<input type="button" onclick="javascript:popSecondary('../catalog/usmarc_select.php?retpage=<?php echo HURL($returnPg);?>')" value="<?php echo T("Select"); ?>" class="button" />-->
</td></tr>
<tr>
<td align="right" class="primary"><?php echo T("Subfield Code"); ?></td>
<td align="Left" class="primary">
<?php printInputText("subfield_cd",1,1,$postVars,$pageErrors); ?>
</td></tr>
<tr>
<td align="right" class="primary"><?php echo T("Position");?></td>
<td align="Left" class="primary">
<?php printInputText("position",3,3,$postVars,$pageErrors); ?>
</td></tr>
<tr>
<td align="right" class="primary"><?php echo T("Label");?></td>
<td align="Left" class="primary">
<?php printInputText("label",32,64,$postVars,$pageErrors); ?>
</td></tr>
<tr>
<td align="right" class="primary"><?php echo T("Form Type"); ?></td>
<td align="Left" class="primary">
<select name="form_type" id="form_type" tabindex="7">
<option value="text" <?php if((isset($postVars["form_type"]))&&($postVars["form_type"]=="text")) echo 'selected="selected"'; ?>><?php echo T("Text Field"); ?></option>
<option value="textarea" <?php if((isset($postVars["form_type"]))&&($postVars["form_type"]=="textarea")) echo 'selected="selected"'; ?>><?php echo T("Text Area"); ?></option>
</select>
</td></tr>
<tr>
<td  align="right" class="primary"><?php echo T("Required?"); ?></td>
<td  align="Left" class="primary">
<select name="required" id="required" tabindex="4">
<option value="1" <?php if ((isset($postVars["required"]))&& ($postVars["required"]=="1")) echo 'selected="selected"'; ?>><?php echo T("Yes"); ?></option>
<option value="0" <?php if ((isset($postVars["required"]))&& ($postVars["required"]=="0")) echo 'selected="selected"'; ?>><?php echo T("No"); ?></option>
</select>
</td></tr>
<tr>
<td  align="right" class="primary"><?php echo T("Repeatable?"); ?></td>
<td  align="Left" class="primary">
<select name="repeatable" id="repeatable" tabindex="4">
<option value="1" <?php if ((isset($postVars["repeatable"]))&& ($postVars["repeatable"]=="1")) echo 'selected="selected"'; ?>><?php echo T("Yes"); ?></option>
<option value="0" <?php if ((isset($postVars["repeatable"]))&& ($postVars["repeatable"]=="0")) echo 'selected="selected"'; ?>><?php echo T("No");?></option>
</select>
</td></tr>
<tr>
<td  align="right" class="primary"><?php echo T("Search Results"); ?></td>
<td  align="Left" class="primary">
<?php printInputText("search_results",32,64,$postVars,$pageErrors); ?>
</td></tr>
<tr>
<td align="center" colspan="2" class="primary">
<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
<input type="button" onClick="self.location='<?php echo H(addslashes($cancelLocation));?>'" value="<?php echo T("Cancel"); ?>" class="button" />
</td></tr>
</table>
