SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`translocales` (
  `transLocaleID` int(11) NOT NULL AUTO_INCREMENT,
  `transLocaleCode` varchar(10) NOT NULL,
  `transLocaleText` varchar(50) NOT NULL,
  `transLocaleEnglishText` varchar(50) NOT NULL,
  PRIMARY KEY (`transLocaleID`)
)  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
