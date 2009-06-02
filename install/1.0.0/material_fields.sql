-- Material Fields SQL
-- 
--

DROP TABLE IF EXISTS `material_fields`;
CREATE TABLE `material_fields` (
  `material_field_id` int(4) NOT NULL auto_increment,
  `material_cd` int(11) default NULL,
  `tag` char(3) NOT NULL,
  `subfield_cd` varchar(10) default NULL,
  `position` tinyint(4) NOT NULL,
  `label` varchar(128) default NULL,
  `form_type` enum('text','textarea') NOT NULL default 'text',
  `required` tinyint(1) NOT NULL,
  `repeatable` tinyint(1) default NULL,
  `search_results` varchar(255) default NULL,
  PRIMARY KEY  (`material_field_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `material_fields`
--


/*!40000 ALTER TABLE `material_fields` DISABLE KEYS */;
LOCK TABLES `material_fields` WRITE;
INSERT INTO `material_fields` VALUES (1,1,'245','a',1,'Title','text',0,0,NULL),(2,1,'245','b',2,'Subtitle','text',0,0,NULL),(3,1,'099','a',3,'Call Number','text',0,0,NULL),(4,1,'245','c',4,'Statement of Responsibility','text',0,0,NULL),(5,1,'521','a',5,'Audience Level','text',0,0,NULL),(7,1,'650','a',7,'Subject','text',0,0,NULL),(8,1,'250','a',11,NULL,'text',0,0,NULL),(9,1,'020','a',12,'ISBN','text',0,0,NULL),(10,1,'010','a',1,'LCCN','text',0,0,''),(11,1,'260','a',18,NULL,'text',0,0,NULL),(12,1,'260','b',19,NULL,'text',0,0,NULL),(13,1,'260','c',20,NULL,'text',0,0,NULL),(23,1,'650','C',123,'Test Entry','',0,0,'0'),(15,1,'300','a',22,NULL,'text',0,0,NULL),(16,1,'300','b',23,NULL,'text',0,0,NULL),(17,1,'300','c',24,NULL,'text',0,0,NULL),(18,1,'300','e',25,NULL,'text',0,0,NULL),(19,1,'309','a',26,'Number of Pieces','text',0,0,NULL),(20,1,'505','a',27,'Contents','textarea',0,0,NULL);
UNLOCK TABLES;
/*!40000 ALTER TABLE `material_fields` ENABLE KEYS */;

--
-- Table structure for table `material_type_dm`
--

DROP TABLE IF EXISTS `material_type_dm`;
CREATE TABLE `material_type_dm` (
  `code` smallint(6) NOT NULL auto_increment,
  `description` varchar(40) NOT NULL,
  `default_flg` char(1) NOT NULL,
  `adult_checkout_limit` tinyint(3) unsigned NOT NULL,
  `juvenile_checkout_limit` tinyint(3) unsigned NOT NULL,
  `image_file` varchar(128) default NULL,
  PRIMARY KEY  (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `material_type_dm`
--


/*!40000 ALTER TABLE `material_type_dm` DISABLE KEYS */;
LOCK TABLES `material_type_dm` WRITE;
INSERT INTO `material_type_dm` VALUES (1,'audio tapes','N',10,5,'tape.gif'),(2,'book','Y',20,10,'book.gif'),(3,'cd audio','N',10,5,'cd.gif'),(4,'cd computer','N',5,3,'cd.gif'),(5,'equipment','N',3,0,'case.gif'),(6,'magazines','N',10,5,'mag.gif'),(7,'maps','N',5,3,'map.gif'),(8,'video/dvd','N',5,3,'camera.gif');
UNLOCK TABLES;
/*!40000 ALTER TABLE `material_type_dm` ENABLE KEYS */;

