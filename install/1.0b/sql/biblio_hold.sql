SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`biblio_hold` (
  `bibid` int(11) NOT NULL,
  `copyid` int(11) NOT NULL,
  `holdid` int(11) NOT NULL AUTO_INCREMENT,
  `hold_begin_dt` datetime NOT NULL,
  `mbrid` int(11) NOT NULL,
  PRIMARY KEY (`holdid`),
  KEY `mbr_index` (`mbrid`),
  KEY `bibid_index` (`bibid`),
  KEY `copyid_index` (`copyid`)
)  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
