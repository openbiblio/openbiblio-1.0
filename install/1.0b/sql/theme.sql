-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 21, 2011 at 05:50 PM
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
-- Table structure for table `theme`
--

CREATE TABLE IF NOT EXISTS `theme` (
  `themeid` smallint(6) NOT NULL AUTO_INCREMENT,
  `theme_name` varchar(40) NOT NULL,
  `title_bg` varchar(20) NOT NULL,
  `title_font_face` varchar(128) NOT NULL,
  `title_font_size` tinyint(4) NOT NULL,
  `title_font_bold` char(1) NOT NULL,
  `title_font_color` varchar(20) NOT NULL,
  `title_align` varchar(30) NOT NULL,
  `primary_bg` varchar(20) NOT NULL,
  `primary_font_face` varchar(128) NOT NULL,
  `primary_font_size` tinyint(4) NOT NULL,
  `primary_font_color` varchar(20) NOT NULL,
  `primary_link_color` varchar(20) NOT NULL,
  `primary_error_color` varchar(20) NOT NULL,
  `alt1_bg` varchar(20) NOT NULL,
  `alt1_font_face` varchar(128) NOT NULL,
  `alt1_font_size` tinyint(4) NOT NULL,
  `alt1_font_color` varchar(20) NOT NULL,
  `alt1_link_color` varchar(20) NOT NULL,
  `alt2_bg` varchar(20) NOT NULL,
  `alt2_font_face` varchar(128) NOT NULL,
  `alt2_font_size` tinyint(4) NOT NULL,
  `alt2_font_color` varchar(20) NOT NULL,
  `alt2_link_color` varchar(20) NOT NULL,
  `alt2_font_bold` char(1) NOT NULL,
  `border_color` varchar(20) NOT NULL,
  `border_width` tinyint(4) NOT NULL,
  `table_padding` tinyint(4) NOT NULL,
  PRIMARY KEY (`themeid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `theme`
--

INSERT INTO `theme` (`themeid`, `theme_name`, `title_bg`, `title_font_face`, `title_font_size`, `title_font_bold`, `title_font_color`, `title_align`, `primary_bg`, `primary_font_face`, `primary_font_size`, `primary_font_color`, `primary_link_color`, `primary_error_color`, `alt1_bg`, `alt1_font_face`, `alt1_font_size`, `alt1_font_color`, `alt1_link_color`, `alt2_bg`, `alt2_font_face`, `alt2_font_size`, `alt2_font_color`, `alt2_link_color`, `alt2_font_bold`, `border_color`, `border_width`, `table_padding`) VALUES
(1, 'Mossy Blue', '#7695C0', 'Arial,Helvetica,sans-serif', 26, 'N', '#ffffff', 'left', '#ffffff', 'verdana,arial,helvetica', 13, '#000000', '#0000aa', '#990000', '#CCCC99', 'verdana,arial,helvetica', 13, '#000000', '#0000aa', '#003366', 'verdana,arial,helvetica', 13, '#ffffff', '#ffffff', 'Y', '#000000', 1, 2),
(2, 'Arizona Dessert', '#dfa955', 'Arial,Helvetica,sans-serif', 26, 'N', '#ffffff', 'left', '#ffffff', 'verdana,arial,helvetica', 13, '#000000', '#af6622', '#990000', '#c0c0c0', 'verdana,arial,helvetica', 13, '#000000', '#bf7733', '#c05232', 'verdana,arial,helvetica', 13, '#ffffff', '#ffffff', 'Y', '#000000', 1, 2),
(3, 'Blue and Green', '#aaaaff', 'Arial,Helvetica,sans-serif', 26, 'N', '#000055', 'left', '#ffffff', 'verdana,arial,helvetica', 13, '#000055', '#000088', '#990000', '#aaffaa', 'verdana,arial,helvetica', 13, '#005500', '#000088', '#4444ff', 'verdana,arial,helvetica', 13, '#ffffff', '#ffffff', 'Y', '#000055', 1, 2),
(4, 'Dark Wood', '#551122', 'Arial,Helvetica,sans-serif', 26, 'N', '#ffffff', 'left', '#000000', 'arial', 13, '#ffffff', '#ffff99', '#990000', '#393333', 'arial', 13, '#ffffff', '#ffff99', '#999080', 'verdana,arial,helvetica', 13, '#ffffff', '#ffffff', 'Y', '#a9a090', 1, 2),
(5, 'Metalic Grey', '#ffffff', 'Arial,Helvetica,sans-serif', 26, 'N', '#000000', 'left', '#f0f0f0', 'verdana,arial,helvetica', 13, '#000000', '#0000aa', '#990000', '#e0e0e0', 'verdana,arial,helvetica', 13, '#000000', '#0000aa', '#c9cfde', 'verdana,arial,helvetica', 13, '#000000', '#000000', 'Y', '#000000', 1, 2),
(6, 'Midnight', '#222255', 'Arial,Helvetica,sans-serif', 26, 'N', '#ffffff', 'left', '#000000', 'arial', 13, '#b5b5db', '#ffff99', '#990000', '#333366', 'arial', 13, '#ffffff', '#ffff99', '#8585ab', 'verdana,arial,helvetica', 13, '#ffffff', '#ffffff', 'N', '#b5b5db', 1, 2);
