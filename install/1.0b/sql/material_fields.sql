-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2011 at 05:46 PM
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
-- Table structure for table `material_fields`
--

CREATE TABLE IF NOT EXISTS `material_fields` (
  `material_field_id` int(4) NOT NULL AUTO_INCREMENT,
  `material_cd` int(11) DEFAULT NULL,
  `tag` char(3) NOT NULL,
  `subfield_cd` varchar(10) DEFAULT NULL,
  `position` tinyint(4) NOT NULL,
  `label` varchar(128) DEFAULT NULL,
  `form_type` enum('text','textarea') NOT NULL DEFAULT 'text',
  `required` tinyint(1) NOT NULL,
  `repeatable` tinyint(1) DEFAULT NULL,
  `search_results` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`material_field_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=63 ;

--
-- Dumping data for table `material_fields`
--

INSERT INTO `material_fields` (`material_field_id`, `material_cd`, `tag`, `subfield_cd`, `position`, `label`, `form_type`, `required`, `repeatable`, `search_results`) VALUES
(1, 2, '245', 'a', 0, 'Title', 'text', 1, 0, NULL),
(2, 2, '245', 'b', 1, 'Subtitle', 'text', 0, 0, NULL),
(3, 2, '099', 'a', 2, 'Call Number', 'text', 1, 0, NULL),
(4, 2, '245', 'c', 4, 'Statement of Responsibility', 'text', 0, 0, NULL),
(7, 2, '650', 'a', 8, 'Subject', 'text', 0, 4, NULL),
(8, 2, '250', 'a', 6, 'Edition', 'text', 0, 0, NULL),
(9, 2, '020', 'a', 9, 'ISBN', 'text', 0, 0, NULL),
(54, 7, '44', 'a', 4, 'Country of publishing/producing entity code', 'text', 0, 0, NULL),
(11, 2, '260', 'a', 17, 'Place of Publication', 'text', 0, 0, NULL),
(12, 2, '260', 'b', 16, 'Publisher', 'text', 0, 0, NULL),
(13, 2, '260', 'c', 18, 'Date of Publication', 'text', 0, 0, NULL),
(39, 6, '100', 'a', 6, '100a - Personal name - Author', 'text', 0, NULL, NULL),
(20, 2, '505', 'a', 19, 'Contents', 'textarea', 0, 0, NULL),
(24, 2, '100', 'a', 3, 'Author', 'text', 1, 0, NULL),
(58, 7, '342', 'g', 10, '342g - Longitude of central meridian or projection center', 'text', 0, NULL, NULL),
(37, 6, '245', 'f', 2, '245f - Inclusive dates', 'text', 0, NULL, NULL),
(38, 6, '245', 'h', 3, '245h - Medium', 'text', 0, NULL, NULL),
(29, 2, '050', 'a', 10, 'US LoC Classification', 'text', 0, 0, NULL),
(30, 2, '050', 'b', 11, 'US LoC Item Number', 'text', 0, 0, NULL),
(31, 2, '082', 'a', 14, 'Dewey Classification', 'text', 0, 0, NULL),
(32, 2, '082', '2', 15, 'Dewey edition', 'text', 0, 0, NULL),
(33, 2, '700', 'a', 5, 'Additional contributors', 'text', 0, 0, NULL),
(40, 6, '22', 'a', 9, '022a - International Standard Serial Number', 'text', 0, NULL, NULL),
(41, 6, '50', 'a', 12, '050a - Classification number', 'text', 0, NULL, NULL),
(55, 7, '110', 'a', 5, '110a - Corporate name or jurisdiction name as entry element', 'text', 0, NULL, NULL),
(43, 6, '410', 'a', 0, '410a - Corporate name or jurisdiction name as entry element', 'text', 0, NULL, NULL),
(44, 6, '245', 'a', 1, '245a - Title', 'text', 0, NULL, NULL),
(57, 7, '342', 'd', 8, '342d - Longitude resolution', 'text', 0, NULL, NULL),
(56, 7, '342', 'c', 7, '342c - Latitude resolution', 'text', 0, NULL, NULL),
(50, 6, '50', 'b', 13, '050b - Item number', 'text', 0, NULL, NULL),
(59, 7, '342', 'q', 11, '342q - Ellipsoid name', 'text', 0, NULL, NULL),
(60, 7, '342', 'w', 14, '342w - Local planar or local georeference information', 'text', 0, NULL, NULL),
(61, 7, '342', 'h', 15, '342h - Latitude of projection origin or projection center', 'text', 0, NULL, NULL),
(62, 1, '20', 'a', 0, '020a - International Standard Book Number', 'text', 0, NULL, NULL);
