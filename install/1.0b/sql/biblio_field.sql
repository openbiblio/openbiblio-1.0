-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2011 at 05:38 PM
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22233 ;
