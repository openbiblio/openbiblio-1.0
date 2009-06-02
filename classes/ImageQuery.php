<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/Query.php"));

/******************************************************************************
 * ImageQuery data access component for item images
 *
 * @access public
 ******************************************************************************
 */
class ImageQuery extends Query {
  /****************************************************************************
   * Get images associated with a particular bibid
   * @param integer $bibid
   * @param string $imgurl optional URL of a specific image to select
   * @return array of images or false, if error occurs
   * @access public
   ****************************************************************************
   */
  function get($bibid, $imgurl='') {
    $sql = $this->mkSQL("select * from images where bibid=%N ", $bibid);
    if ($imgurl != '') {
      $sql .= $this->mkSQL("and imgurl=%Q ", $imgurl);
    }
    $sql .= "order by position ";
    $result = $this->exec($sql);
    return $result;
  }
  /****************************************************************************
   * Insert a new thumbnailed image from an uploaded file.
   * @param integer $bibid
   * @param integer $position
   * @param integer $caption
   * @param string $file temporary name for uploaded file
   * @return bool returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function insertThumb($bibid, $position, $caption, $file) {
    return $this->_do_insert($bibid, $file, $position, $caption, 'Thumb', '');
  }
  /****************************************************************************
   * Insert a new link with thumbnail image from an uploaded file.
   * @param integer $bibid
   * @param integer $position
   * @param integer $caption
   * @param string $file temporary name for uploaded file
   * @param string $url URL to link to
   * @return bool returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function insertLink($bibid, $position, $caption, $file, $url) {
    return $this->_do_insert($bibid, $file, $position, $caption, 'Link', $url);
  }
  function appendThumb($bibid, $caption, $file) {
    return $this->_do_insert($bibid, $file, 'end', $caption, 'Thumb', '');
  }
  function appendLink($bibid, $caption, $file, $url) {
    return $this->_do_insert($bibid, $file, 'end', $caption, 'Link', $url);
  }
  function _do_insert($bibid, $file, $position, $caption, $type='Thumb', $url='') {
    $this->lock();
    if ($position == 'end') {
      $result = $this->_append($bibid, $file, $caption, $type, $url);
    } else {
      $result = $this->_insert($bibid, $file, $position, $caption, $type, $url);
    }
    $this->unlock();
    return $result;
  }
  function _append($bibid, $file, $caption, $type, $url) {
    $sql = $this->mkSQL('select max(position) as pos from images where bibid=%N '
                        . 'order by bibid ', $bibid);
    $r = $this->exec($sql);
    if ($r === false) {
      return false;
    }
    if (count($r) == 0 or !is_numeric($r[0]['pos'])) {
      $position = 0;
    } else {
      $position = $r[0]['pos']+1;
    }
    return $this->_insert($bibid, $file, $position, $caption, $type, $url);
  }
  function _insert($bibid, $file, $position, $caption, $type, $url) {
    if (!is_numeric($bibid) or !is_numeric($position)) {
      $this->_errorOccurred = true;
      $this->_error = T("Invalid bibid or position.");
      return false;
    }
    if (ereg('\.(jpg|png)$', strtolower($file['name']), $regs)) {
      $ext = $regs[0];
    } else {
      $this->_errorOccurred = true;
      $this->_error = T("File name end not jpg or png");
      return false;
    }
    $n = $position;
    while (1) {
      $full = OBIB_UPLOAD_DIR.'img-'.$bibid.'-'.$n.$ext;
      $thumb = OBIB_UPLOAD_DIR.'img-'.$bibid.'-'.$n.'-t'.'.jpg';
      if (count($this->get($bibid, $thumb)) == 0) {
        break;
      }
      $n++;
    }
    if (!move_uploaded_file($file['tmp_name'], $full)) {
      $this->_errorOccurred = true;
      $this->_error = T("Unable to move uploaded file.");
      return false;
    }
    if (!$this->_mkThumbnail($full, $thumb)) {
      @unlink($full);
      $this->_errorOccurred = true;
      $this->_error = T("Unable to create thumbnail.");
      return false;
    }
    if ($type == 'Thumb') {
      $url = $full;
    } else {
      @unlink($full);
    }
    if (!$this->_renumber($position)) {
      return false;
    }
    $sql = $this->mkSQL("insert into images values (%N, %Q, %Q, %N, %Q, %Q) ",
                        $bibid, $thumb, $url, $position, $caption, $type);
    return $this->exec($sql);
  }
  function _mkThumbnail($full, $thumb) {
    global $settings;
    if (!function_exists('imagecreatefromjpeg')) {	// no GD extension
      if ($full != $thumb and !copy($full, $thumb)) {
        return false;
      }
      return true;
    }
    $sizeinfo = @getimagesize($full);
    if (!$sizeinfo) {
      return false;
    }
    list($width, $height, $type) = $sizeinfo;
    switch ($type) {
    case 2:		// JPG
      $fimg = @imagecreatefromjpeg($full);
      break;
    case 3:		// PNG
      $fimg = @imagecreatefrompng($full);
      break;
    default:
      return false;
    }
    if (!$fimg) {
      return false;
    }
    $maxw = $settings->get('thumbnail_max_width');
    $maxh = $settings->get('thumbnail_max_height');
    if ($width == 0 or $height == 0) {
      return false;
    }
    if ($width/$height > $maxw/$maxh and $width > $maxw) {
      $w = $maxw;
      $h = $w*$height/$width;
    } else if ($height > $maxh) {
      $h = $maxh;
      $w = $h*$width/$height;
    } else {
      // Use the image as its own thumbnail.
      return copy($full, $thumb);
    }
    $timg = @imagecreatetruecolor($w, $h);
    if (!$timg) {
      return false;
    }
    imagecopyresized($timg, $fimg, 0, 0, 0, 0, $w, $h, $width, $height);
    if (!@imagejpeg($timg, $thumb)) {
      @unlink($thumb);
    }
    return true;
  }
  /****************************************************************************
   * Delete an image
   * @param integer $bibid
   * @param string $imgurl
   * @return bool returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function delete($bibid, $imgurl='') {
    $this->lock();
    $imgs = $this->get($bibid, $imgurl);
    if (!is_array($imgs)) {
      $this->unlock();
      return $imgs;
    }
    foreach ($imgs as $img) {
      if ($img['type'] == 'Thumb'
          and ereg('^'.quotemeta(OBIB_UPLOAD_DIR).'[-.A-Za-z0-9]+', $img['url'])) {
        @unlink($img['url']);
      }
      if (ereg('^'.quotemeta(OBIB_UPLOAD_DIR).'[-.A-Za-z0-9]+', $imgurl)) {
        @unlink($imgurl);
      }
      $sql = $this->mkSQL("delete from images where bibid=%N and imgurl=%Q ",
                          $bibid, $imgurl);
      if (!$this->exec($sql)) {
        $this->unlock();
        return false;
      }
    }
    $this->unlock();
    return true;
  }
  /****************************************************************************
   * Reposition an image
   * @param integer $bibid
   * @param string $imgurl
   * @param integer $position new position
   * @return bool returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function reposition($bibid, $imgurl, $position) {
    $this->lock();
    if (!$this->_renumber($position)) {
      $this->unlock();;
      return false;
    }
    $sql = $this->mkSQL("update images set position=%N "
                        . "where bibid=%N and imgurl=%Q ",
                        $position, $bibid, $imgurl);
    $result = $this->exec($sql);
    // Renumber positions now and again.
    if ($result and rand(0, 3) == 0) {
      if (!$this->exec('set @pos=0')) {
        $result = false;
      } else {
        $result = $this->exec('update images set position=(@pos:=@pos+1) order by position');
      }
    }
    $this->unlock();
    return $result;
  }
  function _renumber($position) {
    $sql = $this->mkSQL("update images set position=position+1 "
                        . "where position >= %N ", $position);
    if (!$this->exec($sql)) {
      return false;
    }
    return true;
  }
  /****************************************************************************
   * Update an image's caption
   * @param integer $bibid
   * @param string $imgurl
   * @param integer $caption new caption
   * @return bool returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function updateCaption($bibid, $imgurl, $caption) {
    $this->lock();
    $sql = $this->mkSQL("update images set caption=%Q "
                        . "where bibid=%N and imgurl=%Q ", $caption, $bibid, $imgurl);
    $r = $this->exec($sql);
    $this->unlock();
    return $r;
  }
}
