SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`member_fields` (
  `mbrid` int(11) NOT NULL,
  `code` varchar(16) COLLATE latin1_general_ci NOT NULL,
  `data` text COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`mbrid`,`code`),
  KEY `code_index` (`code`)
)  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
