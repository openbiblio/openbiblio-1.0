create table mbr_classify_dm (
  code char(1) primary key
  ,description varchar(40) not null
  ,default_flg char(1) not null
)
;

insert into mbr_classify_dm values ('a','adult','Y');
insert into mbr_classify_dm values ('j','juvenile','N');
