<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  $tab = "reports";
  $nav = "letterlist";

  include("../shared/logincheck.php");
  include("../shared/header.php");
  require_once("../functions/fileIOFuncs.php");
  require_once("../classes/ReportDefinition.php");
  require_once("../classes/LetterFormat.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
  define("LETTER_DEFS_DIR","../reports/letterdefs");
  define("REPORT_DEFS_DIR","../reports/reportdefs");

  #****************************************************************************
  #*  Read label layout definition xml
  #****************************************************************************
  $labelids = array();
  $labelTitles = array();
  $sqlStatements = array();
  $labelFiles = array();
  
  if ($handle = opendir(LETTER_DEFS_DIR)) {
    while (false !== ($file = readdir($handle))) { 
      if ($file[0] != "." && !is_dir($file)) {
        $fileName = LETTER_DEFS_DIR."/".$file;
        //$xml = file_get_contents($fileName);
        $xml = fileGetContents($fileName);
        if ($xml === FALSE) {
          echo '<p><font class="error">';
          echo $loc->getText('Cannot read letter file: %fileName%',
            array('fileName' => basename($fileName)));
          echo '</font></p>';
          continue;
        }
        $letterDef = new LetterFormat();
        if ($letterDef->parse($xml)) {
          $labelid = $letterDef->getId();
          $labelids[] = $labelid;
          $labelTitles[$labelid] = $letterDef->getTitle();
          $reportDefFilename = REPORT_DEFS_DIR."/".$letterDef->getReportDefFilename();
          $labelFiles[$labelid] = $fileName;
          $initialSort[$labelid] = $letterDef->getGroupBy();
        } else {
          echo $loc->getText("reportListXmlErr");
          echo "<pre>file name: ".H($fileName)."\n".H($letterDef->getXmlErrorString())."</pre>";
          exit();
        }
        $letterDef->destroy();
        unset($letterDef);
        
        #****************************************************************************
        #*  Read label query sql from report definition xml
        #****************************************************************************
        $xml = fileGetContents($reportDefFilename);
        if ($xml === FALSE) {
          array_pop($labelids);
          unset($labelTitles[$labelid]);
          unset($labelFiles[$labelid]);
          unset($initialSort[$labelid]);
          echo '<p><font class="error">';
          echo $loc->getText('Cannot read report file: %fileName%',
            array('fileName' => basename($reportDefFilename)));
          echo '</font></p>';
          continue;
        }
        $rptDef = new ReportDefinition();
        if ($rptDef->parse($xml)) {
          $sqlStatements[$labelid] = $rptDef->getSql();
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

<h1><?php echo $loc->getText("letterListHdr");?></h1>

<?php echo $loc->getText("letterListDesc");?>
<ol>
<?php
  foreach ($labelids as $lblid) {
    $title = $loc->getText($labelTitles[$lblid]);
    $file = $labelFiles[$lblid];
    $sql = $sqlStatements[$lblid];
    $initialSort = $initialSort[$lblid];
    echo "<li><a href=\"../reports/report_criteria.php?reset=Y&amp;rptid=".HURL($lblid)."&amp;title=".HURL($title)."&amp;sql=".HURL($sql)."&amp;letter=".HURL($file)."&amp;initialSort=".HURL($initialSort)."\">".H($title)."</a></li>";
  }
?>
</ol>
<?php include("../shared/footer.php"); ?>
