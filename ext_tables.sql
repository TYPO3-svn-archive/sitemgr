#
# Table structure for table 'be_users'
#
CREATE TABLE be_users (
	tx_kssitemgr_manager_for_be_groups text
);



#
# Table structure for table 'be_users'
#
CREATE TABLE tx_templavoila_tmplobj (
	tx_kssitemgr_manager_allowed_for_customer tinyint(4) DEFAULT '0' NOT NULL
);



#
# Table structure for table 'tx_kssitemgr_customer'
#
CREATE TABLE tx_kssitemgr_customer (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(30) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3_origuid int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext,
	main_be_user text,
	admin_be_users text,
	normal_be_users text,
	be_groups text,
	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid)
);
