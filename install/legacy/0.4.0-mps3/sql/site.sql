CREATE TABLE site (
  siteid integer NOT NULL auto_increment,
  calendar integer NOT NULL,
  name text NOT NULL,
  code varchar(10) NULL,
  address1 varchar(128) NULL,
  address2 varchar(128) NULL,
  city varchar(50) NULL,
  state char(2) NULL,
  zip varchar(15) NULL,
  phone varchar(15) NULL,
  fax varchar(15) NULL,
  email varchar(128) NULL,
  delivery_note TEXT DEFAULT '' NOT NULL,
  PRIMARY KEY (siteid)
);
