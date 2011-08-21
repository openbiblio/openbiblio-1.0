-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2011 at 05:36 PM
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=73 ;
