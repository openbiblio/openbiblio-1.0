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

/******************************************************************************
 * Localize
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class Localize {
  var $_trans = NULL;

  /****************************************************************************
   * @return boolean true if data is valid, otherwise false.
   * @access public
   ****************************************************************************
   */
  function Localize ($locale, $section) {
    $localePath = "../locale/".$locale."/".$section.".php";
    include($localePath);
    $this->_trans = $trans;
    return true;
  }

  /****************************************************************************
   * @return string returns the translated text for the specified key
   *                If the key is not found in the translation table then the
   *                key will be returned instead of the translated value.
   * @param string $key key name for the translation table
   * @param array $vars associative array containing substitution variables
   * @access public
   ****************************************************************************
   */
  function getText ($key, $vars=NULL) {
    $text = $key;
    $transFunc = @$this->_trans[$key];
    if ($vars != NULL) {
      foreach($vars as $varKey => $value) {
        $search = "%".$varKey."%";
        if ($transFunc) {
          $transFunc = str_replace($search,addslashes($value),$transFunc);
        } else {
          $text = str_replace($search,$value,$text);
        }
      }
    }
    @eval ($transFunc);
    if (OBIB_HIGHLIGHT_I18N_FLG) {
      $text = "<i>".$text."</i>";
    }
    return $text;
  }

}

?>
