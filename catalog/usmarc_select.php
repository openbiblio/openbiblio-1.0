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
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/UsmarcBlockDm.php");
  require_once("../classes/UsmarcBlockDmQuery.php");
  require_once("../classes/UsmarcTagDm.php");
  require_once("../classes/UsmarcTagDmQuery.php");
  require_once("../classes/UsmarcSubfieldDm.php");
  require_once("../classes/UsmarcSubfieldDmQuery.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Retrieving get var
  #****************************************************************************
  if (isset($HTTP_GET_VARS["block"])) {
    $selectedBlock = $HTTP_GET_VARS["block"];
  } else {
    $selectedBlock = "";
  }
  if (isset($HTTP_GET_VARS["tag"])) {
    $selectedTag = $HTTP_GET_VARS["tag"];
  } else {
    $selectedTag = "";
  }
  if (isset($HTTP_GET_VARS["subfld"])) {
    $selectedSubfld = $HTTP_GET_VARS["subfld"];
  } else {
    $selectedSubfld = "";
  }
  if (isset($HTTP_GET_VARS["retpage"])) {
    $retPage = $HTTP_GET_VARS["retpage"];
  } else {
    $retPage = "";
  }

  #****************************************************************************
  #*  Loading up an array ($marcArray) with the USMarc tag descriptions.
  #****************************************************************************
  $marcBlockDmQ = new UsmarcBlockDmQuery();
  $marcBlockDmQ->connect();
  if ($marcBlockDmQ->errorOccurred()) {
    $marcBlockDmQ->close();
    displayErrorPage($marcBlockDmQ);
  }
  $marcBlockDmQ->execSelect();
  if ($marcBlockDmQ->errorOccurred()) {
    $marcBlockDmQ->close();
    displayErrorPage($marcBlockDmQ);
  }
  $marcBlocks = $marcBlockDmQ->fetchRows();
  $marcBlockDmQ->close();


?>

<html>
<head>
<style type="text/css">
  <?php include("../css/style.php");?>
</style>
<meta name="description" content="OpenBiblio Library Automation System">
<title><?php echo $loc->getText("usmarcSelectHdr"); ?></title>

<script language="JavaScript">
<!--
function backToMain(URL) {
    var mainWin;
    mainWin = window.open(URL,"main");
    mainWin.focus();
    this.close();
}
-->
</script>



</head>
<body bgcolor="<?php echo OBIB_PRIMARY_BG;?>" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" onLoad="self.focus()">

<!-- **************************************************************************************
     * Header
     **************************************************************************************-->
<table class="primary" width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr bgcolor="<?php echo OBIB_TITLE_BG;?>">
    <td width="100%" class="title" valign="top">
      <?php echo $loc->getText("usmarcSelectHdr"); ?>
    </td>
    <td class="title" valign="top" nowrap="yes"><font class="small"><a href="javascript:window.close()"><font color="<?php echo OBIB_TITLE_FONT_COLOR?>">Close Window</font></a>&nbsp;&nbsp;</font></td>
  </tr>
</table>
<!-- **************************************************************************************
     * Line
     **************************************************************************************-->
<table class="primary" width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr bgcolor="<?php echo OBIB_BORDER_COLOR;?>">
    <td><img src="../images/shim.gif" width="1" height="1" border="0"></td>
  </tr>
</table>
<font class="primary">
<!-- **************************************************************************************
     * beginning of main body
     **************************************************************************************-->

<h1><?php echo $loc->getText("usmarcSelectInst"); ?>:</h1>
<table cellpadding="0" cellspacing="0" border="0">
  <?php
    foreach($marcBlocks as $blockKey => $block) {
      #***************************************
      #*  check for a selected block
      #***************************************
      if (strcmp($selectedBlock,$blockKey) == 0) {
        ?>
        <tr><td class="noborder" nowrap>
        <a href="../catalog/usmarc_select.php?retpage=<?php echo $retPage; ?>" class="nav">
          -&nbsp;</a>
        </td><td class="noborder" colspan="3">
          <?php echo $blockKey." - ".$block->getDescription(); ?>
        </td></tr>
        <?php
        #***************************************
        #*  read all tags for selected block
        #***************************************
        $marcTagDmQ = new UsmarcTagDmQuery();
        $marcTagDmQ->connect();
        if ($marcTagDmQ->errorOccurred()) {
          $marcTagDmQ->close();
          displayErrorPage($marcTagDmQ);
        }
        $marcTagDmQ->execSelect($selectedBlock);
        if ($marcTagDmQ->errorOccurred()) {
          $marcTagDmQ->close();
          displayErrorPage($marcTagDmQ);
        }
        $marcTags = $marcTagDmQ->fetchRows();
        $marcTagDmQ->close();
        if ($marcTags != false) {
          foreach($marcTags as $tagKey => $tag) {
            #***************************************
            #*  check for a selected tag
            #***************************************
            if (strcmp($selectedTag,$tagKey) == 0) {
              ?>
              <tr><td class="noborder"></td>
              <td class="noborder" nowrap>
              <a href="../catalog/usmarc_select.php?retpage=<?php echo $retPage; ?>&block=<?php echo $blockKey; ?>" class="nav">
              -&nbsp;</a>
              </td><td class="noborder" colspan="2">
                <?php echo $tagKey." - ".$tag->getDescription(); ?></a>
              </td></tr>
              <?php
              #***************************************
              #*  read all subfields for selected tag
              #***************************************
              $marcSubfldDmQ = new UsmarcSubfieldDmQuery();
              $marcSubfldDmQ->connect();
              if ($marcSubfldDmQ->errorOccurred()) {
                $marcSubfldDmQ->close();
                displayErrorPage($marcSubfldDmQ);
              }
              $marcSubfldDmQ->execSelect($selectedTag);
              if ($marcSubfldDmQ->errorOccurred()) {
                $marcSubfldDmQ->close();
                displayErrorPage($marcSubfldDmQ);
              }
              $marcSubflds = $marcSubfldDmQ->fetchRows();
              $marcSubfldDmQ->close();

              if ($marcSubflds != false) {
                foreach($marcSubflds as $subfldKey => $subfld) {
                  ?>
                  <tr><td class="noborder" colspan="2"></td>
                  <td class="noborder">
                  <a href="javascript:backToMain('../catalog/<?php echo $retPage; ?>?tag=<?php echo $selectedTag; ?>&subfld=<?php echo $subfld->getSubfieldCd(); ?>')" class="nav">
                    <?php echo $loc->getText("usmarcSelectUse");?></a>
                  </td><td class="noborder" width="100%">
                  <?php echo $subfld->getSubfieldCd()." - ".$subfld->getDescription(); ?></a><br>
                  </td></tr>
                  <?php
                }
              } else {
                ?>
                <tr><td class="noborder" colspan="2"></td>
                <td class="noborder">
                +</a>
                </td><td class="noborder">
                <?php echo $loc->getText("usmarcSelectNoTags"); ?>
                </td></tr>
                <?php
              }


            } else {
              #***************************************
              #*  draw unselected tags
              #***************************************
              ?>
              <tr><td class="noborder"></td>
              <td class="noborder">
              <a href="../catalog/usmarc_select.php?retpage=<?php echo $retPage; ?>&block=<?php echo $blockKey; ?>&tag=<?php echo $tagKey; ?>" class="nav">
              +</a>
              </td><td class="noborder" colspan="2" width="100%">
                <?php echo $tagKey." - ".$tag->getDescription(); ?></a>
              </td></tr>
              <?php
            }
          }
        } else {
          ?>
          <tr><td class="noborder">
          </td><td class="noborder" colspan="3" width="100%">
            <?php echo $loc->getText("usmarcSelectNoTags"); ?>
          </td></tr>

        <?php
        }
      } else {
        #***************************************
        #*  draw unselected blocks
        #***************************************
        ?>
        <tr><td class="noborder">
        <a href="../catalog/usmarc_select.php?retpage=<?php echo $retPage; ?>&block=<?php echo $blockKey; ?>" class="nav">
          +</a>
        </td><td class="noborder" colspan="3" width="100%">
          <?php echo $blockKey." - ".$block->getDescription(); ?>
        </td></tr>

        <?php
      }
    }
  ?>
</table>


<!-- **************************************************************************************
     * Footer
     **************************************************************************************-->
<br><br><br>
</font>
<font face="Arial, Helvetica, sans-serif" size="1" color="<?php echo OBIB_PRIMARY_FONT_COLOR;?>">
<center>
  <br><br>
  Powered by OpenBiblio<br>
  Copyright &copy; 2002 <a href="http://dave.stevens.name">Dave Stevens</a><br>
  under the
  <a href="../shared/copying.html">GNU General Public License</a>
</center>
<br>
</font>
    </td>
  </tr>
</table>
</body>
</html>