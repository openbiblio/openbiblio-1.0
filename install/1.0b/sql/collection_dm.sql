-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2011 at 05:44 PM
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
-- Table structure for table `collection_dm`
--

CREATE TABLE IF NOT EXISTS `collection_dm` (
  `code` smallint(6) NOT NULL AUTO_INCREMENT,
  `description` varchar(40) NOT NULL,
  `default_flg` char(1) NOT NULL,
  `type` enum('Circulated','Distributed') NOT NULL DEFAULT 'Circulated',
  PRIMARY KEY (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=28 ;

--
-- Dumping data for table `collection_dm`
--

INSERT INTO `collection_dm` (`code`, `description`, `default_flg`, `type`) VALUES
(1, 'Fiction', 'N', 'Circulated'),
(2, 'Nonfiction', 'Y', 'Circulated'),
(3, 'Cassettes', 'N', 'Circulated'),
(4, 'Compact Discs', 'N', 'Circulated'),
(5, 'Computer Software', 'N', 'Circulated'),
(6, 'Science Fiction', 'N', 'Circulated'),
(10, 'Magazines', 'N', 'Distributed'),
(11, 'Reference', 'N', 'Circulated'),
(12, 'Videos and DVDs', 'N', 'Circulated'),
(13, 'Cook Books', 'N', 'Circulated'),
(14, 'Wood Shop', 'N', 'Circulated'),
(15, 'Craft Shop', 'N', 'Circulated'),
(18, 'Automotive', 'N', 'Circulated');
