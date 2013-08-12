-- phpMyAdmin SQL Dump
-- version 3.5.8.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 11, 2013 at 02:35 PM
-- Server version: 5.5.32-0ubuntu0.13.04.1
-- PHP Version: 5.4.9-4ubuntu2.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `openbibliowork`
--

-- --------------------------------------------------------

--
-- Table structure for table `validation_dm`
--

CREATE TABLE IF NOT EXISTS `validation_dm` (
  `code` char(8) NOT NULL,
  `description` varchar(32) NOT NULL,
  `pattern` varchar(1024) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='for HTML5 input field validations';

--
-- Dumping data for table `validation_dm`
--

INSERT INTO `validation_dm` (`code`, `description`, `pattern`) VALUES
('isbn', 'ISBN', '((978[\\--â€“ ])?[0-9][0-9\\--â€“ ]{10}[\\--â€“ ][0-9xX])|((978)?[0-9]{9}[0-9Xx])'),
('ipv4', 'IPv4', '^(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[0-9]{1,2})(\\.(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[0-9]{1,2})){3}$'),
('url', 'URL', '(http|ftp|https):\\/\\/[\\w\\-_]+(\\.[\\w\\-_]+)+([\\w\\-\\.,@?^=%&amp;:/~\\+#]*[\\w\\-\\@?^=%&amp;/~\\+#])?'),
('email', 'eMail', '(\\w[-._\\w]*\\w@\\w[-._\\w]*\\w\\.\\w{2,3})'),
('zip-us', 'Postal Code US/Canada', '^\\d{5}-\\d{4}|\\d{5}|[A-Z]\\d[A-Z] \\d[A-Z]\\d$'),
('tel-uk', 'Phone No. UK', '(\\s*\\(?0\\d{4}\\)?(\\s*|-)\\d{3}(\\s*|-)\\d{3}\\s*)|(\\s*\\(?0\\d{3}\\)?(\\s*|-)\\d{3}(\\s*|-)\\d{4}\\s*)|(\\s*(7|8)(\\d{7}|\\d{3}(\\-|\\s{1})\\d{4})\\s*)'),
('zip-uk', 'Postal Code UK', '(((^[BEGLMNS][1-9]\\d?)|(^W[2-9])|(^(A[BL]|B[ABDHLNRST]|C[ABFHMORTVW]|D[ADEGHLNTY]|E[HNX]|F[KY]|G[LUY]|H[ADGPRSUX]|I[GMPV]|JE|K[ATWY]|L[ADELNSU]|M[EKL]|N[EGNPRW]|O[LX]|P[AEHLOR]|R[GHM]|S[AEGKL-PRSTWY]|T[ADFNQRSW]|UB|W[ADFNRSV]|YO|ZE)\\d\\d?)|(^W1[A-HJKSTUW0-9])|(((^WC[1-2])|(^EC[1-4])|(^SW1))[ABEHMNPRVWXY]))(\\s*)?([0-9][ABD-HJLNP-UW-Z]{2}))$|(^GIR\\s?0AA$)'),
('tel-us', 'Phone No. US/Canada', '^([0-9]( |-)?)?(\\(?[0-9]{3}\\)?|[0-9]{3})( |-)?([0-9]{3}( |-)?[0-9]{4}|[a-zA-Z0-9]{7})$'),
('loc', 'LoC Call No.', '^(?P<aclass>[A-Z]{1,3}) (?P<nclass>\\\\d{1,4})(\\\\ ?) (\\\\.(?P<dclass>\\\\d{1,3}))? (?P<date>\\\\ [A-Za-z0-9]{1,4}\\\\ )? ([\\\\ \\\\.](?P<c1>[A-Z][0-9]{1,4})) (\\\\ (?P<c1d>[A-Za-z0-9]{0,4}))? (\\\\.?(?P<c2>[A-Z][0-9]{1,4}))? (\\\\ (?P<e8>\\\\w*)\\\\ ?)? (\\\\ (?P<e9>\\\\w*)\\\\ ?)? (\\\\ (?P<e10>\\\\w*)\\\\ ?)?'),
('issn', 'ISSN', '\\d{4}\\-\\d{3}(\\d|x|X)'),
('date', 'date - YYYY-MM-DD', '(19|20)\\d{2}-((0[1-9])|(1[0-2]))-((0[1-9])|([1-2][0-9])|(3[0-1]))'),
('year', 'Year - 1800-2099', '(18|19|20)\\d{2}'),
('tel', 'Phone No.', '^([0-9]( |-)?)?(\\(?[0-9]{3}\\)?|[0-9]{3})( |-)?([0-9]{3}( |-)?[0-9]{4}|[a-zA-Z0-9]{7})$'),
('zip', 'Postal Code', '^\\d{5}-\\d{4}|\\d{5}|[A-Z]\\d[A-Z] \\d[A-Z]\\d$');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
