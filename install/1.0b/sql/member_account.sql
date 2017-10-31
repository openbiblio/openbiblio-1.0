SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`member_account` (
  `mbrid` int(11) NOT NULL DEFAULT '0',
  `transid` int(11) NOT NULL,
  `create_dt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `create_userid` int(11) NOT NULL DEFAULT '0',
  `transaction_type_cd` char(2) NOT NULL DEFAULT '',
  `amount` decimal(8,2) NOT NULL DEFAULT '0.00',
  `description` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`mbrid`,`transid`)
)  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


