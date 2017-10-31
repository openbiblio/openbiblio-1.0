SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`site` (
  `siteid` int(11) NOT NULL AUTO_INCREMENT,
  `calendar` int(11) NOT NULL,
  `name` text NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  `address1` varchar(128) DEFAULT NULL,
  `address2` varchar(128) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` char(2) DEFAULT NULL,
  `zip` varchar(15) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `fax` varchar(15) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `delivery_note` text NOT NULL,
  PRIMARY KEY (`siteid`)
)   DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;
INSERT INTO %prfx%.`site` (`siteid`, `calendar`, `name`, `code`, `address1`, `address2`, `city`, `state`, `zip`, `phone`, `fax`, `email`, `delivery_note`) VALUES
(1, 0, 'Home', 'home', '344 Bacon Rd', '', 'Mercer', 'ME', '04957', '207-587-2623', '', 'flaplante@flos-inc.com', 'Leave under cover'),
(2, 2, 'LaPlante Library', 'lib', '344 Bacon Rd', '', 'Mercer', 'ME', '04957', '207-587-2623', '', 'library@flos-inc.com', 'Leave at front door');
