SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`report_displays` (
  `page` varchar(32) NOT NULL,
  `position` int(11) NOT NULL,
  `report` text NOT NULL,
  `title` text NOT NULL,
  `max_rows` int(11) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`page`,`position`)
)  DEFAULT CHARSET=utf8;
