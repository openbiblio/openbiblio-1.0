-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2011 at 05:43 PM
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
-- Table structure for table `collection_circ`
--

CREATE TABLE IF NOT EXISTS `collection_circ` (
  `code` smallint(6) NOT NULL AUTO_INCREMENT,
  `days_due_back` tinyint(3) unsigned NOT NULL,
  `daily_late_fee` decimal(4,2) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

--
-- Dumping data for table `collection_circ`
--

INSERT INTO `collection_circ` (`code`, `days_due_back`, `daily_late_fee`) VALUES
(1, 30, 0.01),
(2, 14, 0.10),
(3, 7, 0.25),
(4, 7, 0.10),
(5, 7, 0.10),
(11, 7, 0.10),
(12, 7, 0.10),
(13, 7, 0.10),
(14, 7, 0.10),
(15, 7, 0.10),
(6, 7, 0.10),
(18, 7, 0.00);
