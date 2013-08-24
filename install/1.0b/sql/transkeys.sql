SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`transkeys` (
  `transKeyID` int(11) NOT NULL AUTO_INCREMENT,
  `transKeySectionName` varchar(50) NOT NULL,
  `transKeyText` varchar(100) NOT NULL,
  PRIMARY KEY (`transKeyID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
