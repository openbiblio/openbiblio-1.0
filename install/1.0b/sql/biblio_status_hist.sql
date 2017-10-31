SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`biblio_status_hist` (
  `histid` bigint(20) NOT NULL AUTO_INCREMENT,
  `bibid` int(11) NOT NULL,
  `copyid` int(11) NOT NULL,
  `status_cd` char(3) NOT NULL,
  `status_begin_dt` datetime NOT NULL,
  PRIMARY KEY (`histid`),
  KEY `copy_index` (`bibid`,`copyid`)
)   DEFAULT CHARSET=utf8 AUTO_INCREMENT=178 ;
