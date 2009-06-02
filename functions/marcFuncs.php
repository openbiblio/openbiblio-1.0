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


/*********************************************************************************
 * Draws input html tag of type text.
 * @param string $tag input field tag
 * @param int $occur input field occurance if field is being entered as repeatable
 * @return void
 * @access public
 *********************************************************************************
 */
function printUsmarcText($tag,$subfieldCd,&$marcTags,&$marcSubflds,$showTagDesc){
  $arrayIndex = sprintf("%03d",$tag).$subfieldCd;

  if (($showTagDesc) 
    && (isset($marcTags[$tag]))
    && (isset($marcSubflds[$arrayIndex]))){
    echo $marcTags[$tag]->getDescription();
    echo " (".$marcSubflds[$arrayIndex]->getDescription().")";
  } elseif (isset($marcSubflds[$arrayIndex])){
    echo $marcSubflds[$arrayIndex]->getDescription();
  }
}

?>
