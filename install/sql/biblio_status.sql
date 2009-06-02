create table biblio_status (
  bibid integer not null primary key
  ,status_begin_dt date not null
  ,status_cd char(3) not null
  ,mbrid integer null
  ,status_renew_dt date null
  ,due_back_dt date null
  ,key mbr_index (mbrid)
  )
;
