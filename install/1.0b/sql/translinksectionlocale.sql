SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`translinksectionlocale` (
  `transLinkID` int(11) NOT NULL AUTO_INCREMENT,
  `transSectionName` varchar(50) DEFAULT NULL,
  `transLocaleCode` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`transLinkID`),
  UNIQUE KEY `transSectionName` (`transSectionName`,`transLocaleCode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
