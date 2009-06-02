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

  $tab = "cataloging";
  $nav = "";

  include("../shared/read_settings.php");
  include("../shared/logincheck.php");

  if (count($HTTP_POST_FILES) == 0) {
    header("Location: upload_usmarc_form.php");
    exit();
  }

  include("../shared/header.php");

  $usmarc_data = file($HTTP_POST_FILES["usmarc_data"]["tmp_name"]);
  echo "first line is [".$usmarc_data[0]."]<br>";
?>
<pre>
raw data:
<?php  readfile($HTTP_POST_FILES["usmarc_data"]["tmp_name"]); ?>
</pre>

<?php include("../shared/footer.php"); ?>
