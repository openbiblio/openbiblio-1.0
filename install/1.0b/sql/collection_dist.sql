SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`collection_dist` (
  `code` smallint(6) NOT NULL AUTO_INCREMENT,
  `restock_threshold` int(10) unsigned NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;
INSERT INTO %prfx%.`collection_dist` (`code`, `restock_threshold`) VALUES
(1, 1),
(2, 1),
(3, 1),
(10, 2);
