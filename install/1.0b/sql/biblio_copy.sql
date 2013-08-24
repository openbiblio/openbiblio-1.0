SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`biblio_copy` (
  `bibid` int(11) NOT NULL DEFAULT '0',
  `copyid` int(11) NOT NULL AUTO_INCREMENT,
  `create_dt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_change_dt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_change_userid` int(11) NOT NULL DEFAULT '0',
  `barcode_nmbr` varchar(20) NOT NULL DEFAULT '',
  `copy_desc` varchar(160) DEFAULT NULL,
  `vendor` varchar(100) DEFAULT NULL,
  `fund` varchar(100) DEFAULT NULL,
  `price` varchar(10) DEFAULT NULL,
  `expiration` varchar(100) DEFAULT NULL,
  `histid` int(11) DEFAULT NULL,
  `siteid` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`copyid`),
  UNIQUE KEY `histid_idx` (`histid`),
  KEY `bibid_idx` (`bibid`),
  KEY `barcode_index` (`barcode_nmbr`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=73 ;
