CREATE TABLE `settings` (
  `name` varchar(64) NOT NULL default '',
  `position` int(11) default NULL,
  `title` text,
  `type` enum('text','int','bool','select') NOT NULL default 'text',
  `width` int(11) default NULL,
  `type_data` text,
  `validator` text,
  `value` text,
  PRIMARY KEY  (`name`)
);
