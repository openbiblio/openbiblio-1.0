create table biblio_status_dm (
  code char(3) primary key
  ,description varchar(40) not null
  ,default_flg char(1) not null
)
;
insert into biblio_status_dm values ('in','checked in','N');
insert into biblio_status_dm values ('out','checked out','Y');
insert into biblio_status_dm values ('mnd','damaged/mending','N');
insert into biblio_status_dm values ('dis','display area','N');
insert into biblio_status_dm values ('cll','hold call','N');
insert into biblio_status_dm values ('hld','hold wait','N');
insert into biblio_status_dm values ('lst','lost','N');
insert into biblio_status_dm values ('ln','on loan','N');
insert into biblio_status_dm values ('ord','on order','N');
insert into biblio_status_dm values ('crt','shelving cart','N');
