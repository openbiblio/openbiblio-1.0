-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 25, 2011 at 08:42 PM
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=93 ;

--
-- Dumping data for table `material_fields`
--

INSERT INTO `material_fields` (`material_field_id`, `material_cd`, `tag`, `subfield_cd`, `position`, `label`, `form_type`, `required`, `repeatable`, `search_results`) VALUES
(1, 2, '245', 'a', 0, 'Title', 'text', 1, 0, NULL),
(2, 2, '245', 'b', 1, 'Subtitle', 'text', 0, 0, NULL),
(3, 2, '099', 'a', 0, 'Call Number', 'text', 1, 0, NULL),
(4, 2, '245', 'c', 4, 'Statement of Responsibility', 'text', 0, 0, NULL),
(63, 8, '245', 'a', 0, 'Title', 'text', 0, 0, NULL),
(7, 2, '650', 'a', 8, 'Subject', 'text', 0, 4, NULL),
(8, 2, '250', 'a', 6, 'Edition', 'text', 0, 0, NULL),
(9, 2, '020', 'a', 9, 'ISBN', 'text', 0, 0, NULL),
(54, 7, '44', 'a', 1, 'Country of publishing/producing entity code', 'text', 0, 0, NULL),
(11, 2, '260', 'a', 17, 'Place of Publication', 'text', 0, 0, NULL),
(12, 2, '260', 'b', 16, 'Publisher', 'text', 0, 0, NULL),
(13, 2, '260', 'c', 18, 'Date of Publication', 'text', 0, 0, NULL),
(39, 6, '100', 'a', 5, 'Personal name - Author', 'text', 0, 0, NULL),
(64, 8, '245', 'b', 1, 'Remainder of title', 'text', 0, 0, NULL),
(20, 2, '505', 'a', 19, 'Contents', 'textarea', 0, 0, NULL),
(24, 2, '100', 'a', 3, 'Author', 'text', 1, 0, NULL),
(58, 7, '342', 'g', 5, 'Longitude of central meridian or projection center', 'text', 0, 0, NULL),
(37, 6, '245', 'f', 3, 'Inclusive dates', 'text', 0, 0, NULL),
(38, 6, '245', 'h', 4, 'Medium', 'text', 0, 0, NULL),
(79, 8, '50', 'a', 12, 'Classification number', 'text', 0, NULL, NULL),
(65, 8, '245', 'c', 2, 'Statement of responsibility, etc.', 'text', 0, 0, NULL),
(29, 2, '050', 'a', 10, 'US LoC Classification', 'text', 0, 0, NULL),
(30, 2, '050', 'b', 11, 'US LoC Item Number', 'text', 0, 0, NULL),
(31, 2, '082', 'a', 14, 'Dewey Classification', 'text', 0, 0, NULL),
(32, 2, '082', '2', 15, 'Dewey edition', 'text', 0, 0, NULL),
(33, 2, '700', 'a', 5, 'Additional contributors', 'text', 0, 0, NULL),
(40, 6, '22', 'a', 6, 'International Standard Serial Number', 'text', 0, 0, NULL),
(41, 6, '50', 'a', 7, 'Classification number', 'text', 0, 0, NULL),
(55, 7, '110', 'a', 2, 'Corporate name or jurisdiction name as entry element', 'text', 0, 0, NULL),
(43, 6, '410', 'a', 1, 'Corporate name or jurisdiction name as entry element', 'text', 0, 0, NULL),
(44, 6, '245', 'a', 2, 'Title', 'text', 0, 0, NULL),
(57, 7, '342', 'd', 4, 'Longitude resolution', 'text', 0, 0, NULL),
(75, 8, '300', 'b', 4, 'Other physical details', 'text', 0, NULL, NULL),
(56, 7, '342', 'c', 3, 'Latitude resolution', 'text', 0, 0, NULL),
(50, 6, '50', 'b', 8, 'Item number', 'text', 0, 0, NULL),
(59, 7, '342', 'q', 6, 'Ellipsoid name', 'text', 0, 0, NULL),
(60, 7, '342', 'w', 7, 'Local planar or local georeference information', 'text', 0, 0, NULL),
(61, 7, '342', 'h', 8, 'Latitude of projection origin or projection center', 'text', 0, 0, NULL),
(62, 1, '20', 'a', 1, 'International Standard Book Number', 'text', 0, 0, NULL),
(66, 8, '245', 'h', 3, 'Medium', 'text', 0, 0, NULL),
(67, 8, '306', 'a', 6, 'Playing time', 'text', 0, 0, NULL),
(68, 8, '260', 'c', 7, 'Date of publication, distribution, etc.', 'text', 0, 0, NULL),
(69, 8, '260', 'b', 8, 'Name of publisher, distributor, etc.', 'text', 0, 0, NULL),
(70, 8, '260', 'a', 9, 'Place of publication, distribution, etc.', 'text', 0, 0, NULL),
(76, 8, '300', 'c', 5, 'Dimensions', 'text', 0, NULL, NULL),
(77, 8, '505', 'a', 10, 'Formatted contents note', 'textarea', 0, 0, NULL),
(78, 8, '10', 'a', 11, 'LC control number', 'text', 0, NULL, NULL),
(80, 8, '50', 'b', 13, 'Item number', 'text', 0, NULL, NULL),
(81, 8, '82', 'a', 14, 'Classification number', 'text', 0, NULL, NULL),
(82, 8, '82', 'b', 15, 'Item number', 'text', 0, NULL, NULL),
(83, 8, '82', '2', 16, 'Edition number', 'text', 0, NULL, NULL),
(84, 8, '80', 'a', 17, 'Universal Decimal Classification number', 'text', 0, NULL, NULL),
(85, 8, '80', 'b', 18, 'Item number', 'text', 0, NULL, NULL),
(86, 8, '80', '2', 19, 'Edition identifier', 'text', 0, NULL, NULL),
(87, 3, '099', 'a', 0, 'Call Number', 'text', 1, 0, NULL),
(88, 4, '099', 'a', 0, 'Call Number', 'text', 1, 0, NULL),
(89, 5, '099', 'a', 0, 'Call Number', 'text', 0, NULL, NULL),
(90, 1, '099', 'a', 0, 'Call Number', 'text', 0, NULL, NULL),
(91, 6, '099', 'a', 0, 'Call Number', 'text', 0, NULL, NULL),
(92, 7, '099', 'a', 0, 'Call Number', 'text', 0, NULL, NULL);
