-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2011 at 05:40 PM
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22417 ;
