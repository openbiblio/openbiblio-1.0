<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
require_once(REL(__FILE__, "../classes/DmTable.php"));

class CopyStatus extends DmTable {
	public function __construct() {
//echo "in CopyStatus::__construct";echo "<br />\n";
		$this->setName('biblio_status_dm');
		$this->setFields(array(
			'code'=>'string',
			'description'=>'string',
			'default_flg'=>'string',
		));
		$this->setKey('code');
	}

    public function getStatusCds() {
		$recs = $this->getAll('description');
//echo "{";print_r($recs);echo "}";
        if (isset($recs)) {
            while($row = $recs->fetch_assoc()) {
               $cds[] = $row['description'];
            }
		    return $cds;
        } else {
            return $recs;
        }
    }
}
