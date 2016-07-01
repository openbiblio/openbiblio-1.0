CREATE TABLE IF NOT EXISTS %prfx%.staff (
  userid int(11) NOT NULL AUTO_INCREMENT,
  create_dt datetime NOT NULL,
  last_change_dt datetime NOT NULL,
  last_change_userid int(11) NOT NULL,
  username varchar(20) NOT NULL,
  pwd char(32) NOT NULL,
  last_name varchar(30) NOT NULL,
  first_name varchar(30) DEFAULT NULL,
  secret_key char(32) NOT NULL,
  suspended_flg char(1) NOT NULL,
  admin_flg char(1) NOT NULL,
  circ_flg char(1) NOT NULL,
  circ_mbr_flg char(1) NOT NULL,
  catalog_flg char(1) NOT NULL,
  reports_flg char(1) NOT NULL,
  tools_flg char(1) NOT NULL,
  PRIMARY KEY (userid)
) DEFAULT CHARSET=utf8;
INSERT INTO %prfx%.staff (userid, create_dt, last_change_dt, last_change_userid, username, pwd, last_name, first_name, suspended_flg, admin_flg, circ_flg, circ_mbr_flg, catalog_flg, reports_flg, tools_flg) VALUES
(2, '0000-00-00 00:00:00', '2010-12-18 16:42:03', 2, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Administrator', '', 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y');
