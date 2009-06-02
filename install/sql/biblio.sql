create table biblio (
  bibid integer auto_increment primary key
  ,barcode_nmbr bigint not null
  ,create_dt date not null
  ,last_updated_dt date not null
  ,material_cd smallint not null
  ,collection_cd smallint not null
  ,title text not null
  ,subtitle text null
  ,author varchar(128) not null
  ,add_author varchar(128) null
  ,edition varchar(50) null
  ,call_nmbr varchar(30) not null
  ,lccn_nmbr varchar(50) null
  ,isbn_nmbr varchar(50) null
  ,lc_call_nmbr varchar(50) null
  ,lc_item_nmbr varchar(50) null
  ,udc_nmbr varchar(50) null
  ,udc_ed_nmbr varchar(10) null
  ,publisher varchar(128) null
  ,publication_dt varchar(50) null
  ,publication_loc varchar(50) null
  ,summary text null
  ,pages varchar(50) null
  ,physical_details varchar(128) null
  ,dimensions varchar(50) null
  ,accompanying varchar(128) null
  ,price decimal(6,2) null
  )
;
