SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`biblio_stock` (
  `bibid` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `vendor` varchar(100) DEFAULT NULL,
  `fund` varchar(100) DEFAULT NULL,
  `price` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`bibid`)
)  DEFAULT CHARSET=utf8;
