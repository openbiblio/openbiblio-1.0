<table class="primary">
  <tr>
    <th colspan="3" nowrap="yes" align="left">
      <?php echo $formLabel; ?>:
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <?php echo $loc->getText("biblioMarcNewFormTag"); ?>:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("tag",3,3,$postVars,$pageErrors); ?>
      <input type="button" onClick="javascript:popSecondary('../catalog/usmarc_select.php?retpage=<?php echo $returnPg;?>')" value="<?php echo $loc->getText("biblioMarcNewFormSelect"); ?>" class="button">
      <?php echo $tagDesc; ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <?php echo $loc->getText("biblioMarcNewFormInd1"); ?>
    </td>
    <td valign="top" class="primary">
      <input type="checkbox" name="ind1Cd" value="CHECKED"
        <?php if (isset($postVars["ind1Cd"])) echo $postVars["ind1Cd"]; ?> >
      <?php echo $ind1Desc; ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <?php echo $loc->getText("biblioMarcNewFormInd2"); ?>
    </td>
    <td valign="top" class="primary">
      <input type="checkbox" name="ind2Cd" value="CHECKED"
        <?php if (isset($postVars["ind2Cd"])) echo $postVars["ind2Cd"]; ?> >
      <?php echo $ind2Desc; ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <?php echo $loc->getText("biblioMarcNewFormSubfld"); ?>:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("subfieldCd",1,1,$postVars,$pageErrors); ?>
      <?php echo $subfldDesc; ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <?php echo $loc->getText("biblioMarcNewFormData"); ?>:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("fieldData",60,256,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td align="center" colspan="2" class="primary">
      <input type="submit" value="<?php echo $loc->getText("catalogSubmit"); ?>" class="button">
      <input type="button" onClick="parent.location='../catalog/biblio_marc_list.php?bibid=<?php echo $bibid;?>'" value="<?php echo $loc->getText("catalogCancel"); ?>" class="button">
    </td>
  </tr>

</table>
