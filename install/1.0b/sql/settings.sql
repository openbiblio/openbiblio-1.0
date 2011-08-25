-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 26, 2011 at 12:23 AM
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
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `name` varchar(64) NOT NULL DEFAULT '',
  `position` int(11) DEFAULT NULL,
  `title` text,
  `type` enum('text','int','bool','select') NOT NULL DEFAULT 'text',
  `width` int(11) DEFAULT NULL,
  `type_data` text,
  `validator` text,
  `value` text,
  `menu` enum('admin','tools','none') NOT NULL DEFAULT 'admin',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`name`, `position`, `title`, `type`, `width`, `type_data`, `validator`, `value`, `menu`) VALUES
('plugin_list', NULL, NULL, 'text', NULL, NULL, NULL, ',lookup2,biblioFlds', ''),
('allow_plugins_flg', 0, 'Allow Plugins', 'bool', NULL, NULL, NULL, 'Y', 'tools'),
('library_name', 1, 'Library Title', 'select', NULL, 'sites', NULL, '2', 'admin'),
('item_barcode_flg', 1, 'Use item barcodes', 'bool', NULL, NULL, NULL, 'Y', 'tools'),
('library_hours', 2, 'Library Hours', 'text', 32, NULL, NULL, 'M-F: 8am - 5pm<br />Sat:  9am - noon', 'admin'),
('item_autoBarcode_flg', 2, 'Item Auto Barcodes', 'bool', NULL, NULL, NULL, 'Y', 'tools'),
('library_phone', 3, 'Library Phone No.', 'text', NULL, NULL, NULL, '207-587-2623', 'admin'),
('library_home', 4, 'Library Address', 'text', NULL, NULL, NULL, 'Mercer, Maine', 'admin'),
('block_checkouts_when_fines_due', 5, 'Block Checkouts When Fines Due', 'bool', 1, NULL, NULL, 'Y', 'admin'),
('locale', 6, 'Locale', 'select', NULL, 'locales', NULL, 'en', 'admin'),
('charset', 7, 'Character Set', 'text', NULL, NULL, NULL, '', 'admin'),
('items_per_page', 8, 'Items per Page', 'int', NULL, NULL, NULL, '25', 'admin'),
('request_from', 9, 'Request From', 'text', NULL, NULL, NULL, '', 'admin'),
('request_to', 10, 'Request To', 'text', NULL, NULL, NULL, '', 'admin'),
('mbr_barcode_flg', 10, 'Use Member barcodes', 'bool', NULL, NULL, NULL, 'N', 'tools'),
('request_subject', 11, 'Request Subject', 'text', NULL, NULL, NULL, '', 'admin'),
('mbr_autoBarcode_flg', 11, 'Member Auto Barcodes', 'bool', NULL, NULL, NULL, 'Y', 'tools'),
('library_url', 12, 'Library URL', 'text', NULL, NULL, NULL, 'library.flos-inc.com', 'admin'),
('opac_url', 13, 'OPAC URL', 'text', 32, NULL, NULL, 'opac.flos-inc.com', 'admin'),
('library_image_url', 14, 'Library Image URL', 'text', 32, NULL, NULL, '../images/pond.jpg', 'admin'),
('themeid', 15, 'Dark Wood', 'int', 10, NULL, NULL, '2', 'admin'),
('theme_dir_url', 16, 'Theme Dir URL', 'select', NULL, 'themes', NULL, '../themes/default', 'admin'),
('use_image_flg', 17, 'Use Image', 'bool', NULL, NULL, NULL, 'N', 'admin'),
('show_checkout_mbr', 20, 'Show member who has an item checked out', 'bool', NULL, NULL, NULL, 'Y', 'tools'),
('show_item_photos', 21, 'Show Item Photos', 'bool', NULL, NULL, NULL, 'Y', 'tools'),
('show_detail_opac', 22, 'Show copy details in OPAC', 'bool', NULL, NULL, NULL, 'Y', 'tools'),
('multi_site_func', 23, 'Default site for multiple site functionality (0 = disabled)', 'int', NULL, NULL, NULL, '2', 'tools'),
('site_login', 25, 'Select a Site at Logon', 'bool', NULL, NULL, NULL, 'N', 'tools'),
('checkout_interval', 26, 'Checkout_Interval', 'select', NULL, NULL, NULL, '1', 'tools'),
('item_barcode_width', 27, 'Item Barcode Width', 'int', NULL, NULL, NULL, '13', 'tools'),
('show_lib_info', 28, 'Show Lib Info on Staff pages', 'bool', NULL, NULL, NULL, 'N', 'admin'),
('thumbnail_width', 29, 'Thumbnail Max Width', 'int', NULL, NULL, NULL, '100', 'admin'),
('thumbnail_height', 30, 'Thumbnail Max Height', 'int', NULL, NULL, NULL, '150', 'admin'),
('version', 33, NULL, 'text', NULL, '\0', NULL, '1.0b', 'none');
