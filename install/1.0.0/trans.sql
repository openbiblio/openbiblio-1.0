/*
SQLyog Enterprise - MySQL GUI v6.07
Host - 5.0.22 : Database - myBiblio
*********************************************************************
Server version : 5.0.22
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `transEntries` */

DROP TABLE IF EXISTS `transEntries`;

CREATE TABLE `transEntries` (
  `transEntryID` int(11) NOT NULL auto_increment,
  `transLocaleCode` varchar(10) NOT NULL,
  `transKeyText` varchar(100) NOT NULL,
  `transEntryText` varchar(255) NOT NULL,
  `transSectionName` varchar(50) NOT NULL,
  PRIMARY KEY  (`transEntryID`),
  UNIQUE KEY `transLocaleCode` (`transLocaleCode`,`transKeyText`,`transSectionName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `transEntries` */

/*Table structure for table `transKeys` */

DROP TABLE IF EXISTS `transKeys`;

CREATE TABLE `transKeys` (
  `transKeyID` int(11) NOT NULL auto_increment,
  `transKeySectionName` varchar(50) NOT NULL,
  `transKeyText` varchar(100) NOT NULL,
  PRIMARY KEY  (`transKeyID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `transKeys` */

/*Table structure for table `transLinkSectionLocale` */

DROP TABLE IF EXISTS `transLinkSectionLocale`;

CREATE TABLE `transLinkSectionLocale` (
  `transLinkID` int(11) NOT NULL auto_increment,
  `transSectionName` varchar(50) default NULL,
  `transLocaleCode` varchar(10) default NULL,
  PRIMARY KEY  (`transLinkID`),
  UNIQUE KEY `transSectionName` (`transSectionName`,`transLocaleCode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `transLinkSectionLocale` */

/*Table structure for table `transLocales` */

DROP TABLE IF EXISTS `transLocales`;

CREATE TABLE `transLocales` (
  `transLocaleID` int(11) NOT NULL auto_increment,
  `transLocaleCode` varchar(10) NOT NULL,
  `transLocaleText` varchar(50) NOT NULL,
  `transLocaleEnglishText` varchar(50) NOT NULL,
  PRIMARY KEY  (`transLocaleID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `transLocales` */

/*Table structure for table `transSections` */

DROP TABLE IF EXISTS `transSections`;

CREATE TABLE `transSections` (
  `transSectionID` int(11) NOT NULL auto_increment,
  `transSectionProjID` int(11) NOT NULL default '1',
  `transSectionName` varchar(50) NOT NULL,
  `transSectionIsProtected` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`transSectionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `transSections` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
