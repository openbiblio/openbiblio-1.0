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
 * Does the same as file_get_contents but will work with PHP version < 4.3
 * @return string
 * @access public
 *********************************************************************************
 */
function fileGetContents($filename){
  $result = "";
  $handle = @fopen ($filename, "rb");
  if ($handle === FALSE) {
    return FALSE;
  }
  while (!feof ($handle)) {
    $buffer = fgets($handle, 4096);
    if ($buffer === FALSE && !feof($handle)) {
      return FALSE;
    }
    $result .= $buffer;
  }
  if (!fclose ($handle)) {
    /* We don't care because we've finished reading. */
    // return FALSE;
  }
  return $result;
}
?>
