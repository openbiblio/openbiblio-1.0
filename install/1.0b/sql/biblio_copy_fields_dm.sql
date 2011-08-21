-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2011 at 05:38 PM
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
-- Table structure for table `biblio_copy_fields_dm`
--

CREATE TABLE IF NOT EXISTS `biblio_copy_fields_dm` (
  `code` varchar(16) NOT NULL DEFAULT '',
  `description` varchar(32) NOT NULL DEFAULT '',
  `default_flg` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `biblio_copy_fields_dm`
--

INSERT INTO `biblio_copy_fields_dm` (`code`, `description`, `default_flg`) VALUES
('pr', 'Price', 'N'),
('src', 'Source', 'N'),
('cvr', 'Cover Type', 'N');
