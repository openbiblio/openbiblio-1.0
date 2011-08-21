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
-- Table structure for table `images`
--

CREATE TABLE IF NOT EXISTS `images` (
  `bibid` int(11) NOT NULL,
  `imgurl` text NOT NULL,
  `url` text NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  `caption` text NOT NULL,
  `type` enum('Thumb','Link') NOT NULL,
  PRIMARY KEY (`bibid`,`imgurl`(128))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
