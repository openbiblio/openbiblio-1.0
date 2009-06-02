CREATE TABLE biblio_status_hist (
	histid bigint auto_increment NOT NULL,
	bibid integer NOT NULL,
	copyid integer NOT NULL,
	status_cd char(3) NOT NULL,
	status_begin_dt datetime NOT NULL,
	PRIMARY KEY (histid),
	INDEX copy_index (bibid,copyid)
);
