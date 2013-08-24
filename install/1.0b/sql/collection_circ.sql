SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`collection_circ` (
  `code` smallint(6) NOT NULL AUTO_INCREMENT,
  `days_due_back` tinyint(3) unsigned NOT NULL,
  `daily_late_fee` decimal(4,2) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;
INSERT INTO %prfx%.`collection_circ` (`code`, `days_due_back`, `daily_late_fee`) VALUES
(1, 30, 0.01),
(2, 14, 0.10),
(3, 7, 0.25),
(4, 7, 0.10),
(5, 7, 0.10),
(11, 7, 0.10),
(12, 7, 0.10),
(13, 7, 0.10),
(14, 7, 0.10),
(15, 7, 0.10),
(6, 7, 0.10),
(18, 7, 0.00);
