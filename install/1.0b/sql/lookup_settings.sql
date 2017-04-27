SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`lookup_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `protocol` enum('YAZ','SRU') NOT NULL DEFAULT 'YAZ',
  `maxHits` tinyint(4) NOT NULL DEFAULT '25',
  `timeout` int(10) unsigned NOT NULL DEFAULT '20',
  `keepDashes` enum('y','n') NOT NULL DEFAULT 'n',
  `callNmbrType` enum('LoC','Dew','UDC','local') NOT NULL DEFAULT 'Dew',
  `autoDewey` enum('y','n') NOT NULL DEFAULT 'y',
  `defaultDewey` varchar(10) NOT NULL DEFAULT '813.52',
  `autoCutter` enum('y','n') NOT NULL DEFAULT 'y',
  `cutterType` enum('LoC','CS3') NOT NULL DEFAULT 'CS3',
  `cutterWord` tinyint(4) NOT NULL DEFAULT '1',
  `noiseWords` varchar(255) NOT NULL DEFAULT 'a an and for of the this those',
  `autoCollect` enum('y','n') NOT NULL DEFAULT 'y',
  `fictionName` varchar(10) NOT NULL DEFAULT 'Fiction',
  `fictionCode` tinyint(4) NOT NULL DEFAULT '1',
  `fictionLoc` varchar(255) NOT NULL DEFAULT 'PQ PR PS PT PU PV PW PX PY PZ',
  `fictionDew` varchar(255) NOT NULL DEFAULT '813 823',
  PRIMARY KEY (`id`)
)   DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
INSERT INTO %prfx%.`lookup_settings` (`id`, `protocol`, `maxHits`, `timeout`, `keepDashes`, `callNmbrType`, `autoDewey`, `defaultDewey`, `autoCutter`, `cutterType`, `cutterWord`, `noiseWords`, `autoCollect`, `fictionName`, `fictionCode`, `fictionLoc`, `fictionDew`) VALUES
(1, 'SRU', 25, 10, 'n', 'LoC', 'n', '813.52', 'y', 'LoC', 1, 'a an and for of the this those', 'y', 'Fiction', 1, 'PQ PR PS PT PU PV PW PX PY PZ', '813 823');
