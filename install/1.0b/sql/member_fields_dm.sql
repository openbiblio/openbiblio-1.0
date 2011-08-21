-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2011 at 05:47 PM
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
-- Table structure for table `member_fields_dm`
--

CREATE TABLE IF NOT EXISTS `member_fields_dm` (
  `code` varchar(16) COLLATE latin1_general_ci NOT NULL,
  `description` char(32) COLLATE latin1_general_ci NOT NULL,
  `default_flg` char(1) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `member_fields_dm`
--

INSERT INTO `member_fields_dm` (`code`, `description`, `default_flg`) VALUES
('schoolGrade', 'School Grade', 'N'),
('schoolTeacher', 'School Teacher', 'N');
