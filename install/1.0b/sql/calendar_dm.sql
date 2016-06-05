SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`calendar_dm` (
  `code` int(11) NOT NULL AUTO_INCREMENT,
  `description` char(32) NOT NULL,
  `default_flg` char(1) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;
INSERT INTO %prfx%.`calendar_dm` (`code`, `description`, `default_flg`) VALUES
(1, 'Fourth of July', ''),
(2, 'Normal', ''),
(3, 'Another one', ''),
(4, 'Fred''s Birthbay', '');
