SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`cart` (
  `sess_id` text NOT NULL,
  `name` char(16) NOT NULL,
  `id` int(11) NOT NULL,
  PRIMARY KEY (`sess_id`(64),`name`,`id`)
)  DEFAULT CHARSET=utf8;
