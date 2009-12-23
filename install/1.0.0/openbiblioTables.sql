-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 18, 2009 at 02:56 PM
-- Server version: 5.1.37
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `openbibliowork`
--

-- --------------------------------------------------------

--
-- Table structure for table `biblio`
--

CREATE TABLE IF NOT EXISTS `biblio` (
  `bibid` int(11) NOT NULL AUTO_INCREMENT,
  `create_dt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_change_dt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_change_userid` int(11) NOT NULL DEFAULT '0',
  `material_cd` smallint(6) NOT NULL DEFAULT '0',
  `collection_cd` smallint(6) NOT NULL DEFAULT '0',
  `opac_flg` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`bibid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1285 ;

-- --------------------------------------------------------

--
-- Table structure for table `biblio_copy`
--

CREATE TABLE IF NOT EXISTS `biblio_copy` (
  `bibid` int(11) NOT NULL DEFAULT '0',
  `copyid` int(11) NOT NULL AUTO_INCREMENT,
  `create_dt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_change_dt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_change_userid` int(11) NOT NULL DEFAULT '0',
  `barcode_nmbr` varchar(20) NOT NULL DEFAULT '',
  `copy_desc` varchar(160) DEFAULT NULL,
  `vendor` varchar(100) DEFAULT NULL,
  `fund` varchar(100) DEFAULT NULL,
  `price` varchar(10) DEFAULT NULL,
  `expiration` varchar(100) DEFAULT NULL,
  `histid` int(11) DEFAULT NULL,
  `siteid` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`copyid`),
  UNIQUE KEY `histid_idx` (`histid`),
  KEY `bibid_idx` (`bibid`),
  KEY `barcode_index` (`barcode_nmbr`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9621 ;

-- --------------------------------------------------------

--
-- Table structure for table `biblio_copy_fields`
--

CREATE TABLE IF NOT EXISTS `biblio_copy_fields` (
  `copyid` int(11) NOT NULL DEFAULT '0',
  `bibid` int(11) NOT NULL DEFAULT '0',
  `code` varchar(16) NOT NULL DEFAULT '',
  `data` text NOT NULL,
  PRIMARY KEY (`copyid`,`bibid`,`code`),
  KEY `code_index` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `biblio_copy_fields_dm`
--

CREATE TABLE IF NOT EXISTS `biblio_copy_fields_dm` (
  `code` varchar(16) NOT NULL DEFAULT '',
  `description` varchar(32) NOT NULL DEFAULT '',
  `default_flg` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `biblio_field`
--

CREATE TABLE IF NOT EXISTS `biblio_field` (
  `bibid` int(11) NOT NULL,
  `fieldid` int(11) NOT NULL AUTO_INCREMENT,
  `seq` int(11) NOT NULL,
  `tag` char(3) NOT NULL,
  `ind1_cd` char(1) DEFAULT NULL,
  `ind2_cd` char(1) DEFAULT NULL,
  `field_data` text,
  `display` text,
  PRIMARY KEY (`fieldid`),
  KEY `bibid_idx` (`bibid`),
  KEY `tag_idx` (`tag`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21019 ;

-- --------------------------------------------------------

--
-- Table structure for table `biblio_hold`
--

CREATE TABLE IF NOT EXISTS `biblio_hold` (
  `bibid` int(11) NOT NULL,
  `copyid` int(11) NOT NULL,
  `holdid` int(11) NOT NULL AUTO_INCREMENT,
  `hold_begin_dt` datetime NOT NULL,
  `mbrid` int(11) NOT NULL,
  PRIMARY KEY (`holdid`),
  KEY `mbr_index` (`mbrid`),
  KEY `bibid_index` (`bibid`),
  KEY `copyid_index` (`copyid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `biblio_status_dm`
--

CREATE TABLE IF NOT EXISTS `biblio_status_dm` (
  `code` char(3) NOT NULL,
  `description` varchar(40) NOT NULL,
  `default_flg` char(1) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `biblio_status_hist`
--

CREATE TABLE IF NOT EXISTS `biblio_status_hist` (
  `histid` bigint(20) NOT NULL AUTO_INCREMENT,
  `bibid` int(11) NOT NULL,
  `copyid` int(11) NOT NULL,
  `status_cd` char(3) NOT NULL,
  `status_begin_dt` datetime NOT NULL,
  PRIMARY KEY (`histid`),
  KEY `copy_index` (`bibid`,`copyid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=81 ;

-- --------------------------------------------------------

--
-- Table structure for table `biblio_stock`
--

CREATE TABLE IF NOT EXISTS `biblio_stock` (
  `bibid` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `vendor` varchar(100) DEFAULT NULL,
  `fund` varchar(100) DEFAULT NULL,
  `price` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`bibid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `biblio_subfield`
--

CREATE TABLE IF NOT EXISTS `biblio_subfield` (
  `bibid` int(11) NOT NULL,
  `fieldid` int(11) NOT NULL,
  `subfieldid` int(11) NOT NULL AUTO_INCREMENT,
  `seq` int(11) NOT NULL,
  `subfield_cd` char(1) NOT NULL,
  `subfield_data` text NOT NULL,
  PRIMARY KEY (`subfieldid`),
  KEY `bibid_idx` (`bibid`),
  KEY `fieldid_idx` (`fieldid`),
  KEY `subfield_cd_idx` (`subfield_cd`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20861 ;

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE IF NOT EXISTS `booking` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `booking_member`
--

CREATE TABLE IF NOT EXISTS `booking_member` (
  `bookingid` bigint(20) NOT NULL,
  `mbrid` int(11) NOT NULL,
  PRIMARY KEY (`bookingid`,`mbrid`),
  KEY `mbrid_idx` (`mbrid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `calendar`
--

CREATE TABLE IF NOT EXISTS `calendar` (
  `calendar` int(11) NOT NULL,
  `date` date NOT NULL,
  `open` enum('Yes','No','Unset') NOT NULL,
  PRIMARY KEY (`calendar`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `calendar_dm`
--

CREATE TABLE IF NOT EXISTS `calendar_dm` (
  `code` int(11) NOT NULL AUTO_INCREMENT,
  `description` char(32) NOT NULL,
  `default_flg` char(1) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE IF NOT EXISTS `cart` (
  `sess_id` text NOT NULL,
  `name` char(16) NOT NULL,
  `id` int(11) NOT NULL,
  PRIMARY KEY (`sess_id`(64),`name`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `collection_circ`
--

CREATE TABLE IF NOT EXISTS `collection_circ` (
  `code` smallint(6) NOT NULL AUTO_INCREMENT,
  `days_due_back` tinyint(3) unsigned NOT NULL,
  `daily_late_fee` decimal(4,2) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `collection_dist`
--

CREATE TABLE IF NOT EXISTS `collection_dist` (
  `code` smallint(6) NOT NULL AUTO_INCREMENT,
  `restock_threshold` int(10) unsigned NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Table structure for table `collection_dm`
--

CREATE TABLE IF NOT EXISTS `collection_dm` (
  `code` smallint(6) NOT NULL AUTO_INCREMENT,
  `description` varchar(40) NOT NULL,
  `default_flg` char(1) NOT NULL,
  `type` enum('Circulated','Distributed') NOT NULL DEFAULT 'Circulated',
  PRIMARY KEY (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Table structure for table `cutter`
--

CREATE TABLE IF NOT EXISTS `cutter` (
  `theName` varchar(32) NOT NULL DEFAULT '',
  `theNmbr` mediumint(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`theName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE IF NOT EXISTS `images` (
  `bibid` int(11) NOT NULL,
  `imgurl` text NOT NULL,
  `url` text NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  `caption` text NOT NULL,
  `type` enum('Thumb','Link') NOT NULL,
  PRIMARY KEY (`bibid`,`imgurl`(128))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lookup_hosts`
--

CREATE TABLE IF NOT EXISTS `lookup_hosts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `seq` tinyint(4) NOT NULL,
  `active` enum('y','n') NOT NULL DEFAULT 'n',
  `host` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `db` varchar(20) NOT NULL,
  `user` varchar(20) DEFAULT NULL,
  `pw` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `lookup_settings`
--

CREATE TABLE IF NOT EXISTS `lookup_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `protocol` enum('YAZ','SRU') NOT NULL DEFAULT 'YAZ',
  `maxHits` tinyint(4) NOT NULL DEFAULT '25',
  `timeout` int(10) unsigned NOT NULL DEFAULT '20',
  `keepDashes` enum('y','n') NOT NULL DEFAULT 'n',
  `callNmbrType` enum('LoC','Dew','UDC','local') NOT NULL DEFAULT 'Dew',
  `autoDewey` enum('y','n') NOT NULL DEFAULT 'y',
  `defaultDewey` varchar(10) NOT NULL DEFAULT '813.52',
  `autoCutter` enum('y','n') NOT NULL DEFAULT 'y',
  `cutterType` enum('LoC','CS3') NOT NULL DEFAULT 'CS3',
  `cutterWord` tinyint(4) NOT NULL DEFAULT '1',
  `noiseWords` varchar(255) NOT NULL DEFAULT 'a an and for of the this those',
  `autoCollect` enum('y','n') NOT NULL DEFAULT 'y',
  `fictionName` varchar(10) NOT NULL DEFAULT 'Fiction',
  `fictionCode` tinyint(4) NOT NULL DEFAULT '1',
  `fictionLoc` varchar(255) NOT NULL DEFAULT 'PQ PR PS PT PU PV PW PX PY PZ',
  `fictionDew` varchar(255) NOT NULL DEFAULT '813 823',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `material_fields`
--

CREATE TABLE IF NOT EXISTS `material_fields` (
  `material_field_id` int(4) NOT NULL AUTO_INCREMENT,
  `material_cd` int(11) DEFAULT NULL,
  `tag` char(3) NOT NULL,
  `subfield_cd` varchar(10) DEFAULT NULL,
  `position` tinyint(4) NOT NULL,
  `label` varchar(128) DEFAULT NULL,
  `form_type` enum('text','textarea') NOT NULL DEFAULT 'text',
  `required` tinyint(1) NOT NULL,
  `repeatable` tinyint(1) DEFAULT NULL,
  `search_results` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`material_field_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=63 ;

-- --------------------------------------------------------

--
-- Table structure for table `material_type_dm`
--

CREATE TABLE IF NOT EXISTS `material_type_dm` (
  `code` smallint(6) NOT NULL AUTO_INCREMENT,
  `description` varchar(40) NOT NULL,
  `default_flg` char(1) NOT NULL,
  `adult_checkout_limit` tinyint(3) unsigned NOT NULL,
  `juvenile_checkout_limit` tinyint(3) unsigned NOT NULL,
  `image_file` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `mbr_classify_dm`
--

CREATE TABLE IF NOT EXISTS `mbr_classify_dm` (
  `code` smallint(6) NOT NULL AUTO_INCREMENT,
  `description` varchar(40) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `default_flg` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `max_fines` decimal(4,2) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE IF NOT EXISTS `member` (
  `mbrid` int(11) NOT NULL AUTO_INCREMENT,
  `barcode_nmbr` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `create_dt` datetime NOT NULL,
  `last_change_dt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `last_change_userid` int(11) NOT NULL DEFAULT '0',
  `last_name` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `first_name` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `address1` varchar(32) COLLATE latin1_general_ci DEFAULT NULL,
  `address2` varchar(32) COLLATE latin1_general_ci DEFAULT NULL,
  `city` varchar(32) COLLATE latin1_general_ci DEFAULT NULL,
  `state` varchar(32) COLLATE latin1_general_ci DEFAULT NULL,
  `zip` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `zip_ext` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `home_phone` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  `work_phone` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  `email` varchar(128) COLLATE latin1_general_ci DEFAULT NULL,
  `classification` smallint(6) NOT NULL,
  `siteid` tinyint(3) unsigned NOT NULL,
  `school_grade` tinyint(3) unsigned NOT NULL,
  `school_teacher` varchar(32) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`mbrid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `member_account`
--

CREATE TABLE IF NOT EXISTS `member_account` (
  `mbrid` int(11) NOT NULL DEFAULT '0',
  `transid` int(11) NOT NULL AUTO_INCREMENT,
  `create_dt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `create_userid` int(11) NOT NULL DEFAULT '0',
  `transaction_type_cd` char(2) NOT NULL DEFAULT '',
  `amount` decimal(8,2) NOT NULL DEFAULT '0.00',
  `description` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`mbrid`,`transid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `member_fields`
--

CREATE TABLE IF NOT EXISTS `member_fields` (
  `mbrid` int(11) NOT NULL,
  `code` varchar(16) COLLATE latin1_general_ci NOT NULL,
  `data` text COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`mbrid`,`code`),
  KEY `code_index` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `member_fields_dm`
--

CREATE TABLE IF NOT EXISTS `member_fields_dm` (
  `code` varchar(16) COLLATE latin1_general_ci NOT NULL,
  `description` char(32) COLLATE latin1_general_ci NOT NULL,
  `default_flg` char(1) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `php_sess`
--

CREATE TABLE IF NOT EXISTS `php_sess` (
  `id` varchar(128) NOT NULL DEFAULT '',
  `last_access_dt` datetime DEFAULT NULL,
  `data` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `report_displays`
--

CREATE TABLE IF NOT EXISTS `report_displays` (
  `page` varchar(32) NOT NULL,
  `position` int(11) NOT NULL,
  `report` text NOT NULL,
  `title` text NOT NULL,
  `max_rows` int(11) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`page`,`position`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE IF NOT EXISTS `session` (
  `userid` int(5) NOT NULL,
  `last_updated_dt` datetime NOT NULL,
  `token` int(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `name` varchar(64) NOT NULL DEFAULT '',
  `position` int(11) DEFAULT NULL,
  `title` text,
  `type` enum('text','int','bool','select') NOT NULL DEFAULT 'text',
  `width` int(11) DEFAULT NULL,
  `type_data` text,
  `validator` text,
  `value` text,
  `menu` enum('admin','tools','none') NOT NULL DEFAULT 'admin',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `site`
--

CREATE TABLE IF NOT EXISTS `site` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE IF NOT EXISTS `staff` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `create_dt` datetime NOT NULL,
  `last_change_dt` datetime NOT NULL,
  `last_change_userid` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `pwd` char(32) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `first_name` varchar(30) DEFAULT NULL,
  `suspended_flg` char(1) NOT NULL,
  `admin_flg` char(1) NOT NULL,
  `circ_flg` char(1) NOT NULL,
  `circ_mbr_flg` char(1) NOT NULL,
  `catalog_flg` char(1) NOT NULL,
  `reports_flg` char(1) NOT NULL,
  `tools_flg` char(1) NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `state_dm`
--

CREATE TABLE IF NOT EXISTS `state_dm` (
  `code` char(2) NOT NULL,
  `description` varchar(20) NOT NULL,
  `default_flg` char(1) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `theme`
--

CREATE TABLE IF NOT EXISTS `theme` (
  `themeid` smallint(6) NOT NULL AUTO_INCREMENT,
  `theme_name` varchar(40) NOT NULL,
  `title_bg` varchar(20) NOT NULL,
  `title_font_face` varchar(128) NOT NULL,
  `title_font_size` tinyint(4) NOT NULL,
  `title_font_bold` char(1) NOT NULL,
  `title_font_color` varchar(20) NOT NULL,
  `title_align` varchar(30) NOT NULL,
  `primary_bg` varchar(20) NOT NULL,
  `primary_font_face` varchar(128) NOT NULL,
  `primary_font_size` tinyint(4) NOT NULL,
  `primary_font_color` varchar(20) NOT NULL,
  `primary_link_color` varchar(20) NOT NULL,
  `primary_error_color` varchar(20) NOT NULL,
  `alt1_bg` varchar(20) NOT NULL,
  `alt1_font_face` varchar(128) NOT NULL,
  `alt1_font_size` tinyint(4) NOT NULL,
  `alt1_font_color` varchar(20) NOT NULL,
  `alt1_link_color` varchar(20) NOT NULL,
  `alt2_bg` varchar(20) NOT NULL,
  `alt2_font_face` varchar(128) NOT NULL,
  `alt2_font_size` tinyint(4) NOT NULL,
  `alt2_font_color` varchar(20) NOT NULL,
  `alt2_link_color` varchar(20) NOT NULL,
  `alt2_font_bold` char(1) NOT NULL,
  `border_color` varchar(20) NOT NULL,
  `border_width` tinyint(4) NOT NULL,
  `table_padding` tinyint(4) NOT NULL,
  PRIMARY KEY (`themeid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_type_dm`
--

CREATE TABLE IF NOT EXISTS `transaction_type_dm` (
  `code` char(2) NOT NULL,
  `description` varchar(40) NOT NULL,
  `default_flg` char(1) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transentries`
--

CREATE TABLE IF NOT EXISTS `transentries` (
  `transEntryID` int(11) NOT NULL AUTO_INCREMENT,
  `transLocaleCode` varchar(10) NOT NULL,
  `transKeyText` varchar(100) NOT NULL,
  `transEntryText` varchar(255) NOT NULL,
  `transSectionName` varchar(50) NOT NULL,
  PRIMARY KEY (`transEntryID`),
  UNIQUE KEY `transLocaleCode` (`transLocaleCode`,`transKeyText`,`transSectionName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `transkeys`
--

CREATE TABLE IF NOT EXISTS `transkeys` (
  `transKeyID` int(11) NOT NULL AUTO_INCREMENT,
  `transKeySectionName` varchar(50) NOT NULL,
  `transKeyText` varchar(100) NOT NULL,
  PRIMARY KEY (`transKeyID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `translinksectionlocale`
--

CREATE TABLE IF NOT EXISTS `translinksectionlocale` (
  `transLinkID` int(11) NOT NULL AUTO_INCREMENT,
  `transSectionName` varchar(50) DEFAULT NULL,
  `transLocaleCode` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`transLinkID`),
  UNIQUE KEY `transSectionName` (`transSectionName`,`transLocaleCode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `translocales`
--

CREATE TABLE IF NOT EXISTS `translocales` (
  `transLocaleID` int(11) NOT NULL AUTO_INCREMENT,
  `transLocaleCode` varchar(10) NOT NULL,
  `transLocaleText` varchar(50) NOT NULL,
  `transLocaleEnglishText` varchar(50) NOT NULL,
  PRIMARY KEY (`transLocaleID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `transsections`
--

CREATE TABLE IF NOT EXISTS `transsections` (
  `transSectionID` int(11) NOT NULL AUTO_INCREMENT,
  `transSectionProjID` int(11) NOT NULL DEFAULT '1',
  `transSectionName` varchar(50) NOT NULL,
  `transSectionIsProtected` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`transSectionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `usmarc_block_dm`
--

CREATE TABLE IF NOT EXISTS `usmarc_block_dm` (
  `block_nmbr` tinyint(4) NOT NULL DEFAULT '0',
  `description` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`block_nmbr`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `usmarc_indicator_dm`
--

CREATE TABLE IF NOT EXISTS `usmarc_indicator_dm` (
  `tag` smallint(6) NOT NULL DEFAULT '0',
  `indicator_nmbr` tinyint(4) NOT NULL DEFAULT '0',
  `indicator_cd` char(1) NOT NULL DEFAULT '',
  `description` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`tag`,`indicator_nmbr`,`indicator_cd`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `usmarc_subfield_dm`
--

CREATE TABLE IF NOT EXISTS `usmarc_subfield_dm` (
  `tag` smallint(6) NOT NULL DEFAULT '0',
  `subfield_cd` char(1) NOT NULL DEFAULT '',
  `description` varchar(80) NOT NULL DEFAULT '',
  `repeatable_flg` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`tag`,`subfield_cd`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `usmarc_tag_dm`
--

CREATE TABLE IF NOT EXISTS `usmarc_tag_dm` (
  `block_nmbr` tinyint(4) NOT NULL DEFAULT '0',
  `tag` smallint(6) NOT NULL DEFAULT '0',
  `description` varchar(80) NOT NULL DEFAULT '',
  `ind1_description` varchar(80) NOT NULL DEFAULT '',
  `ind2_description` varchar(80) NOT NULL DEFAULT '',
  `repeatable_flg` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`block_nmbr`,`tag`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `view_fields`
--

CREATE TABLE IF NOT EXISTS `view_fields` (
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
