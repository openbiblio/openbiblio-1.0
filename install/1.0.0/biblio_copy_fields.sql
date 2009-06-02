/*
SQLyog Enterprise - MySQL GUI v6.07
Host - 5.0.22 : Database - myBiblio
*********************************************************************
Server version : 5.0.22
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `biblio_copy_fields` */

DROP TABLE IF EXISTS `biblio_copy_fields`;

CREATE TABLE `biblio_copy_fields` (
  `copyid` int(11) NOT NULL default '0',
  `bibid` int(11) NOT NULL default '0',
  `code` varchar(16) NOT NULL default '',
  `data` text NOT NULL,
  PRIMARY KEY  (`copyid`,`bibid`,`code`),
  KEY `code_index` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `biblio_copy_fields_dm` */

DROP TABLE IF EXISTS `biblio_copy_fields_dm`;

CREATE TABLE `biblio_copy_fields_dm` (
  `code` varchar(16) NOT NULL default '',
  `description` varchar(32) NOT NULL default '',
  `default_flg` char(1) NOT NULL default '',
  PRIMARY KEY  (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
