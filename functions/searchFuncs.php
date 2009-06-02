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
 * explodes a quoted string into words
 * @param string $str String to be exploded
 * @return stringArray
 * @access public
 *********************************************************************************
 */
function explodeQuoted($str) {
  if ($str == ""){
    $elements[]="";
    return $elements; 
  }

  $inQuotes=false; 

  $words=explode(" ", $str); 
  foreach($words as $word) { 
    if($inQuotes==true) { 
      // add word to the last element 
      $elements[sizeof($elements)-1].=" ".str_replace('"','',$word); 
      if($word[strlen($word)-1]=="\"") $inQuotes=false; 
    } else { 
      // create a new element 
      $elements[]=str_replace('"','',$word);
      if($word[0]=="\"" && $word[strlen($word)-1]!="\"") $inQuotes=true; 
    } 
  } 
  return $elements; 
}

?>
