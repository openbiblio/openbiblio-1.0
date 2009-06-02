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
  include("../install/header.php");

?>
<br>
<h1>OpenBiblio Installation:</h1>

<!--This install module is still not complete.  Please follow the 
<a href="../install_instructions.html">Install Instructions</a> to install OpenBiblio
instead of this module.<br><br-->
<?php
  require_once("../classes/InstallQuery.php");

  $installQ = new InstallQuery();
  @$installQ->connect();
  if ($installQ->errorOccurred()) {
    @$installQ->close();
    ?>
      The connection to the database failed with the following error.
        <pre>
    <?php echo $installQ->getDbError(); ?>
        </pre>
      Please make sure the following has been done
      before running this install script.
      <ol>
        <li>create OpenBiblio database (<a href="../install_instructions.html#step4">step 4</a> of the install instructions)</li>
        <li>create OpenBiblio database user (<a href="../install_instructions.html#step5">step 5</a> of the install instructions)</li>
        <li>update openbibilio/database_constants.php with your new database username and password
          (<a href="../install_instructions.html#step8">step 8</a> of the install instructions)</li>
      </ol>
      See <a href="../install_instructions.html">Install Instructions</a> for more details.
    <?php
    include("../install/footer.php");
    exit();
  }
  $installQ->close();
  echo "Database connection is good.  Please choose a language and press the install button.<br>\n";

?>
  <br>
  <form name="installForm" method="POST" action="../install/install.php">
    <table cellpadding=0 cellspacing=0 border=0>
      <tr>
        <td><font class="primary">Language:</font></td>
        <td><select name="locale">
<?php
  require_once("../locale/language_list.php");
  foreach($locales as $langCode => $langDesc) {
    echo "<option value=\"".$langCode."\">".$langDesc."\n";
  }
?>
        </select></td>
      </tr>
      <tr>
        <td rowspan="2" valign="top"><font class="primary">Install Test Data:</font></td>
        <td><input type="checkbox" name="installTestData" value="yes"></td>
      </tr>
      <tr>
        <td><input type="submit" value="Install"></td>
      </tr>
    </table>
  </form>


<?php include("../install/footer.php"); ?>
