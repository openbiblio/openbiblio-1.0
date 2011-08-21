CREATE TABLE biblio_subfield (
  bibid integer NOT NULL,
  fieldid integer NOT NULL,
  subfieldid integer auto_increment NOT NULL,
  seq integer NOT NULL,
  subfield_cd char(1) NOT NULL,
  subfield_data text NOT NULL,
  PRIMARY KEY (subfieldid),
  KEY bibid_idx (bibid),
  KEY fieldid_idx (fieldid),
  KEY subfield_cd_idx (subfield_cd)
);
