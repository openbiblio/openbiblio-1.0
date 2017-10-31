SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`usmarc_block_dm` (
  `block_nmbr` tinyint(4) NOT NULL DEFAULT '0',
  `description` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`block_nmbr`)
)  DEFAULT CHARSET=utf8;
INSERT INTO %prfx%.`usmarc_block_dm` (`block_nmbr`, `description`) VALUES
(0, 'Control information, numbers, and codes'),
(1, 'Main entry'),
(2, 'Titles and title paragraph (title, edition, imprint)'),
(3, 'Physical description, etc.'),
(4, 'Series statements'),
(5, 'Notes'),
(6, 'Subject access fields'),
(7, 'Added entries other than subject or series, linking fields'),
(8, 'Series added entries: location, and alternate graphics'),
(9, 'Reserved for local implementation');
