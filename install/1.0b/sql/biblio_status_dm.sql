SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`biblio_status_dm` (
  `code` char(3) NOT NULL,
  `description` varchar(40) NOT NULL,
  `default_flg` char(1) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO %prfx%.`biblio_status_dm` (`code`, `description`, `default_flg`) VALUES
('in', 'checked in', 'Y'),
('out', 'checked out', 'N'),
('mnd', 'damaged/mending', 'N'),
('dis', 'display area', 'N'),
('hld', 'on hold', 'N'),
('lst', 'lost', 'N'),
('ln', 'on loan', 'N'),
('ord', 'on order', 'N'),
('crt', 'shelving cart', 'N');
