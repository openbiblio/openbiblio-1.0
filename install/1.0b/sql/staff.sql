-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2011 at 07:45 PM
-- Server version: 5.5.8
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `openbibliodbtrial`
--

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE IF NOT EXISTS `staff` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `create_dt` datetime NOT NULL,
  `last_change_dt` datetime NOT NULL,
  `last_change_userid` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `pwd` char(32) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `first_name` varchar(30) DEFAULT NULL,
  `suspended_flg` char(1) NOT NULL,
  `admin_flg` char(1) NOT NULL,
  `circ_flg` char(1) NOT NULL,
  `circ_mbr_flg` char(1) NOT NULL,
  `catalog_flg` char(1) NOT NULL,
  `reports_flg` char(1) NOT NULL,
  `tools_flg` char(1) NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`userid`, `create_dt`, `last_change_dt`, `last_change_userid`, `username`, `pwd`, `last_name`, `first_name`, `suspended_flg`, `admin_flg`, `circ_flg`, `circ_mbr_flg`, `catalog_flg`, `reports_flg`, `tools_flg`) VALUES
(2, '0000-00-00 00:00:00', '2010-12-18 16:42:03', 2, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Administrator', '', 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y');
