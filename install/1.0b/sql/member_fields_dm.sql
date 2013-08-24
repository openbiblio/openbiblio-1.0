SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`member_fields_dm` (
  `code` varchar(16) COLLATE latin1_general_ci NOT NULL,
  `description` char(32) COLLATE latin1_general_ci NOT NULL,
  `default_flg` char(1) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO %prfx%.`member_fields_dm` (`code`, `description`, `default_flg`) VALUES
('schoolGrade', 'School Grade', 'N'),
('schoolTeacher', 'School Teacher', 'N');
