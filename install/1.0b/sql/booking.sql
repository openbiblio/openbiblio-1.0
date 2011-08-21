-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2011 at 05:40 PM
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
-- Table structure for table `booking`
--

CREATE TABLE IF NOT EXISTS `booking` (
  `bookingid` bigint(20) NOT NULL AUTO_INCREMENT,
  `bibid` int(11) NOT NULL,
  `book_dt` date NOT NULL,
  `due_dt` date NOT NULL,
  `out_histid` bigint(20) DEFAULT NULL,
  `out_dt` datetime DEFAULT NULL,
  `ret_histid` bigint(20) DEFAULT NULL,
  `ret_dt` datetime DEFAULT NULL,
  `create_dt` datetime NOT NULL,
  `last_change_dt` datetime NOT NULL,
  `last_change_userid` int(11) NOT NULL,
  PRIMARY KEY (`bookingid`),
  KEY `bibid_idx` (`bibid`),
  KEY `due_dt_idx` (`due_dt`),
  KEY `book_dt_idx` (`book_dt`),
  KEY `out_histid_idx` (`out_histid`),
  KEY `out_dt_idx` (`out_dt`),
  KEY `ret_histid_idx` (`ret_histid`),
  KEY `ret_dt_idx` (`ret_dt`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;
