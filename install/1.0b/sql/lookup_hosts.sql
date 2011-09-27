-- phpMyAdmin SQL Dump
-- version 3.3.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
<<<<<<< local
-- Generation Time: Sep 21, 2011 at 10:29 PM
=======
-- Generation Time: Sep 26, 2011 at 08:29 AM
>>>>>>> other
-- Server version: 5.1.54
-- PHP Version: 5.3.5-1ubuntu7.2

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
  `port` int(11) unsigned NOT NULL DEFAULT '210',
  `name` varchar(50) NOT NULL,
  `db` varchar(20) NOT NULL,
  `service` enum('Z3950','SRU','SRW') NOT NULL DEFAULT 'Z3950',
  `syntax` varchar(20) NOT NULL DEFAULT '',
  `user` varchar(20) DEFAULT NULL,
  `pw` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `lookup_hosts`
--

INSERT INTO `lookup_hosts` (`id`, `seq`, `active`, `host`, `port`, `name`, `db`, `service`, `syntax`, `user`, `pw`) VALUES
<<<<<<< local
(14, 1, 'n', 'z3950.loc.gov', 7090, 'U.S. Library of Congress - z39.50', 'voyager', 'Z3950', 'marcxml', '', ''),
=======
(14, 1, 'y', 'z3950.loc.gov', 7090, 'U.S. Library of Congress - z39.50', 'voyager', 'Z3950', 'marcxml', '', ''),
>>>>>>> other
(23, 1, 'n', 'z3950.loc.gov', 7090, 'U.S. Library of Congress - SRU', 'voyager', 'SRU', 'dc', '', ''),
<<<<<<< local
(2, 2, 'y', 'copac.ac.uk', 3000, 'UK COPAC - SRU', 'copac', 'SRU', 'dc', '', ''),
(21, 2, 'y', 'z3950.copac.ac.uk', 210, 'UK COPAC - Z3950', 'copac', 'Z3950', 'marcxml', '', ''),
=======
(2, 2, 'n', 'copac.ac.uk', 3000, 'UK COPAC - SRU', 'COPAC', 'SRU', 'dc', '', ''),
(21, 2, 'n', 'z3950.copac.ac.uk', 210, 'UK COPAC - Z3950', 'COPAC', 'Z3950', 'mods', '', ''),
>>>>>>> other
(20, 2, 'n', 'z3950cat.bl.uk', 9909, 'British Lending Library', 'ZBLACU', 'Z3950', 'marcxml', 'NEIRED2005', '5V7W_pb-'),
(3, 3, 'n', 'catalogue.nla.gov.au', 7090, 'Australia National Library', 'voyager', 'Z3950', 'marcxml', '', ''),
(4, 4, 'n', 'gso.gbv.de/sru/', 80, 'German Library Group', '2.1', 'SRU', 'dc', '', ''),
(5, 5, 'n', 'groar.bne.es', 2210, 'Biblioteca Nacional', 'bimo', 'Z3950', 'marcxml', '', ''),
(6, 6, 'n', 'z3950.bcl.jcyl.es', 2109, 'Biblioteca de Castilla y Leon', 'AbsysBCL', 'Z3950', 'marcxml', '', ''),
(7, 7, 'n', 'bcr1.larioja.org', 210, 'Biblioteca de La Rioja (ESP)', 'AbsysE', 'SRU', 'dc', '', ''),
(8, 8, 'n', 'zed.natlib.govt.nz', 210, 'New Zealand National Library', 'pinz', 'SRU', 'dc', '', ''),
(9, 9, 'n', 'pino.csic.es', 9909, 'Red de bibliotecas del CSIC', 'MAD01', 'Z3950', 'marcxml', NULL, NULL),
(12, 10, 'n', 'opac.sbn.it', 3950, 'SBN - Sistema Bibliotecario Nazi', 'nopac', 'Z3950', 'marcxml', '', ''),
(15, 11, 'n', 'aulib.abdn.ac.uk', 9991, 'UK Aberdeen', 'ABN01', 'Z3950', 'marcxml', '', ''),
(18, 12, 'n', 'z3950.gbv.de', 210, 'Gemeinsamer Bibliotheksverbund', 'gvk', 'SRU', 'dc', '999', 'abc');
