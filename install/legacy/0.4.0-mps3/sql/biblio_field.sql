CREATE TABLE biblio_field (
  bibid integer NOT NULL,
  fieldid integer auto_increment NOT NULL,
  seq integer NOT NULL,
  tag char(3) NOT NULL,
  ind1_cd char(1) NULL,
  ind2_cd char(1) NULL,
  field_data text default NULL,
  display text default NULL,
  PRIMARY KEY (fieldid),
  KEY bibid_idx (bibid),
  KEY tag_idx (tag)
);
