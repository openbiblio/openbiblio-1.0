create table biblio_copy (
  bibid integer not null,
  copyid integer auto_increment not null,
  create_dt datetime NOT NULL,
  last_change_dt datetime NOT NULL,
  last_change_userid integer NOT NULL,
  barcode_nmbr varchar(20) not null,
  copy_desc varchar(160) null,
  vendor varchar(100) null,
  fund varchar(100) null,
  price varchar(10) null,
  expiration varchar(100) null,
  histid integer null,
  primary key (copyid),
  index bibid_idx (bibid),
  index barcode_index (barcode_nmbr),
  unique histid_idx (histid)
  )
;
