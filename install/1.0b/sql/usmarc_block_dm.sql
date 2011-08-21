-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2011 at 05:52 PM
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
-- Table structure for table `usmarc_block_dm`
--

CREATE TABLE IF NOT EXISTS `usmarc_block_dm` (
  `block_nmbr` tinyint(4) NOT NULL DEFAULT '0',
  `description` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`block_nmbr`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `usmarc_block_dm`
--

INSERT INTO `usmarc_block_dm` (`block_nmbr`, `description`) VALUES
(0, 'Control information, numbers, and codes'),
(1, 'Main entry'),
(2, 'Titles and title paragraph (title, edition, imprint)'),
(3, 'Physical description, etc.'),
(4, 'Series statements'),
(5, 'Notes'),
(6, 'Subject access fields'),
(7, 'Added entries other than subject or series, linking fields'),
(8, 'Series added entries: location, and alternate graphics'),
(9, 'Reserved for local implementation');
