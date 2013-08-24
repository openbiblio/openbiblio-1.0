SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`biblio_field` (
  `bibid` int(11) NOT NULL,
  `fieldid` int(11) NOT NULL AUTO_INCREMENT,
  `seq` int(11) NOT NULL,
  `tag` char(3) NOT NULL,
  `ind1_cd` char(1) DEFAULT NULL,
  `ind2_cd` char(1) DEFAULT NULL,
  `field_data` text,
  `display` text,
  PRIMARY KEY (`fieldid`),
  KEY `bibid_idx` (`bibid`),
  KEY `tag_idx` (`tag`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22233 ;
