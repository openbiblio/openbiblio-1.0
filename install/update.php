<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  $doing_install = true;
  require_once("../shared/common.php");

  require_once(REL(__FILE__, "../install/UpgradeQuery.php"));

  include(REL(__FILE__, "../install/header.php"));
?>
<br />
<h1>OpenBiblio Upgrade:</h1>

<?php

  # testing connection and current version
  $upgradeQ = new UpgradeQuery();

  echo "Updating OpenBiblio tables, please wait...<br />\n";

  list($notices, $error) = $upgradeQ->performUpgrade_e();
  if ($error) {
    echo "<h1>Upgrade Failed</h1>";
    echo H($error->toStr());
    exit();
  }
  $upgradeQ->close();

?>
<br />
OpenBiblio tables have been updated successfully!<br />
<?php
if (!empty($notices)) {
  echo '<h2>NOTICE:</h2>';
  echo '<ul>';
  foreach ($notices as $n) {
    echo '<li>'.H($n).'</li>';
  }
  echo '</ul>';
}
?>
<a href="../circ/index.php">start using OpenBiblio</a>

<?php

  include(REL(__FILE__, "../install/footer.php"));
