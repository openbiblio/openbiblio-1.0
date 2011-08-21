CREATE TABLE biblio (
  bibid integer auto_increment NOT NULL,
  create_dt datetime NOT NULL,
  last_change_dt datetime NOT NULL,
  last_change_userid integer NOT NULL,
  material_cd smallint NOT NULL,
  collection_cd smallint NOT NULL,
  opac_flg char(1) NOT NULL,
  PRIMARY KEY (bibid)
);
