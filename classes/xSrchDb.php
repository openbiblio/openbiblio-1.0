<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	require_once(REL(__FILE__, "../functions/marcFuncs.php"));
	require_once(REL(__FILE__, '../classes/Queryi.php'));
	require_once(REL(__FILE__, "../model/MediaTypes.php"));
	require_once(REL(__FILE__, "../model/Collections.php"));
	require_once(REL(__FILE__, "../model/Copies.php"));
 	require_once(REL(__FILE__, "../model/BiblioCopyFields.php"));

/**
 * This class provides Biblio search facilities
 * @author Luuk Jansen
 * @author Fred LaPlante
 */

class SrchDb extends Queryi {
	public $bibid;
	private $createDt;
	private $daysDueBack;
	private $matlCd;
	private $collCd;
	private $imageFile;
	private $opacFlg;
	private $nCpy;

	public function __construct() {
		parent::__construct();
	}
	public function getData (){
		//function mkBiblioArray($dbObj) {
	 	$rslt['barCd'] = $this->barCd;
	 	$rslt['bibid'] = $this->bibid;
	 	$rslt['imageFile'] = $this->imageFile;
	 	$rslt['daysDueBack'] = $this->daysDueBack;
	 	$rslt['createDt'] = $this->createDt;
	 	$rslt['matlCd'] = $this->matlCd;
	 	$rslt['collCd'] = $this->collCd;
	 	$rslt['opacFlg'] = $this->opacFlg;
		$rslt['avIcon'] = $this->avIcon;
		$rslt['nCpy'] = $this->nCpy;
	 	$rslt['data'] = $this->getBiblioDetail();
	 	return $rslt;
	}

} // class

?>
