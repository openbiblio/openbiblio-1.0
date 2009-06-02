<?php
require_once('../../../database_constants.php');
if (!mysql_connect(OBIB_HOST, OBIB_USERNAME, OBIB_PWD)) {
  die("Can't connect to DB");
}
if (!mysql_select_db(OBIB_DATABASE)) {
  die("Can't select DB");
}
$res = mysql_query('SHOW TABLES');
while ($row = mysql_fetch_row($res)) {
  if (!mysql_query('DROP TABLE '.$row[0])) {
    die("Can't drop table ".$row[0]);
  }
}
echo "Tables dropped"
?>
