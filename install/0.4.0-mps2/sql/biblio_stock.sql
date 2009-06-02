create table %prfx%biblio_stock (
  bibid integer not null,
  count integer not null,
  vendor varchar(100) null,
  fund varchar(100) null,
  price varchar(10) null,
  primary key (bibid)
  )
;
