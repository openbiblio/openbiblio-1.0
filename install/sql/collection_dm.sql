create table collection_dm (
  code smallint auto_increment primary key
  ,description varchar(40) not null
  ,default_flg char(1) not null
  ,days_due_back tinyint unsigned not null
)
;

insert into collection_dm values (null,'Adult Fiction','N',21);
insert into collection_dm values (null,'Adult Nonfiction','Y',21);
insert into collection_dm values (null,'Cassettes','N',7);
insert into collection_dm values (null,'Compact Discs','N',7);
insert into collection_dm values (null,'Computer Software','N',7);
insert into collection_dm values (null,'Easy Readers','N',21);
insert into collection_dm values (null,'Juvenile Fiction','N',21);
insert into collection_dm values (null,'Juvenile Nonfiction','N',21);
insert into collection_dm values (null,'New Books','N',14);
insert into collection_dm values (null,'Periodics','N',14);
insert into collection_dm values (null,'Reference','N',0);
insert into collection_dm values (null,'Videos and DVDs','N',3);
