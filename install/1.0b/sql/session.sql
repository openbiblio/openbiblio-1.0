SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`session` (
  `userid` int(5) NOT NULL,
  `last_updated_dt` datetime NOT NULL,
  `token` int(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
