create table member (
  mbrid integer auto_increment primary key
  ,barcode_nmbr bigint not null
  ,create_dt date not null
  ,last_updated_dt date not null
  ,last_name varchar(50) not null
  ,first_name varchar(50) not null
  ,address1 varchar(128) null
  ,address2 varchar(128) null
  ,city varchar(50) null
  ,state char(2) null
  ,zip mediumint null
  ,zip_ext smallint null
  ,home_phone varchar(15) null
  ,work_phone varchar(15) null
  ,classification char(1) not null
  ,school_grade tinyint null
  ,school_teacher varchar(50) null
  )
;
