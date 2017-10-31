SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
CREATE TABLE IF NOT EXISTS %prfx%.`booking_member` (
  `bookingid` bigint(20) NOT NULL,
  `mbrid` int(11) NOT NULL,
  PRIMARY KEY (`bookingid`,`mbrid`),
  KEY `mbrid_idx` (`mbrid`)
)  DEFAULT CHARSET=utf8;
