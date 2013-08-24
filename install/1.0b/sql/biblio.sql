SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`biblio` (
  `bibid` int(11) NOT NULL AUTO_INCREMENT,
  `create_dt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_change_dt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_change_userid` int(11) NOT NULL DEFAULT '0',
  `material_cd` smallint(6) NOT NULL DEFAULT '0',
  `collection_cd` smallint(6) NOT NULL DEFAULT '0',
  `opac_flg` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`bibid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1375 ;
