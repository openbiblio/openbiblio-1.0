<?php
require_once('../../../database_constants.php');

$sqls = array(
  "DELETE FROM biblio_status_hist",
  "UPDATE biblio_copy SET status_cd='in', due_back_dt=NULL, mbrid=NULL",
  # FIXME
);

if (!mysql_connect(OBIB_HOST, OBIB_USERNAME, OBIB_PWD)) {
  die("Can't connect to DB");
}
if (!mysql_select_db(OBIB_DATABASE)) {
  die("Can't select DB");
}
foreach ($sqls as $sql) {
  $res = mysql_query($sql);
  if (!$res) {
    die("Database Error: ".mysql_error());
  }
}
echo "Loaded sample circulation data."
?>
