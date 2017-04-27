SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`booking` (
  `bookingid` bigint(20) NOT NULL AUTO_INCREMENT,
  `bibid` int(11) NOT NULL,
  `book_dt` date NOT NULL,
  `due_dt` date NOT NULL,
  `out_histid` bigint(20) DEFAULT NULL,
  `out_dt` datetime DEFAULT NULL,
  `ret_histid` bigint(20) DEFAULT NULL,
  `ret_dt` datetime DEFAULT NULL,
  `create_dt` datetime NOT NULL,
  `last_change_dt` datetime NOT NULL,
  `last_change_userid` int(11) NOT NULL,
  PRIMARY KEY (`bookingid`),
  KEY `bibid_idx` (`bibid`),
  KEY `due_dt_idx` (`due_dt`),
  KEY `book_dt_idx` (`book_dt`),
  KEY `out_histid_idx` (`out_histid`),
  KEY `out_dt_idx` (`out_dt`),
  KEY `ret_histid_idx` (`ret_histid`),
  KEY `ret_dt_idx` (`ret_dt`)
)   DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;
