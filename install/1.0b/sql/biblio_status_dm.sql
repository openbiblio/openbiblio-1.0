-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2011 at 05:39 PM
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
-- Table structure for table `biblio_status_dm`
--

CREATE TABLE IF NOT EXISTS `biblio_status_dm` (
  `code` char(3) NOT NULL,
  `description` varchar(40) NOT NULL,
  `default_flg` char(1) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `biblio_status_dm`
--

INSERT INTO `biblio_status_dm` (`code`, `description`, `default_flg`) VALUES
('in', 'checked in', 'Y'),
('out', 'checked out', 'N'),
('mnd', 'damaged/mending', 'N'),
('dis', 'display area', 'N'),
('hld', 'on hold', 'N'),
('lst', 'lost', 'N'),
('ln', 'on loan', 'N'),
('ord', 'on order', 'N'),
('crt', 'shelving cart', 'N');
