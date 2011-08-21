-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2011 at 05:42 PM
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
-- Table structure for table `calendar_dm`
--

CREATE TABLE IF NOT EXISTS `calendar_dm` (
  `code` int(11) NOT NULL AUTO_INCREMENT,
  `description` char(32) NOT NULL,
  `default_flg` char(1) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `calendar_dm`
--

INSERT INTO `calendar_dm` (`code`, `description`, `default_flg`) VALUES
(1, 'Fourth of July', ''),
(2, 'Normal', ''),
(3, 'Another one', ''),
(4, 'Fred''s Birthbay', '');
