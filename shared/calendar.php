<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
  require_once(REL(__FILE__, "../model/Themes.php"));
  require_once(REL(__FILE__, "../classes/Calendar.php"));

  $themes = new Themes;
  $theme = $themes->getOne(Settings::get('themeid'));
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo H(Settings::get('html_lang_attr')); ?>" lang="<?php echo H(Settings::get('html_lang_attr')); ?>">
  <head>
		<?php // code character set if specified
		if (Settings::get('charset') != "") { ?>
		<meta http-equiv="content-type" content="text/html; charset=<?php echo H(Settings::get('charset')); ?>" />
		<?php } ?>
    <title><?php echo T("Calendar"); ?></title>
    <style>
      body {
        margin: 0;
        padding: 0;
        background-color: <?php echo H($theme['alt1_bg']); ?>;
        font-face: <?php echo H($theme['alt1_font_face']); ?>;
        font-size: 12px;
        color: <?php echo H($theme['alt1_font_color']); ?>;
      }
      a:link { color: <?php echo H($theme['alt1_link_color']); ?> }
      .calendar, .calendarToday, .calendarHeader, .calendarDayNames { font-size: 12px }
      .calendarHeader { color: <?php echo H($theme['alt1_link_color']); ?> }
      .calendarDayNames { font-weight: bold }
      .calendarToday { border: 1px solid black }
    </style>
  </head>
  <body>
<?php
  class Cal extends Calendar {
    function getCalendarLink($month, $year) {
      $s = getenv('SCRIPT_NAME');
      return "$s?month=$month&amp;year=$year";
    }
  }

  $d = getdate(time());
  if ($_REQUEST['month'] != "") {
    $d['mon'] = $_REQUEST['month'];
  }
  if ($_REQUEST['year'] != "") {
    $d['year'] = $_REQUEST['year'];
  }
  $cal = new Cal;
  echo $cal->getVThreeMonthView($d['mon'], $d['year']);
?>
  </body>
</html>
