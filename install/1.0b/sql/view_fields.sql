SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`view_fields` (
  `vfid` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(32) NOT NULL,
  `position` tinyint(4) NOT NULL,
  `tag` char(3) NOT NULL,
  `tag_id` tinyint(4) DEFAULT NULL,
  `subfield` char(1) DEFAULT NULL,
  `subfield_id` tinyint(4) DEFAULT NULL,
  `required` char(1) NOT NULL DEFAULT 'N',
  `auto_repeat` enum('No','Tag','Subfield') NOT NULL DEFAULT 'No',
  `label` varchar(128) DEFAULT NULL,
  `form_type` enum('text','textarea') NOT NULL DEFAULT 'text',
  PRIMARY KEY (`vfid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
