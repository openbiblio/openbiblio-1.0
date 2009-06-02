<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  $tab = "reports";
  $nav = "reportlist";

  include("../shared/logincheck.php");
  include("../shared/header.php");
  require_once("../functions/fileIOFuncs.php");
  require_once("../classes/ReportDefinition.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
  define("REPORT_DEFS_DIR","../reports/reportdefs");

  #****************************************************************************
  #*  Read report definition xml
  #****************************************************************************
  $reportids = array();
  $reportTitles = array();
  $reportSql = array();
  
  if ($handle = opendir(REPORT_DEFS_DIR)) {
    while (false !== ($file = readdir($handle))) { 
      if ($file[0] != "." && !is_dir($file)) {
        $fileName = REPORT_DEFS_DIR."/".$file;
        //$xml = file_get_contents($fileName);
        $xml = fileGetContents($fileName);
        if ($xml === FALSE) {
          echo '<p><font class="error">';
          echo $loc->getText('Cannot read report file: %fileName%',
            array('fileName' => basename($fileName)));
          echo '</font></p>';
          continue;
        }
        $rptDef = new ReportDefinition();
        if ($rptDef->parse($xml)) {
          $rptid = $rptDef->getId();
          $reportids[] = $rptid;
          $reportTitles[$rptid] = $rptDef->getTitle();
          $reportSql[$rptid] = $rptDef->getSql();
        } else {
          echo $loc->getText("reportListXmlErr");
          echo "<pre>file name: ".H($fileName)."\n".H($rptDef->getXmlErrorString())."</pre>";
          exit();
        }
        $rptDef->destroy();
        unset($rptDef);
      } 
    }
    closedir($handle); 
  }
?>

<h1><?php echo $loc->getText("reportListHdr");?></h1>

<?php echo $loc->getText("reportListDesc");?>
<ol>
<?php
  foreach ($reportids as $rptid) {
    $title = $loc->getText($reportTitles[$rptid]);
    $sql = $reportSql[$rptid];
    echo "<li><a href=\"../reports/report_criteria.php?reset=Y&amp;rptid=".HURL($rptid)."&amp;title=".HURL($title)."&amp;sql=".HURL($sql)."\">".H($title)."</a></li>";
  }
?>
</ol>
<?php include("../shared/footer.php"); ?>
