SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`state_dm` (
  `code` varchar(20) NOT NULL,
  `description` varchar(32) NOT NULL,
  `default_flg` char(1) NOT NULL,
  PRIMARY KEY (`code`)
)  DEFAULT CHARSET=utf8;
INSERT INTO `state_dm` (`code`, `description`, `default_flg`) VALUES
('ME', 'Maine', 'Y'),
('CA', 'California', 'N');
