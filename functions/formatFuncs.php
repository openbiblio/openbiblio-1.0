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
 * Same as ctype_alnum without requiring PHP 4.3 or 4.2 with ctype turned on
 * @param String $text text to check
 * @return boolean
 * @access public
 *********************************************************************************
 */
function ctypeAlnum($text){
  ereg("[a-zA-Z0-9]+",$text,$regs);
  if (count($regs) == 0) {
    return false;
  }
  if ($regs[0] == $text) {
    return true;
  }
  return false;
}

/*********************************************************************************
 * Displays a number as a currency according to the currency format for the
 * specified locale
 * @param decimal $amount Amount that you want to format.
 * @param int $decimals number of decimals
 * @return string
 * @access public
 *********************************************************************************
 */
function moneyFormat($amount,$decimals){
  // get local info
  $localeInfo = localeconv();
  if ($amount < 0) {
    $prefix = "n";
    $sign = $localeInfo["negative_sign"];
  } else {
    $prefix = "p";
    $sign = $localeInfo["positive_sign"];
  }
  $currencySymbol = $localeInfo["currency_symbol"];
  $dec_point = $localeInfo["mon_decimal_point"];
  $thousand_sep = $localeInfo["mon_thousands_sep"];
  if ($localeInfo[$prefix."_sep_by_space"]) {
    $sep = " ";
  } else {
    $sep = "";
  }

  // format number
  $result = number_format(abs($amount),$decimals,$dec_point,$thousand_sep);

/* add currency symbol and sign
   _sign_posn doc:
   0 Parentheses surround the quantity and currency_symbol  
   1 The sign string precedes the quantity and currency_symbol  
   2 The sign string succeeds the quantity and currency_symbol  
   3 The sign string immediately precedes the currency_symbol  
   4 The sign string immediately succeeds the currency_symbol  
*/

  if ($localeInfo[$prefix."_sign_posn"] == 0) {
    if ($localeInfo[$prefix."_cs_precedes"]) {
      $result = "(".$currencySymbol.$sep.$result.")";
    } else {
      $result = "(".$result.$sep.$currencySymbol.")";
    }
  }elseif ($localeInfo[$prefix."_sign_posn"] == 1) {
    if ($localeInfo[$prefix."_cs_precedes"]) {
      $result = $sign.$currencySymbol.$sep.$result;
    } else {
      $result = $sign.$result.$sep.$currencySymbol;
    }
  }elseif ($localeInfo[$prefix."_sign_posn"] == 2) {
    if ($localeInfo[$prefix."_cs_precedes"]) {
      $result = $currencySymbol.$sep.$result.$sign;
    } else {
      $result = $result.$sep.$currencySymbol.$sign;
    }
  }elseif ($localeInfo[$prefix."_sign_posn"] == 3) {
    if ($localeInfo[$prefix."_cs_precedes"]) {
      $result = $sign.$currencySymbol.$sep.$result;
    } else {
      $result = $result.$sep.$sign.$currencySymbol;
    }
  }elseif ($localeInfo[$prefix."_sign_posn"] == 4) {
    if ($localeInfo[$prefix."_cs_precedes"]) {
      $result = $currencySymbol.$sign.$sep.$result;
    } else {
      $result = $result.$sep.$currencySymbol.$sign;
    }
  }
  
  return $result;
}
?>
