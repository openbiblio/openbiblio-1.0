SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`biblio_subfield` (
  `bibid` int(11) NOT NULL,
  `fieldid` int(11) NOT NULL,
  `subfieldid` int(11) NOT NULL AUTO_INCREMENT,
  `seq` int(11) NOT NULL,
  `subfield_cd` char(1) NOT NULL,
  `subfield_data` text NOT NULL,
  PRIMARY KEY (`subfieldid`),
  KEY `bibid_idx` (`bibid`),
  KEY `fieldid_idx` (`fieldid`),
  KEY `subfield_cd_idx` (`subfield_cd`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22417 ;
