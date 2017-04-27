SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`biblio_copy_fields_dm` (
  `code` varchar(16) NOT NULL,
  `description` varchar(32) NOT NULL DEFAULT '',
  `default_flg` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`code`)
)  DEFAULT CHARSET=utf8;
INSERT INTO %prfx%.`biblio_copy_fields_dm` (`code`, `description`, `default_flg`) VALUES
('pr', 'Price', 'N'),
('src', 'Source', 'N'),
('cvr', 'Cover Type', 'N');
