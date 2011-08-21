-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2011 at 05:45 PM
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
-- Table structure for table `lookup_settings`
--

CREATE TABLE IF NOT EXISTS `lookup_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `protocol` enum('YAZ','SRU') NOT NULL DEFAULT 'YAZ',
  `maxHits` tinyint(4) NOT NULL DEFAULT '25',
  `timeout` int(10) unsigned NOT NULL DEFAULT '20',
  `keepDashes` enum('y','n') NOT NULL DEFAULT 'n',
  `callNmbrType` enum('LoC','Dew','UDC','local') NOT NULL DEFAULT 'Dew',
  `autoDewey` enum('y','n') NOT NULL DEFAULT 'y',
  `defaultDewey` varchar(10) NOT NULL DEFAULT '813.52',
  `autoCutter` enum('y','n') NOT NULL DEFAULT 'y',
  `cutterType` enum('LoC','CS3') NOT NULL DEFAULT 'CS3',
  `cutterWord` tinyint(4) NOT NULL DEFAULT '1',
  `noiseWords` varchar(255) NOT NULL DEFAULT 'a an and for of the this those',
  `autoCollect` enum('y','n') NOT NULL DEFAULT 'y',
  `fictionName` varchar(10) NOT NULL DEFAULT 'Fiction',
  `fictionCode` tinyint(4) NOT NULL DEFAULT '1',
  `fictionLoc` varchar(255) NOT NULL DEFAULT 'PQ PR PS PT PU PV PW PX PY PZ',
  `fictionDew` varchar(255) NOT NULL DEFAULT '813 823',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `lookup_settings`
--

INSERT INTO `lookup_settings` (`id`, `protocol`, `maxHits`, `timeout`, `keepDashes`, `callNmbrType`, `autoDewey`, `defaultDewey`, `autoCutter`, `cutterType`, `cutterWord`, `noiseWords`, `autoCollect`, `fictionName`, `fictionCode`, `fictionLoc`, `fictionDew`) VALUES
(1, 'SRU', 25, 10, 'n', 'LoC', 'n', '813.52', 'y', 'LoC', 1, 'a an and for of the this those', 'y', 'Fiction', 1, 'PQ PR PS PT PU PV PW PX PY PZ', '813 823');
