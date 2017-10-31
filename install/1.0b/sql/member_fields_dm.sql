CREATE TABLE IF NOT EXISTS %prfx%.`member_fields_dm` (
  `code` varchar(16) NOT NULL,
  `description` char(32) NOT NULL,
  `default_flg` char(1),
  PRIMARY KEY (`code`)
)   DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO %prfx%.`member_fields_dm` (`code`, `description`, `default_flg`) VALUES
('schoolGrade', 'School Grade', 'N'),
('schoolTeacher', 'School Teacher', 'N');
