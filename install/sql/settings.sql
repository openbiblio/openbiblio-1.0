create table settings (
  library_name varchar(128) null
  ,library_image_url text null
  ,use_image_flg char(1) not null
  ,library_hours varchar(128) null
  ,library_phone varchar(40) null
  ,library_url text null
  ,opac_url text null
  ,session_timeout smallint not null
  ,items_per_page tinyint not null
  ,version varchar(10) not null
  ,themeid smallint not null
)
;

insert into settings
values (
  'Your Library Name Goes Here'
  ,null
  ,'N'
  ,'M-F 8am-9pm, Sa noon-5pm, Su 1-5pm'
  ,'111-222-3333'
  ,'../home/index.php'
  ,'../opac/index.php'
  ,20
  ,10
  ,'0.3.0'
  ,1
)
;
