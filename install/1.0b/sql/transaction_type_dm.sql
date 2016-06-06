SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`transaction_type_dm` (
  `code` char(2) NOT NULL,
  `description` varchar(40) NOT NULL,
  `default_flg` char(1) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `transaction_type_dm` (`code`, `description`, `default_flg`) VALUES
('-p', 'payment', 'Y'),
('-r', 'credit', 'N'),
('+c', 'charge', 'N');
