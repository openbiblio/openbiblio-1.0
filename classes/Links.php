<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

class LinkUrl {
	function LinkUrl($url, $idparam, $defparams) {
		$this->url = $url;
		$this->idparam = $idparam;
		$this->defparams = $defparams;
	}
	function get($id, $params=NULL) {
		global $tab;	# FIXME - get rid of $tab
		if ($params==NULL) {
			$params = array();
		}
		$params = array_merge($this->defparams, $params);
		$params[$this->idparam] = $id;
		if ($tab == 'opac') {
			$params['tab'] = 'opac';
		}
		$ps = array();
		foreach ($params as $k=>$v) {
			$ps[] = U($k).'='.U($v);
		}
		if (!empty($ps)) {
			return $this->url . '?' . implode('&', $ps);
		} else {
			return $this->url;
		}
	}
}

$_Links_urls = array(
	'biblio'=>new LinkUrl('../shared/biblio_view.php', 'bibid', array()),
	'booking_opac'=>new LinkUrl('../opac/booking.php', 'bookingid', array()),
	'subject'=>new LinkUrl('../shared/biblio_search.php', 'searchText', array(
		'searchType'=>'subject',
		'exact'=>'1'
	)),
);
class Links {
	function mkLink($type, $id, $text, $params=NULL) {
		global $_Links_urls;
		if (!isset($_Links_urls[$type])) {
			Fatal::internalError(T("No such link type: ").$type);
		}
		$url = $_Links_urls[$type]->get($id, $params);
		return "<a href=\"".H($url)."\">".$text."</a>\n";
	}
}
