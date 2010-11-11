#
# Table structure for table 'pages'
#
CREATE TABLE pages (
	tx_metaext_alttitle varchar(120) DEFAULT '' NOT NULL,
	tx_metaext_geoposition varchar(40) DEFAULT '' NOT NULL,
	tx_metaext_georegion varchar(10) DEFAULT '' NOT NULL,
	tx_metaext_geoplacename varchar(80) DEFAULT '' NOT NULL,
	tx_metaext_publisher varchar(80) DEFAULT '' NOT NULL,
	tx_metaext_copyright varchar(80) DEFAULT '' NOT NULL,
	tx_metaext_publisher varchar(80) DEFAULT '' NOT NULL,
	tx_metaext_robots tinyint(4) NOT NULL default '0',
	tx_metaext_importance char(3) DEFAULT '0.5' NOT NULL,
);

#
# Table structure for table 'pages_language_overlay'
#
CREATE TABLE pages_language_overlay (
	tx_metaext_alttitle varchar(120) DEFAULT '' NOT NULL,
	tx_metaext_geoplacename varchar(80) DEFAULT '' NOT NULL,
	tx_metaext_copyright varchar(80) DEFAULT '' NOT NULL,
);