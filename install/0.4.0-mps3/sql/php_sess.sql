CREATE TABLE php_sess (
  id varchar(128) NOT NULL default '',
  last_access_dt datetime default NULL,
  data mediumtext,
  PRIMARY KEY  (id)
);

