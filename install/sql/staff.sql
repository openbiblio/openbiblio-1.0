create table staff (
  userid integer(5) auto_increment primary key
  ,create_dt date not null
  ,last_updated_dt date not null
  ,username varchar(20) not null
  ,pwd varchar(20) not null
  ,last_name varchar(30) not null
  ,first_name varchar(30) null
  ,suspended_flg char(1) not null
  ,admin_flg char(1) not null
  ,circ_flg char(1) not null
  ,catalog_flg char(1) not null
  )
;

insert into staff
values (null
  ,curdate()
  ,curdate()
  ,'admin'
  ,password('admin')
  ,'Root Administrator'
  ,null
  ,'N'
  ,'Y'
  ,'Y'
  ,'Y'
)
;
