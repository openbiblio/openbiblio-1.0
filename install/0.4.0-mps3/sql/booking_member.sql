CREATE TABLE booking_member (
	bookingid bigint NOT NULL,
         mbrid integer NOT NULL,
	PRIMARY KEY (bookingid, mbrid),
	INDEX mbrid_idx (mbrid)
);
