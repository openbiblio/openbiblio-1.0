<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once(REL(__FILE__, "../classes/DBTable.php"));

class Themes extends DBTable {
	public function __construct() {
		parent::__construct();
		$this->setName('theme');
		$this->setFields(array(
			'themeid'=>'number',
			'theme_name'=>'string',
			'title_bg'=>'string',
			'title_font_face'=>'string',
			'title_font_size'=>'number',
			'title_font_bold'=>'string',
			'title_font_color'=>'string',
			'title_align'=>'string',
			'primary_bg'=>'string',
			'primary_font_face'=>'string',
			'primary_font_size'=>'number',
			'primary_font_color'=>'string',
			'primary_link_color'=>'string',
			'primary_error_color'=>'string',
			'alt1_bg'=>'string',
			'alt1_font_face'=>'string',
			'alt1_font_size'=>'number',
			'alt1_font_color'=>'string',
			'alt1_link_color'=>'string',
			'alt2_bg'=>'string',
			'alt2_font_face'=>'string',
			'alt2_font_size'=>'number',
			'alt2_font_color'=>'string',
			'alt2_link_color'=>'string',
			'alt2_font_bold'=>'string',
			'border_color'=>'string',
			'border_width'=>'number',
			'table_padding'=>'number',
		));
		$this->setKey('themeid');
        $this->setReq(array(
            'theme_name', 'border_color', 'border_width', 'table_padding',
        ));
		$this->setSequenceField('themeid');
	}
	function getSelect($all=false) {
		$select = array();
		if ($all) {
			$select['all'] = 'All';
		}
		$recs = $this->getAll('theme_name');
		while ($rec = $recs->fetch_assoc()) {
			$select[$rec['themeid']] = $rec['theme_name'];
		}
		return $select;
	}
}
