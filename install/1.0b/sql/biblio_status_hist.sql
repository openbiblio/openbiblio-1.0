-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2011 at 05:39 PM
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=178 ;
