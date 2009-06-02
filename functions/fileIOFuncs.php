<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/*********************************************************************************
 * Does the same as file_get_contents but will work with PHP version < 4.3
 * @return string
 * @access public
 *********************************************************************************
 */
function fileGetContents($filename){
  $result = "";
  $handle = fopen ($filename, "r");
  while (!feof ($handle)) {
    $buffer = fgets($handle, 4096);
    $result = $result.$buffer;
  }
  fclose ($handle);
  return $result;
}
