CREATE TABLE IF NOT EXISTS staff (
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
  start_page varchar(64) DEFAULT 'admin' NOT NULL,
  PRIMARY KEY (userid)
)   DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO staff (userid, create_dt, last_change_dt, last_change_userid, username, pwd, last_name, first_name, secret_key, suspended_flg, admin_flg, circ_flg, circ_mbr_flg, catalog_flg, reports_flg, tools_flg, start_page) VALUES
(1, '2017-05-06 00:00:01', '2017-05-06 00:00:01', 2, 'admin', MD5('admin'), 'Administrator', '', MD5('admin'), 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'admin');