create table collection_dm (
  code smallint auto_increment primary key
  ,description varchar(40) not null
  ,default_flg char(1) not null
  ,type enum('Circulated', 'Distributed') default 'Circulated' not null
)
;
