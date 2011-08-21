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
-- Table structure for table `lookup_hosts`
--

CREATE TABLE IF NOT EXISTS `lookup_hosts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `seq` tinyint(4) NOT NULL,
  `active` enum('y','n') NOT NULL DEFAULT 'n',
  `host` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `db` varchar(20) NOT NULL,
  `user` varchar(20) DEFAULT NULL,
  `pw` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `lookup_hosts`
--

INSERT INTO `lookup_hosts` (`id`, `seq`, `active`, `host`, `name`, `db`, `user`, `pw`) VALUES
(15, 11, 'n', 'aulib.abdn.ac.uk:9991', 'Aberdeen', 'ABN01', '', ''),
(2, 2, 'n', 'z3950.copac.ac.uk:3000', 'COPAC', 'copac', '', ''),
(3, 3, 'n', 'catalogue.nla.gov.au:7090', 'National Library of Australia', 'voyager', '', ''),
(4, 4, 'n', 'gso.gbv.de/sru/:80', 'German Library Group', '2.1', '', ''),
(5, 5, 'n', 'groar.bne.es:2210', 'Biblioteca Nacional', 'bimo', '', ''),
(6, 6, 'n', 'z3950.bcl.jcyl.es:2109', 'Biblioteca de Castilla y Leon', 'AbsysBCL', '', ''),
(7, 7, 'n', 'bcr1.larioja.org:210', 'Biblioteca de La Rioja (ESP)', 'AbsysE', '', ''),
(8, 8, 'n', 'zed.natlib.govt.nz', 'National Library of New Zealand', 'pinz', '', ''),
(9, 9, 'n', 'pino.csic.es:9909', 'Red de bibliotecas del CSIC', 'MAD01', NULL, NULL),
(12, 10, 'n', 'opac.sbn.it:3950', 'SBN - Sistema Bibliotecario Nazi', 'nopac', '', ''),
(14, 1, 'y', 'z3950.loc.gov:7090', 'U.S. Library of Congress', 'voyager', '', ''),
(17, 2, 'n', 'z3950.gbv.de:210', 'German Group', '2.1', '999', 'abc'),
(18, 12, 'n', 'z3950.gbv.de:210', 'Gemeinsamer Bibliotheksverbund', 'gvk', '999', 'abc');
