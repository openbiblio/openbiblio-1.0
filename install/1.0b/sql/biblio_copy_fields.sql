SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`biblio_copy_fields` (
  `copyid` int(11) NOT NULL DEFAULT '0',
  `bibid` int(11) NOT NULL DEFAULT '0',
  `code` varchar(16) NOT NULL DEFAULT '',
  `data` text NOT NULL,
  PRIMARY KEY (`copyid`,`bibid`,`code`),
  KEY `code_index` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
