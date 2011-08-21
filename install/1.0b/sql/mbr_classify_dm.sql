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
-- Table structure for table `mbr_classify_dm`
--

CREATE TABLE IF NOT EXISTS `mbr_classify_dm` (
  `code` smallint(6) NOT NULL AUTO_INCREMENT,
  `description` varchar(40) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `default_flg` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `max_fines` decimal(4,2) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `mbr_classify_dm`
--

INSERT INTO `mbr_classify_dm` (`code`, `description`, `default_flg`, `max_fines`) VALUES
(1, 'adult', 'Y', 0.00),
(2, 'juvenile', 'N', 0.00),
(3, 'Denied', 'N', 99.99),
(4, 'unknown', 'N', 15.00);
