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
-- Table structure for table `booking_member`
--

CREATE TABLE IF NOT EXISTS `booking_member` (
  `bookingid` bigint(20) NOT NULL,
  `mbrid` int(11) NOT NULL,
  PRIMARY KEY (`bookingid`,`mbrid`),
  KEY `mbrid_idx` (`mbrid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
