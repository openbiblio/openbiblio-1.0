SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`calendar` (
  `calendar` int(11) NOT NULL,
  `date` date NOT NULL,
  `open` enum('Yes','No','Unset') NOT NULL,
  PRIMARY KEY (`calendar`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
