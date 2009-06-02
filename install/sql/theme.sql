create table theme (
  themeid smallint auto_increment primary key
  ,theme_name varchar(40) not null
  ,title_bg varchar(20) not null
  ,title_font_face varchar(128) not null
  ,title_font_size tinyint not null
  ,title_font_bold char(1) not null
  ,title_font_color varchar(20) not null
  ,title_align varchar(30) not null
  ,primary_bg varchar(20) not null
  ,primary_font_face varchar(128) not null
  ,primary_font_size tinyint not null
  ,primary_font_color varchar(20) not null
  ,primary_link_color varchar(20) not null
  ,primary_error_color varchar(20) not null
  ,alt1_bg varchar(20) not null
  ,alt1_font_face varchar(128) not null
  ,alt1_font_size tinyint not null
  ,alt1_font_color varchar(20) not null
  ,alt1_link_color varchar(20) not null
  ,alt2_bg varchar(20) not null
  ,alt2_font_face varchar(128) not null
  ,alt2_font_size tinyint not null
  ,alt2_font_color varchar(20) not null
  ,alt2_link_color varchar(20) not null
  ,alt2_font_bold char(1) not null
  ,border_color varchar(20) not null
  ,border_width tinyint not null
  ,table_padding tinyint not null
)
;

insert into theme values (null,'Mossy Blue','#7695C0','Arial,Helvetica,sans-serif',26,'N','#ffffff','left','#ffffff','verdana,arial,helvetica',13,'#000000','#0000aa','#990000','#CCCC99','verdana,arial,helvetica',13,'#000000','#0000aa','#003366','verdana,arial,helvetica',13,'#ffffff','#ffffff','Y','#000000',1,2);
insert into theme values (null,'Arizona Dessert','#dfa955','Arial,Helvetica,sans-serif',26,'N','#ffffff','left','#ffffff','verdana,arial,helvetica',13,'#000000','#af6622','#990000','#c0c0c0','verdana,arial,helvetica',13,'#000000','#bf7733','#c05232','verdana,arial,helvetica',13,'#ffffff','#ffffff','Y','#000000',1,2);
insert into theme values (null,'Blue and Yellow','#ffffff','Arial,Helvetica,sans-serif',26,'N','#000000','left','#ffffff','verdana,arial,helvetica',13,'#000000','#0000aa','#990000','#f0f0d5','verdana,arial,helvetica',13,'#000000','#0000aa','#495fa8','verdana,arial,helvetica',13,'#ffffdb','#ffffdb','Y','#000000',1,2);
insert into theme values (null,'Dark Wood','#551122','Arial,Helvetica,sans-serif',26,'N','#ffffff','left','#000000','arial',13,'#ffffff','#ffff99','#990000','#393333','arial',13,'#ffffff','#ffff99','#999080','verdana,arial,helvetica',13,'#ffffff','#ffffff','Y','#a9a090',1,2);
insert into theme values (null,'Metalic Grey','#ffffff','Arial,Helvetica,sans-serif',26,'N','#000000','left','#f0f0f0','verdana,arial,helvetica',13,'#000000','#0000aa','#990000','#e0e0e0','verdana,arial,helvetica',13,'#000000','#0000aa','#c9cfde','verdana,arial,helvetica',13,'#000000','#000000','Y','#000000',1,2);
insert into theme values (null,'Midnight','#222255','Arial,Helvetica,sans-serif',26,'N','#ffffff','left','#000000','arial',13,'#b5b5db','#ffff99','#990000','#333366','arial',13,'#ffffff','#ffff99','#8585ab','verdana,arial,helvetica',13,'#ffffff','#ffffff','N','#b5b5db',1,2);
