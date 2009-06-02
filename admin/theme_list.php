<?php
/**********************************************************************************
 *   Copyright(C) 2002 David Stevens
 *
 *   This file is part of OpenBiblio.
 *
 *   OpenBiblio is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   OpenBiblio is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with OpenBiblio; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 **********************************************************************************
 */

  $tab = "admin";
  $nav = "themes";

  require_once("../classes/Theme.php");
  require_once("../classes/ThemeQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../shared/read_settings.php");

  require_once("../shared/logincheck.php");

  require_once("../shared/header.php");

  $themeQ = new ThemeQuery();
  $themeQ->connect();
  if ($themeQ->errorOccurred()) {
    $themeQ->close();
    displayErrorPage($themeQ);
  }
  $themeQ->execSelect();
  if ($themeQ->errorOccurred()) {
    $themeQ->close();
    displayErrorPage($themeQ);
  }

?>


<form name="editthemeidform" method="POST" action="../admin/theme_use.php">
<table class="primary">
  <tr>
    <th nowrap="yes" align="left">
      Change Theme In Use:
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Choose a New Theme:
      <select name="themeid">
        <?php
          while ($theme = $themeQ->fetchTheme()) {
            echo "<option value=\"".$theme->getThemeid()."\"";
            if ($theme->getThemeid() == OBIB_THEMEID) {
              echo " selected";
            }
            echo ">".$theme->getThemeName()."\n";
          }
        ?>
      </select>
      <input type="submit" value="Update" class="button">
    </td>
  </tr>
</table>
</form>

<a href="../admin/theme_new_form.php?reset=Y">Add New Theme</a><br>
<h1>Themes:</h1>
<table class="primary">
  <tr>
    <th colspan="3" valign="top">
      <font class="small">*</font>Function</font>
    </th>
    <th valign="top">
      Theme Name</font>
    </th>
    <th valign="top">
      Usage</font>
    </th>
  </tr>
  <?php
    $themeQ->resetResult();
    $row_class = "primary";
    while ($theme = $themeQ->fetchTheme()) {
  ?>
  <tr>
    <td valign="top" class="<?php echo $row_class;?>">
      <a href="../admin/theme_edit_form.php?themeid=<?php echo $theme->getThemeid();?>" class="<?php echo $row_class;?>">edit</a>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <a href="../admin/theme_new_form.php?themeid=<?php echo $theme->getThemeid();?>" class="<?php echo $row_class;?>">copy</a>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <?php if ($theme->getThemeid() == OBIB_THEMEID) { echo "del"; } else { ?>
        <a href="../admin/theme_del_confirm.php?themeid=<?php echo $theme->getThemeid();?>&name=<?php echo urlencode($theme->getThemeName());?>" class="<?php echo $row_class;?>">del</a>
      <?php } ?>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <?php echo $theme->getThemeName();?>
    </td>
    <td valign="top" class="<?php echo $row_class;?>">
      <?php if ($theme->getThemeid() == OBIB_THEMEID) { echo "in use"; } else { echo "&nbsp;"; } ?>
    </td>
  </tr>
  <?php
      # swap row color
      if ($row_class == "primary") {
        $row_class = "alt1";
      } else {
        $row_class = "primary";
      }
    }
    $themeQ->close();
  ?>
</table>
<br>
<table class="primary"><tr><td valign="top" class="noborder"><font class="small">*Note:</font></td>
<td class="noborder"><font class="small">The delete function is not available on the theme that is currently in use.
</font>
</td></tr></table>
<?php include("../shared/footer.php"); ?>
