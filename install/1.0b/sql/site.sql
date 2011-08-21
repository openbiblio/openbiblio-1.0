-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2011 at 05:49 PM
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
-- Table structure for table `site`
--

CREATE TABLE IF NOT EXISTS `site` (
  `siteid` int(11) NOT NULL AUTO_INCREMENT,
  `calendar` int(11) NOT NULL,
  `name` text NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  `address1` varchar(128) DEFAULT NULL,
  `address2` varchar(128) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` char(2) DEFAULT NULL,
  `zip` varchar(15) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `fax` varchar(15) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `delivery_note` text NOT NULL,
  PRIMARY KEY (`siteid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `site`
--

INSERT INTO `site` (`siteid`, `calendar`, `name`, `code`, `address1`, `address2`, `city`, `state`, `zip`, `phone`, `fax`, `email`, `delivery_note`) VALUES
(1, 0, 'Home', 'home', '344 Bacon Rd', '', 'Mercer', 'ME', '04957', '207-587-2623', '', 'flaplante@flos-inc.com', 'Leave under cover'),
(2, 2, 'LaPlante Library', 'lib', '344 Bacon Rd', '', 'Mercer', 'ME', '04957', '207-587-2623', '', 'library@flos-inc.com', 'Leave at front door');
