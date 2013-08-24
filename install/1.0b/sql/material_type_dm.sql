SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`material_type_dm` (
  `code` smallint(6) NOT NULL AUTO_INCREMENT,
  `description` varchar(40) NOT NULL,
  `default_flg` char(1) NOT NULL,
  `adult_checkout_limit` tinyint(3) unsigned NOT NULL,
  `juvenile_checkout_limit` tinyint(3) unsigned NOT NULL,
  `image_file` varchar(128) DEFAULT NULL,
	`srch_disp_lines` tinyint(3) unsigned NOT NULL DEFAULT '4',
  PRIMARY KEY (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;
INSERT INTO %prfx%.`material_type_dm` (`code`, `description`, `default_flg`, `adult_checkout_limit`, `juvenile_checkout_limit`, `image_file`) VALUES
(6, 'magazines', 'N', 10, 5, 'mag.gif'),
(5, 'equipment', 'N', 10, 5, 'case.gif'),
(4, 'cd computer', 'N', 10, 5, 'cd.gif'),
(3, 'cd audio', 'N', 10, 5, 'cd.gif'),
(2, 'book', 'Y', 10, 5, 'book.gif'),
(1, 'audio tapes', 'N', 10, 5, 'tape.gif'),
(7, 'maps', 'N', 10, 5, 'map.gif'),
(8, 'video/dvd', 'N', 10, 5, 'camera.gif');
