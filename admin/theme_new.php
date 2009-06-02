<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "admin";
  $nav = "themes";
  $restrictInDemo = true;
  require_once(REL(__FILE__, "../shared/logincheck.php"));

  require_once(REL(__FILE__, "../model/Themes.php"));

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************

  if (count($_POST) == 0) {
    header("Location: ../admin/theme_new_form.php");
    exit();
  }

  $themes = new Themes;
  $theme = array(
    'theme_name'=>$_POST['themeName'],
    'title_bg'=>$_POST['titleBg'],
    'title_font_face'=>$_POST['titleFontFace'],
    'title_font_size'=>$_POST['titleFontSize'],
    'title_font_bold'=>isset($_POST['titleFontBold']),
    'title_align'=>$_POST['titleAlign'],
    'title_font_color'=>$_POST['titleFontColor'],

    'primary_bg'=>$_POST['primaryBg'],
    'primary_font_face'=>$_POST['primaryFontFace'],
    'primary_font_size'=>$_POST['primaryFontSize'],
    'primary_font_color'=>$_POST['primaryFontColor'],
    'primary_link_color'=>$_POST['primaryLinkColor'],
    'primary_error_color'=>$_POST['primaryErrorColor'],

    'alt1_bg'=>$_POST['alt1Bg'],
    'alt1_font_face'=>$_POST['alt1FontFace'],
    'alt1_font_size'=>$_POST['alt1FontSize'],
    'alt1_font_color'=>$_POST['alt1FontColor'],
    'alt1_link_color'=>$_POST['alt1LinkColor'],

    'alt2_bg'=>$_POST['alt2Bg'],
    'alt2_font_face'=>$_POST['alt2FontFace'],
    'alt2_font_size'=>$_POST['alt2FontSize'],
    'alt2_font_color'=>$_POST['alt2FontColor'],
    'alt2_link_color'=>$_POST['alt2LinkColor'],
    'alt2_font_bold'=>isset($_POST['alt2FontBold']),

    'border_color'=>$_POST['borderColor'],
    'border_width'=>$_POST['borderWidth'],
    'padding'=>$_POST['tablePadding'],
  );

  list($id, $errors) = $themes->insert_el($theme);
  if (!empty($errors)) {
    FieldError::backToForm('../admin/theme_new_form.php', $errors);
  }

  #**************************************************************************
  #*  Show success page
  #**************************************************************************
  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

  echo T("Theme, %name%, has been added.", array('name'=>H($theme['theme_name']))).'<br /><br />';
  echo '<a href="../admin/theme_list.php">'. T("Return to theme list")'.</a>';

  php Page::footer();
