CREATE TABLE tx_libconnect_subject (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	dbis_id tinytext NOT NULL,
	ezb_notation tinytext NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);
