#
# Table structure for table 'tx_kestats_statdata'
#
CREATE TABLE tx_kestats_statdata (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	type tinytext NOT NULL,
	category tinytext NOT NULL,
	element_uid int(11) DEFAULT '0' NOT NULL,
	element_pid int(11) DEFAULT '0' NOT NULL,
	element_title tinytext NOT NULL,
	element_language int(11) DEFAULT '0' NOT NULL,
	element_type int(11) DEFAULT '0' NOT NULL,
	counter int(11) DEFAULT '0' NOT NULL,
	year int(11) DEFAULT '0' NOT NULL,
	month int(11) DEFAULT '0' NOT NULL,
	parent_uid int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY index1 (type(20),category(20),element_title(20),year,month),
	KEY index2 (type(20),category(20),element_uid,element_language,element_type,year,month),
	KEY index3 (element_title(20)),
	KEY index4 (type(20),category(20),element_uid,element_pid,element_language,element_type,year,month),
	KEY index5 (type(20),tstamp)
);

#
# Table structure for table 'tx_kestats_cache'
#
CREATE TABLE tx_kestats_cache (
	uid int(11) NOT NULL auto_increment,
	whereclause text NOT NULL,
	orderby text NOT NULL,
	groupby text NOT NULL,
	result longtext NOT NULL,

	PRIMARY KEY (uid),
	KEY whereclause_index (whereclause(260),orderby(20),groupby(20))
);

#
# Table structure for table 'tx_kestats_queue'
#
CREATE TABLE tx_kestats_queue (
	uid int(11) NOT NULL auto_increment,
	tstamp int(11) DEFAULT '0' NOT NULL,
	data text NOT NULL,
	generaldata text NOT NULL,

	PRIMARY KEY (uid)
);
