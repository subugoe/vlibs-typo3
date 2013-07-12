
CREATE TABLE static_countries (
  cn_short_###LANG_ISO_LOWER### varchar(50) DEFAULT '' NOT NULL
);

CREATE TABLE static_currencies (
  cu_name_###LANG_ISO_LOWER### varchar(50) DEFAULT '' NOT NULL,
  cu_sub_name_###LANG_ISO_LOWER### varchar(20) DEFAULT '' NOT NULL
);

CREATE TABLE static_languages (
  lg_name_###LANG_ISO_LOWER### varchar(50) DEFAULT '' NOT NULL
);

CREATE TABLE static_territories (
  tr_name_###LANG_ISO_LOWER### varchar(50) DEFAULT '' NOT NULL
);

CREATE TABLE static_country_zones (
  zn_name_###LANG_ISO_LOWER### varchar(50) DEFAULT '' NOT NULL
);

