-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2011 at 05:53 PM
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
-- Table structure for table `view_fields`
--

CREATE TABLE IF NOT EXISTS `view_fields` (
  `vfid` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(32) NOT NULL,
  `position` tinyint(4) NOT NULL,
  `tag` char(3) NOT NULL,
  `tag_id` tinyint(4) DEFAULT NULL,
  `subfield` char(1) DEFAULT NULL,
  `subfield_id` tinyint(4) DEFAULT NULL,
  `required` char(1) NOT NULL DEFAULT 'N',
  `auto_repeat` enum('No','Tag','Subfield') NOT NULL DEFAULT 'No',
  `label` varchar(128) DEFAULT NULL,
  `form_type` enum('text','textarea') NOT NULL DEFAULT 'text',
  PRIMARY KEY (`vfid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
