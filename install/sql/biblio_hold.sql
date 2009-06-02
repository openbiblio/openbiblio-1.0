create table biblio_hold (
  bibid integer not null
  ,hold_begin_dt date not null
  ,mbrid integer null
  ,key mbr_index (mbrid)
  )
;
