-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2011 at 05:48 PM
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
-- Table structure for table `report_displays`
--

CREATE TABLE IF NOT EXISTS `report_displays` (
  `page` varchar(32) NOT NULL,
  `position` int(11) NOT NULL,
  `report` text NOT NULL,
  `title` text NOT NULL,
  `max_rows` int(11) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`page`,`position`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
