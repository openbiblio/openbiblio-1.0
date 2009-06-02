<?php
# So require and include get the right path
chdir('../..');

$doing_install = true;
require_once('../shared/common.php');
require_once('../classes/InstallQuery.php');

$installQ = new InstallQuery;
$e = $installQ->connect_e();
if ($e) {
  echo $e->toStr();
}
$installQ->freshInstall('en', true, '0.4.0');
echo 'Installed 0.4.0';

