create table view_fields (
	vfid integer auto_increment not null,
	page varchar(32) not null,
	position tinyint not null,
	tag char(3) not null,
	tag_id tinyint null,
	subfield char(1) null,
	subfield_id tinyint null,
	required char(1) not null default 'N',
	auto_repeat enum('No','Tag','Subfield') not null default 'No',
	label varchar(128) null,
	form_type enum('text','textarea') not null default 'text',
	primary key(vfid)
	)
;
