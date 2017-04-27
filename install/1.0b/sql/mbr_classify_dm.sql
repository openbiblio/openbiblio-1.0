SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`mbr_classify_dm` (
  `code` smallint(6) NOT NULL AUTO_INCREMENT,
  `description` varchar(40) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `default_flg` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `max_fines` decimal(4,2) NOT NULL,
  PRIMARY KEY (`code`)
)   DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=5 ;
INSERT INTO %prfx%.`mbr_classify_dm` (`code`, `description`, `default_flg`, `max_fines`) VALUES
(1, 'adult', 'Y', 0.00),
(2, 'juvenile', 'N', 0.00),
(3, 'Denied', 'N', 99.99),
(4, 'unknown', 'N', 15.00);
