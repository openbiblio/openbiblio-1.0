CREATE TABLE `images` (
  `bibid` integer NOT NULL,
  `imgurl` text NOT NULL,
  `url` text NOT NULL,
  `position` int(11) NOT NULL default 0,
  `caption` text NOT NULL default '',
  `type` enum('Thumb', 'Link') NOT NULL,
  PRIMARY KEY (`bibid`, `imgurl`(128))
);
