create table biblio_topic (
  primary key (bibid, topicid)
  ,bibid integer(10) not null
  ,topicid integer(5) not null
  ,description varchar(128) not null
  ,subdivision varchar(128) null
  )
;
