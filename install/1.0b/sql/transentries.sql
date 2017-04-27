SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`transentries` (
  `transEntryID` int(11) NOT NULL AUTO_INCREMENT,
  `transLocaleCode` varchar(10) NOT NULL,
  `transKeyText` varchar(100) NOT NULL,
  `transEntryText` varchar(255) NOT NULL,
  `transSectionName` varchar(50) NOT NULL,
  PRIMARY KEY (`transEntryID`),
  UNIQUE KEY `transLocaleCode` (`transLocaleCode`,`transKeyText`,`transSectionName`)
)  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
