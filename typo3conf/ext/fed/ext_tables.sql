#
# Table structure for table 'tx_fed_domain_model_datasource'
#
CREATE TABLE tx_fed_domain_model_datasource (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,


	name varchar(255) DEFAULT '' NOT NULL,
	description text NOT NULL,
	query varchar(255) DEFAULT '' NOT NULL,
	func varchar(255) DEFAULT '' NOT NULL,
	url varchar(255) DEFAULT '' NOT NULL,
	url_method int(11) DEFAULT '0' NOT NULL,
	template_file text NOT NULL,
	template_source text NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(30) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3_origuid int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid)
);

## KICKSTARTER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the kickstarter

#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	tx_fed_fcecontentarea varchar(255) DEFAULT '' NOT NULL,
	tx_fed_fcefile varchar(255) DEFAULT '' NOT NULL
);

#
# Table structure for table 'pages'
#
CREATE TABLE pages (
	tx_fed_page_format varchar(255) DEFAULT '' NOT NULL,
	tx_fed_page_flexform text NOT NULL,
	tx_fed_page_controller_action varchar(255) DEFAULT '' NOT NULL,
	tx_fed_page_controller_action_sub varchar(255) DEFAULT '' NOT NULL,
);