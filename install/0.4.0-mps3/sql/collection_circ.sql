create table %prfx%collection_circ (
  code smallint auto_increment primary key
  ,days_due_back tinyint unsigned not null
  ,daily_late_fee decimal(4,2) not null
)
;
