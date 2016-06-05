SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`transsections` (
  `transSectionID` int(11) NOT NULL AUTO_INCREMENT,
  `transSectionProjID` int(11) NOT NULL DEFAULT '1',
  `transSectionName` varchar(50) NOT NULL,
  `transSectionIsProtected` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`transSectionID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
