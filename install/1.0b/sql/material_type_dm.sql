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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `material_type_dm`
--

INSERT INTO `material_type_dm` (`code`, `description`, `default_flg`, `adult_checkout_limit`, `juvenile_checkout_limit`, `image_file`) VALUES
(6, 'magazines', 'N', 10, 5, 'mag.gif'),
(5, 'equipment', 'N', 10, 5, 'case.gif'),
(4, 'cd computer', 'N', 10, 5, 'cd.gif'),
(3, 'cd audio', 'N', 10, 5, 'cd.gif'),
(2, 'book', 'Y', 10, 5, 'book.gif'),
(1, 'audio tapes', 'N', 10, 5, 'tape.gif'),
(7, 'maps', 'N', 10, 5, 'map.gif'),
(8, 'video/dvd', 'N', 10, 5, 'camera.gif'),
(9, '!state books', 'N', 10, 5, 'book.gif');
