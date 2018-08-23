<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 *
 * This process attempts to implement a query in accordance with
 * SRU 1.1 and CQL 1.1 specifications . As written it uses the
 * 'Dublin Core' (dc) query schema and the 'marcxml' record schema for response.
 */


$parser = xml_parser_create();
$hits = array();
$ttlHits = 0;
$maxHits = 10;
foreach ($postVars['hosts'] as $host) {
        $query = '?version=1.1&operation=searchRetrieve&query='.urlencode($_POST['lookupVal']).'&startRecord=1&maximumRecords='.$maxHits.'5&recordSchema=marcxml';
        $sruResults = file_get_contents('http://'.$host['host'].':'.$host['port'].'/'.$host['db'].$query);
        $hits[] = $sruResults;
        $xml = new SimpleXMLElement($sruResults);
        $xml->registerXPathNamespace('srw', 'http://www.loc.gov/zing/srw/');
        $numberHitsInHost = $xml->xpath('//srw:numberOfRecords/text()');
        $ttlHits += (int)$numberHitsInHost;
}
xml_parser_free($parser);
?>
