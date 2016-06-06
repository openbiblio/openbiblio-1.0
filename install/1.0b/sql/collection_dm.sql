SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`collection_dm` (
  `code` smallint(6) NOT NULL AUTO_INCREMENT,
  `description` varchar(40) NOT NULL,
  `default_flg` char(1) NOT NULL,
  `type` enum('Circulated','Distributed') NOT NULL DEFAULT 'Circulated',
  PRIMARY KEY (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;
INSERT INTO %prfx%.`collection_dm` (`code`, `description`, `default_flg`, `type`) VALUES
(1, 'Fiction', 'N', 'Circulated'),
(2, 'Nonfiction', 'Y', 'Circulated'),
(3, 'Cassettes', 'N', 'Circulated'),
(4, 'Compact Discs', 'N', 'Circulated'),
(5, 'Computer Software', 'N', 'Circulated'),
(6, 'Science Fiction', 'N', 'Circulated'),
(10, 'Magazines', 'N', 'Distributed'),
(11, 'Reference', 'N', 'Circulated'),
(12, 'Videos and DVDs', 'N', 'Circulated'),
(13, 'Cook Books', 'N', 'Circulated'),
(14, 'Wood Shop', 'N', 'Circulated'),
(15, 'Craft Shop', 'N', 'Circulated'),
(18, 'Automotive', 'N', 'Circulated');
