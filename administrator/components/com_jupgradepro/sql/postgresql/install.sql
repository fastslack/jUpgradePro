--
-- Table structure for table "#__jupgradepro_errors"
--

DROP TABLE IF EXISTS "#__jupgradepro_errors";

CREATE TABLE "#__jupgradepro_errors" (
  "id" integer NOT NULL ,
  "method" character varying(255) NOT NULL,
  "step" character varying(255) NOT NULL,
  "cid" integer NOT NULL,
  "message" text NOT NULL,
  PRIMARY KEY ("id")
);



DROP TABLE IF EXISTS "#__jupgradepro_extensions";


CREATE TABLE "#__jupgradepro_extensions" (
  "id" integer NOT NULL ,
  "version" character varying(255) NOT NULL,
  "name" character varying(255) NOT NULL,
  "title" character varying(255) NOT NULL,
  "tbl_key" character varying(255) NOT NULL,
  "source" character varying(255) NOT NULL,
  "destination" character varying(255) NOT NULL,
  "cid" integer NOT NULL DEFAULT '0',
  "class" character varying(255) NOT NULL,
  "status" integer NOT NULL DEFAULT '0',
  "cache" integer NOT NULL,
  "xmlpath" character varying(255) NOT NULL,
  "debug" character varying(255) NOT NULL,
  PRIMARY KEY ("id")
);


--
-- Dumping data for table "#__jupgradepro_extensions"
--

INSERT INTO "#__jupgradepro_extensions" VALUES (7,'1.0','extensions','Check extensions','','','',0,'JUpgradeproCheckExtensions',0,0,'',''),(8,'1.0','ext_components','Check components','id','components','extensions',0,'JUpgradeproExtensionsComponents',0,0,'',''),(9,'1.0','ext_modules','Check modules','id','modules','extensions',0,'JUpgradeproExtensionsModules',0,0,'',''),(10,'1.0','ext_plugins','Check plugins','id','mambots','extensions',0,'JUpgradeproExtensionsPlugins',0,0,'',''),(11,'1.5','extensions','Check extensions','','','',0,'JUpgradeproCheckExtensions',0,0,'',''),(12,'1.5','ext_components','Check components','id','components','extensions',0,'JUpgradeproExtensionsComponents',0,0,'',''),(13,'1.5','ext_modules','Check modules','id','modules','extensions',0,'JUpgradeproExtensionsModules',0,0,'',''),(14,'1.5','ext_plugins','Check plugins','id','plugins','extensions',0,'JUpgradeproExtensionsPlugins',0,0,'',''),(15,'2.5','extensions','Check extensions','','','',0,'JUpgradeproCheckExtensions',0,0,'',''),(16,'2.5','ext_components','Check components','extension_id','extensions','extensions',0,'JUpgradeproExtensionsComponents',0,0,'',''),(17,'2.5','ext_modules','Check modules','extension_id','extensions','extensions',0,'JUpgradeproExtensionsModules',0,0,'',''),(18,'2.5','ext_plugins','Check plugins','extension_id','extensions','extensions',0,'JUpgradeproExtensionsPlugins',0,0,'',''),(19,'3.1','extensions','Check extensions','','','',0,'JUpgradeproCheckExtensions',0,0,'',''),(20,'3.1','ext_components','Check components','extension_id','extensions','extensions',0,'JUpgradeproExtensionsComponents',0,0,'',''),(21,'3.1','ext_modules','Check modules','extension_id','extensions','extensions',0,'JUpgradeproExtensionsModules',0,0,'',''),(22,'3.1','ext_plugins','Check plugins','extension_id','extensions','extensions',0,'JUpgradeproExtensionsPlugins',0,0,'',''),(23,'3.2','extensions','Check extensions','','','',0,'JUpgradeproCheckExtensions',0,0,'',''),(24,'3.2','ext_components','Check components','extension_id','extensions','extensions',0,'JUpgradeproExtensionsComponents',0,0,'',''),(25,'3.2','ext_modules','Check modules','extension_id','extensions','extensions',0,'JUpgradeproExtensionsModules',0,0,'',''),(26,'3.2','ext_plugins','Check plugins','extension_id','extensions','extensions',0,'JUpgradeproExtensionsPlugins',0,0,'',''),(27,'3.3','extensions','Check extensions','','','',0,'JUpgradeproCheckExtensions',0,0,'',''),(28,'3.3','ext_components','Check components','extension_id','extensions','extensions',0,'JUpgradeproExtensionsComponents',0,0,'',''),(29,'3.3','ext_modules','Check modules','extension_id','extensions','extensions',0,'JUpgradeproExtensionsModules',0,0,'',''),(30,'3.3','ext_plugins','Check plugins','extension_id','extensions','extensions',0,'JUpgradeproExtensionsPlugins',0,0,'',''),(31,'3.4','extensions','Check extensions','','','',0,'JUpgradeproCheckExtensions',0,0,'',''),(32,'3.4','ext_components','Check components','extension_id','extensions','extensions',0,'JUpgradeproExtensionsComponents',0,0,'',''),(33,'3.4','ext_modules','Check modules','extension_id','extensions','extensions',0,'JUpgradeproExtensionsModules',0,0,'',''),(34,'3.4','ext_plugins','Check plugins','extension_id','extensions','extensions',0,'JUpgradeproExtensionsPlugins',0,0,'','');

--
-- Table structure for table "#__jupgradepro_extensions_tables"
--

DROP TABLE IF EXISTS "#__jupgradepro_extensions_tables";


CREATE TABLE "#__jupgradepro_extensions_tables" (
  "id" integer NOT NULL ,
  "eid" integer NOT NULL,
  "version" character varying(255) NOT NULL,
  "name" character varying(255) NOT NULL,
  "element" character varying(255) NOT NULL,
  "tbl_key" character varying(255) NOT NULL,
  "source" character varying(255) NOT NULL,
  "destination" character varying(255) NOT NULL,
  "class" character varying(255) NOT NULL,
  "cid" integer NOT NULL DEFAULT '0',
  "status" integer NOT NULL DEFAULT '0',
  "cache" integer NOT NULL,
  "total" integer NOT NULL,
  "start" integer NOT NULL,
  "stop" integer NOT NULL,
  "replace" character varying(255) NOT NULL,
  "first" smallint NOT NULL,
  "debug" character varying(255) NOT NULL,
  PRIMARY KEY ("id")
);


--
-- Dumping data for table "#__jupgradepro_extensions_tables"
--

--
-- Table structure for table "#__jupgradepro_files_images"
--

DROP TABLE IF EXISTS "#__jupgradepro_files_images";


CREATE TABLE "#__jupgradepro_files_images" (
  "id" integer NOT NULL ,
  "name" character varying(255) NOT NULL,
  PRIMARY KEY ("id")
);


--
-- Dumping data for table "#__jupgradepro_files_images"
--


--
-- Table structure for table "#__jupgradepro_files_media"
--

DROP TABLE IF EXISTS "#__jupgradepro_files_media";


CREATE TABLE "#__jupgradepro_files_media" (
  "id" integer NOT NULL ,
  "name" character varying(255) NOT NULL,
  PRIMARY KEY ("id")
);


--
-- Dumping data for table "#__jupgradepro_files_media"
--

--
-- Table structure for table "#__jupgradepro_files_templates"
--

DROP TABLE IF EXISTS "#__jupgradepro_files_templates";


CREATE TABLE "#__jupgradepro_files_templates" (
  "id" integer NOT NULL ,
  "name" character varying(255) NOT NULL,
  PRIMARY KEY ("id")
);


--
-- Dumping data for table "#__jupgradepro_files_templates"
--

--
-- Table structure for table "#__jupgradepro_old_ids"
--

DROP TABLE IF EXISTS "#__jupgradepro_old_ids";


CREATE TABLE "#__jupgradepro_old_ids" (
  "id" integer NOT NULL ,
  "table" varchar(200) DEFAULT NULL,
  "old_id" integer DEFAULT NULL,
  "new_id" integer DEFAULT NULL,
  "section" varchar(45) DEFAULT NULL,
  PRIMARY KEY ("id")
);


--
-- Dumping data for table "#__jupgradepro_old_ids"
--

--
-- Table structure for table "#__jupgradepro_sites"
--

DROP TABLE IF EXISTS "#__jupgradepro_sites";


CREATE TABLE "#__jupgradepro_sites" (
  "id" integer NOT NULL ,
  "name" varchar(45) DEFAULT NULL,
  "restful" json DEFAULT NULL,
  "database" json DEFAULT NULL,
  "skips" json DEFAULT NULL,
  "created_by" integer DEFAULT NULL,
  "created" timestamp without time zone DEFAULT '1970-01-01 00:00:00'::timestamp without time zone NULL,
  "modified_by" integer DEFAULT NULL,
  "checked_out" integer DEFAULT NULL,
  "state" integer DEFAULT NULL,
  "ordering" integer DEFAULT NULL,
  "checked_out_time" timestamp without time zone DEFAULT '1970-01-01 00:00:00'::timestamp without time zone NULL,
  "method" varchar(45) DEFAULT NULL,
  "chunk_limit" integer DEFAULT NULL,
  "keep_ids" integer DEFAULT NULL,
  PRIMARY KEY ("id")
);


--
-- Dumping data for table "#__jupgradepro_sites"
--

INSERT INTO "#__jupgradepro_sites" VALUES (17,'dywos','{"rest_key": "", "rest_hostname": "http://www.example.org/", "rest_password": "", "rest_username": ""}','{"db_name": "dywos", "db_type": "mysql", "db_prefix": "bzjo9_", "db_hostname": "localhost", "db_password": "c4XZh3sKAywscwSs", "db_username": "dywos"}','{"skip_core_menus": "0", "skip_core_users": "0", "skip_core_banners": "0", "skip_core_modules": "0", "skip_core_contacts": "0", "skip_core_contents": "0", "skip_core_weblinks": "0", "skip_core_newsfeeds": "0", "skip_core_categories": "0", "skip_core_menus_types": "0", "skip_core_modules_menu": "0", "skip_core_banners_tracks": "0", "skip_core_banners_clients": "0", "skip_core_contents_frontpage": "0"}',NULL,NULL,403,NULL,NULL,1,NULL,'database',100,0),(18,'dywos_rest','{"rest_key": "beer", "rest_hostname": "http://dev2.dywos.com/", "rest_password": "preventer12", "rest_username": "fastslack"}','{"db_name": "", "db_type": "mysql", "db_prefix": "jos_", "db_hostname": "localhost", "db_password": "", "db_username": ""}','{"skip_core_menus": "0", "skip_core_users": "0", "skip_core_banners": "0", "skip_core_modules": "0", "skip_core_contacts": "0", "skip_core_contents": "0", "skip_core_weblinks": "0", "skip_core_newsfeeds": "0", "skip_core_categories": "0", "skip_core_menus_types": "0", "skip_core_modules_menu": "0", "skip_core_banners_tracks": "0", "skip_core_banners_clients": "0", "skip_core_contents_frontpage": "0"}',NULL,NULL,403,NULL,NULL,2,NULL,'restful',50,0),(19,'joomla33','{"rest_key": "", "rest_hostname": "http://www.example.org/", "rest_password": "", "rest_username": ""}','{"db_name": "joomla33", "db_type": "mysql", "db_prefix": "dzsqy_", "db_hostname": "localhost", "db_password": "joomla3312", "db_username": "joomla33"}','{"skip_core_menus": "0", "skip_core_users": "0", "skip_core_banners": "0", "skip_core_modules": "0", "skip_core_contacts": "0", "skip_core_contents": "0", "skip_core_weblinks": "0", "skip_core_newsfeeds": "0", "skip_core_categories": "0", "skip_core_menus_types": "0", "skip_core_modules_menu": "0", "skip_core_banners_tracks": "0", "skip_core_banners_clients": "0", "skip_core_contents_frontpage": "0"}',NULL,NULL,403,NULL,NULL,3,NULL,'database',100,0),(20,'joomla15','{"rest_key": "", "rest_hostname": "http://www.example.org/", "rest_password": "", "rest_username": ""}','{"db_name": "jupgradepro_temp", "db_type": "mysql", "db_prefix": "jos_", "db_hostname": "localhost", "db_password": "jupgradepro_temp12", "db_username": "jupgradepro_temp"}','{"skip_core_menus": "0", "skip_core_users": "0", "skip_core_banners": "0", "skip_core_modules": "0", "skip_core_contacts": "0", "skip_core_contents": "1", "skip_core_weblinks": "0", "skip_core_newsfeeds": "0", "skip_core_categories": "0", "skip_core_menus_types": "0", "skip_core_modules_menu": "0", "skip_core_banners_tracks": "0", "skip_core_banners_clients": "0", "skip_core_contents_frontpage": "0"}',NULL,NULL,403,2147483647,NULL,4,'2018-03-05 17:02:25','database',100,0);

--
-- Table structure for table "#__jupgradepro_steps"
--

DROP TABLE IF EXISTS "#__jupgradepro_steps";


CREATE TABLE "#__jupgradepro_steps" (
  "id" integer NOT NULL ,
  "from" integer NOT NULL DEFAULT '10',
  "to" integer NOT NULL DEFAULT '99',
  "name" character varying(255) NOT NULL,
  "title" character varying(255) NOT NULL,
  "tbl_key" character varying(255) NOT NULL,
  "source" character varying(255) NOT NULL,
  "destination" character varying(255) NOT NULL,
  "class" character varying(255) NOT NULL,
  "cid" integer NOT NULL DEFAULT '0',
  "status" integer NOT NULL,
  "cache" integer NOT NULL,
  "total" integer NOT NULL,
  "start" integer NOT NULL,
  "stop" integer NOT NULL,
  "first" smallint NOT NULL,
  "extension" integer NOT NULL DEFAULT '0',
  "debug" character varying(255) NOT NULL,
  PRIMARY KEY ("id")
);


--
-- Dumping data for table "#__jupgradepro_steps"
--

INSERT INTO "#__jupgradepro_steps" VALUES (1,10,99,'users','Users','id','users','users','JUpgradeproUsers',0,0,0,0,0,0,0,0,''),(2,25,99,'usergroups','User Groups','id','usergroups','usergroups','JUpgradeproUsergroups',0,0,0,0,0,0,0,0,''),(3,25,99,'viewlevels','View Access Levels','id','viewlevels','viewlevels','JUpgradeproViewlevels',0,0,0,0,0,0,0,0,''),(4,10,15,'usergroupmap','Users Groups','aro_id','core_acl_groups_aro_map','user_usergroup_map','JUpgradeproUsergroupMap',0,0,0,0,0,0,0,0,''),(5,25,99,'usergroupmap','Users Groups','user_id','user_usergroup_map','user_usergroup_map','JUpgradeproUsergroupMap',0,0,0,0,0,0,0,0,''),(6,10,99,'categories','Categories','id','categories','categories','JUpgradeproCategories',0,0,0,0,0,0,0,0,''),(7,10,15,'sections','Sections','id','sections','categories','JUpgradeproSections',0,0,0,0,0,0,0,0,''),(8,10,99,'contents','Contents','id','content','content','JUpgradeproContent',0,0,0,0,0,0,0,0,''),(9,10,99,'contents_frontpage','FrontPage Contents','content_id','content_frontpage','content_frontpage','JUpgradeproContentFrontpage',0,0,0,0,0,0,0,0,''),(10,10,99,'menus','Menus','id','menu','menu','JUpgradeproMenu',0,0,0,0,0,0,0,0,''),(11,10,99,'menus_types','Menus Types','id','menu_types','menu_types','JUpgradeproMenusTypes',0,0,0,0,0,0,0,0,''),(12,10,99,'modules','Core Modules','id','modules','modules','JUpgradeproModules',0,0,0,0,0,0,0,0,''),(13,10,99,'modules_menu','Modules Menus','moduleid','modules_menu','modules_menu','JUpgradeproModulesMenu',0,0,0,0,0,0,0,0,''),(14,10,15,'banners','Banners','bid','banner','banners','JUpgradeproBanners',0,0,0,0,0,0,0,0,''),(15,25,99,'banners','Banners','id','banners','banners','JUpgradeproBanners',0,0,0,0,0,0,0,0,''),(16,10,15,'banners_clients','Banners Clients','cid','bannerclient','banner_clients','JUpgradeproBannersClients',0,0,0,0,0,0,0,0,''),(17,25,99,'banners_clients','Banners Clients','id','banner_clients','banner_clients','JUpgradeproBannersClients',0,0,0,0,0,0,0,0,''),(18,15,15,'banners_tracks','Banners Tracks','banner_id','bannertrack','banner_tracks','JUpgradeproBannersTracks',0,0,0,0,0,0,0,0,''),(19,25,99,'banners_tracks','Banners Tracks','banner_id','banner_tracks','banner_tracks','JUpgradeproBannersTracks',0,0,0,0,0,0,0,0,''),(20,10,99,'contacts','Contacts','id','contact_details','contact_details','JUpgradeproContacts',0,0,0,0,0,0,0,0,''),(21,10,99,'newsfeeds','NewsFeeds','id','newsfeeds','newsfeeds','JUpgradeproNewsfeeds',0,0,0,0,0,0,0,0,''),(22,10,33,'weblinks','Weblinks','id','weblinks','weblinks','JUpgradeproWeblinks',0,0,0,0,0,0,0,0,'');

--
-- Table structure for table "#__jupgradepro_version"
--

DROP TABLE IF EXISTS "#__jupgradepro_version";


CREATE TABLE "#__jupgradepro_version" (
  "new" character varying(255) NOT NULL,
  "old" character varying(255) NOT NULL,
  PRIMARY KEY ("new")
);


--
-- Dumping data for table "#__jupgradepro_version"
--

INSERT INTO "#__jupgradepro_version" VALUES ('0','0');
