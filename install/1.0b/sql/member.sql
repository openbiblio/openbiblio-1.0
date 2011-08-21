-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2011 at 05:46 PM
-- Server version: 5.5.8
-- PHP Version: 5.3.5

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=17 ;
