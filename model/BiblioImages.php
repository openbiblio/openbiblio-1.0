<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DBTable.php"));

class BiblioImages extends DBTable {
	public function __construct() {
		parent::__construct();
		$this->setName('images');
		$this->setFields(array(
			'bibid'=>'number',
			'imgurl'=>'string',
			'url'=>'string',
			'position'=>'number',
			'caption'=>'string',
			'type'=>'string',
		));
		$this->setKey('bibid');
		$this->setForeignKey('bibid', 'biblio', 'bibid');
	}

	protected function validate_el($rec, $insert) { /*return array();*/ }

	public function getBiblioMatches($fields) {
		$sql = 'SELECT DISTINCT(i.bibid), i.url, s.subfield_data as data '.
					 'FROM images i, biblio b, biblio_field f, biblio_subfield s '.
					 'WHERE (f.bibid = i.bibid) AND (s.fieldid = f.fieldid) AND (("A" = "B") ';
		foreach ($fields as $field) {
			$set = explode('$',$field);
			$sql .= " OR ((f.tag = '$set[0]') AND (s.subfield_cd = '$set[1]')) ";
		}
		$sql .= ' )';
		$sql .= ' ORDER BY data';

		if ($this->iter) {
			$c = $this->iter;	# Silly PHP
			return new $c($this->select($sql));
		} else
			return $this->select($sql);
	}
	function getOne() {
		$bibid = func_get_args(0);
		$imgurl = func_get_args(1);
		$sql = $this->mkSQL("select * from images where bibid=%N "
			. "and imgurl=%Q ", $bibid, $imgurl);
		return $this->select1($sql);
	}
	function maybeGetOne() {
		$bibid = func_get_args(0);
		$imgurl = func_get_args(1);
		$sql = $this->mkSQL("select * from images where bibid=%N "
			. "and imgurl=%Q ", $bibid, $imgurl);
		return $this->select01($sql);
	}
	function getByBibid($bibid) {
		$sql = $this->mkSQL("select * from images where bibid=%N ", $bibid);
		$sql .= "order by position ";
		return $this->select($sql);
	}
	function insertThumb_e($bibid, $position, $caption, $file) {
		return $this->_do_insert_e($bibid, $file, $position, $caption, 'Thumb', '');
	}
	function insertLink_e($bibid, $position, $caption, $file, $url) {
		return $this->_do_insert_e($bibid, $file, $position, $caption, 'Link', $url);
	}
	function appendThumb_e($bibid, $caption, $file) {
		return $this->_do_insert_e($bibid, $file, 'end', $caption, 'Thumb', '');
	}
	function appendLink_e($bibid, $caption, $file, $url) {
		return $this->_do_insert_e($bibid, $file, 'end', $caption, 'Link', $url);
	}
	function _do_insert_e($bibid, $file, $position, $caption, $type='Thumb', $url='') {
		$this->lock();
		if ($position == 'end') {
			$sql = $this->mkSQL('select max(position) as pos from images where bibid=%N '
				. 'order by bibid ', $bibid);
			$r = $this->select01($sql);
			if (!$r or !is_numeric($r['pos'])) {
				$position = 0;
			} else {
				$position = $r['pos']+1;
			}
		}
		$result = $this->_insert_e($bibid, $file, $position, $caption, $type, $url);
		$this->unlock();
		return $result;
	}
	function _insert_e($bibid, $file, $position, $caption, $type, $url) {
		if (!is_numeric($bibid) or !is_numeric($position)) {
			return new Error(T("Invalid bibid or position."));
		}
		$n = $position;
		/*
		if (preg_match('/\.(jpg|png)$/', strtolower($file['name']), $regs)) {
			$ext = $regs[0];
		} else {
			return new Error(T("File name end not jpg or png"));
		}
		$n = $position;
		while (1) {
			//$full = OBIB_UPLOAD_DIR.'img-'.$bibid.'-'.$n.$ext;
			$full = OBIB_UPLOAD_DIR.$url;
			$thumb = OBIB_UPLOAD_DIR.'img-'.$bibid.'-'.$n.'-t'.'.jpg';
			if (!$this->maybeGetOne($bibid, $thumb)) {
				break;
			}
			$n++;
		}
		if (!move_uploaded_file($file['tmp_name'], $full)) {
			return new Error(T("Unable to move uploaded file."));
		}
		if (!$this->_mkThumbnail($full, $thumb)) {
			@unlink($full);
			return new Error(T("Unable to create thumbnail."));
		}
//		if ($type == 'Thumb') {	//this block does not play well with 
//			$url = $full;					// existing_item's photo editor function
//		} else {
//			@unlink($full);
//		}
		*/
		$this->_renumber($position);
		$sql = $this->mkSQL("insert into images values (%N, %Q, %Q, %N, %Q, %Q) ",
												$bibid, $thumb, $url, $position, $caption, $type);
		$this->act($sql);
		return NULL;
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
		$maxw = Settings::get('thumbnail_width');
		$maxh = Settings::get('thumbnail_height');
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
	function deleteOne() {
		$bibid = func_get_args(0);
		$imgurl = func_get_args(1);
		$this->lock();
		$img = $this->getOne($bibid, $imgurl);
		if ($img['type'] == 'Thumb'
				and preg_match('/^'.quotemeta(OBIB_UPLOAD_DIR).'[-.A-Za-z0-9]+/', $img['url'])) {
			@unlink($img['url']);
		}
		if (preg_match('/^'.quotemeta(OBIB_UPLOAD_DIR).'[-.A-Za-z0-9]+/', $imgurl)) {
			@unlink($imgurl);
		}
		$sql = $this->mkSQL("delete from images where bibid=%N and imgurl=%Q ",
												$bibid, $imgurl);
		$this->act($sql);
		$this->unlock();
	}
	function deleteByBibid($bibid) {
		$this->lock();
		$imgs = $this->getByBibid($bibid);
		while ($img = $imgs->fetch_assoc()) {
			try {
				//@unlink("../photos/".$img['url']);
				//@unlink("../photos/".$img['imgurl']);
				unlink("../photos/".$img['url']);
			}
			catch (Exception $err){
				echo "Error while trying to 'unlink()' photo for biblio #{$bibid}.";
			}
			$sql = $this->mkSQL("delete from images where bibid=%N and imgurl=%Q ",
				$bibid, $img['imgurl']);
			$this->act($sql);
		}
		$this->unlock();
	}
	function reposition($bibid, $imgurl, $position) {
		$this->lock();
		$this->_renumber($position);
		$sql = $this->mkSQL("update images set position=%N "
			. "where bibid=%N and imgurl=%Q ",
			$position, $bibid, $imgurl);
		$this->act($sql);
		// Renumber all positions now and again.
		if (rand(0, 3) == 0) {
			$this->act('set @pos=0');
			$this->act('update images set position=(@pos:=@pos+1) order by position');
		}
		$this->unlock();
	}
	function _renumber($position) {
		$sql = $this->mkSQL("update images set position=position+1 "
			. "where position >= %N ", $position);
		$this->act($sql);
	}
	function updateCaption($bibid, $imgurl, $caption) {
		$sql = $this->mkSQL("update images set caption=%Q "
			. "where bibid=%N and imgurl=%Q ", $caption, $bibid, $imgurl);
		$this->act($sql);
	}
}
