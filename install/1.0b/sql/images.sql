SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`images` (
  `bibid` int(11) NOT NULL,
  `imgurl` text NOT NULL,
  `url` text NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  `caption` text NOT NULL,
  `type` enum('Thumb','Link') NOT NULL,
  PRIMARY KEY (`bibid`,`imgurl`(128))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
