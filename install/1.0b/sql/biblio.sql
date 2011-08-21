-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2011 at 05:35 PM
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
-- Table structure for table `biblio`
--

CREATE TABLE IF NOT EXISTS `biblio` (
  `bibid` int(11) NOT NULL AUTO_INCREMENT,
  `create_dt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_change_dt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_change_userid` int(11) NOT NULL DEFAULT '0',
  `material_cd` smallint(6) NOT NULL DEFAULT '0',
  `collection_cd` smallint(6) NOT NULL DEFAULT '0',
  `opac_flg` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`bibid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1375 ;
