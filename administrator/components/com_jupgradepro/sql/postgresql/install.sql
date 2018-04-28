--
-- Table structure for table "#__jupgradepro_errors"
--

DROP TABLE IF EXISTS "#__jupgradepro_errors";

CREATE TABLE "#__jupgradepro_errors" (
  "id" serial NOT NULL ,
  "method" character varying(255) NOT NULL,
  "step" character varying(255) NOT NULL,
  "cid" integer NOT NULL,
  "message" text NOT NULL,
  PRIMARY KEY ("id")
);



DROP TABLE IF EXISTS "#__jupgradepro_extensions";

CREATE TABLE "#__jupgradepro_extensions" (
  "id" serial NOT NULL ,
  "from" integer NOT NULL DEFAULT '10',
  "to" integer NOT NULL DEFAULT '99',
  "name" character varying(255) NOT NULL,
  "title" character varying(255) NOT NULL,
  "tbl_key" character varying(255) NOT NULL DEFAULT '',
  "source" character varying(255) NOT NULL DEFAULT '',
  "destination" character varying(255) NOT NULL DEFAULT '',
  "cid" integer NOT NULL DEFAULT '0',
  "status" integer NOT NULL DEFAULT '0',
  "cache" integer NOT NULL DEFAULT '0',
  "xmlpath" character varying(255) NOT NULL,
  "debug" character varying(255) NOT NULL DEFAULT '',
  PRIMARY KEY ("id")
);

--
-- Dumping data for table "#__jupgradepro_extensions"
--

INSERT INTO "#__jupgradepro_extensions" VALUES (1,'10','15','extensions','Check extensions','','','',0,0,0,'',''),(2,'10','15','extensions_components','Check components','id','components','extensions',0,0,0,'',''),(3,'10','15','extensions_modules','Check modules','id','modules','extensions',0,0,0,'',''),(4,'10','10','extensions_plugins','Check plugins','id','mambots','extensions',0,0,0,'',''),(5,'15','15','extensions_plugins','Check plugins','id','plugins','extensions',0,0,0,'',''),(6,'25','99','extensions','Check extensions','','','',0,0,0,'',''),(7,'25','99','extensions_components','Check components','extension_id','extensions','extensions',0,0,0,'',''),(8,'25','99','extensions_modules','Check modules','extension_id','extensions','extensions',0,0,0,'',''),(9,'25','99','extensions_plugins','Check plugins','extension_id','extensions','extensions',0,0,0,'','');

SELECT setval('#__jupgradepro_extensions_id_seq', 10, false);

--
-- Table structure for table "#__jupgradepro_extensions_tables"
--

DROP TABLE IF EXISTS "#__jupgradepro_extensions_tables";

CREATE TABLE "#__jupgradepro_extensions_tables" (
  "id" serial NOT NULL ,
  "eid" integer NOT NULL,
  "from" integer NOT NULL DEFAULT '10',
  "to" integer NOT NULL DEFAULT '99',
  "name" character varying(255) NOT NULL,
  "tbl_key" character varying(255) NOT NULL,
  "element" character varying(255) NOT NULL,
  "source" character varying(255) NOT NULL,
  "destination" character varying(255) NOT NULL,
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
  "id" serial NOT NULL ,
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
  "id" serial NOT NULL ,
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
  "id" serial NOT NULL ,
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
  "id" serial NOT NULL ,
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
  "id" serial NOT NULL ,
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

SELECT setval('#__jupgradepro_sites_id_seq', 1, false);

--
-- Table structure for table "#__jupgradepro_steps"
--

DROP TABLE IF EXISTS "#__jupgradepro_steps";

CREATE TABLE "#__jupgradepro_steps" (
  "id" serial NOT NULL,
  "from" integer NOT NULL DEFAULT '10',
  "to" integer NOT NULL DEFAULT '99',
  "name" character varying(255) NOT NULL,
  "title" character varying(255) NOT NULL,
  "tbl_key" character varying(255) NOT NULL,
  "dest_tbl_key" character varying(255) NOT NULL,
  "source" character varying(255) NOT NULL,
  "destination" character varying(255) NOT NULL,
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

INSERT INTO "#__jupgradepro_steps" VALUES (1,10,99,'users','Users','id','id','users','users',0,0,0,0,0,0,0,0,''),(2,25,99,'usergroups','User Groups','id','id','usergroups','usergroups',0,0,0,0,0,0,0,0,''),(3,25,99,'viewlevels','View Access Levels','id','id','viewlevels','viewlevels',0,0,0,0,0,0,0,0,''),(4,10,15,'usergroupmap','Users Groups','aro_id','user_id','core_acl_groups_aro_map','user_usergroup_map',0,0,0,0,0,0,0,0,''),(5,25,99,'usergroupmap','Users Groups','user_id','user_id','user_usergroup_map','user_usergroup_map',0,0,0,0,0,0,0,0,''),(6,10,99,'categories','Categories','id','id','categories','categories',0,0,0,0,0,0,0,0,''),(7,10,15,'sections','Sections','id','id','sections','categories',0,0,0,0,0,0,0,0,''),(8,10,99,'contents','Contents','id','id','content','content',0,0,0,0,0,0,0,0,''),(9,10,99,'contents_frontpage','FrontPage Contents','content_id','content_id','content_frontpage','content_frontpage',0,0,0,0,0,0,0,0,''),(10,10,99,'menus','Menus','id','id','menu','menu',0,0,0,0,0,0,0,0,''),(11,10,99,'menus_types','Menus Types','id','id','menu_types','menu_types',0,0,0,0,0,0,0,0,''),(12,10,99,'modules','Core Modules','id','id','modules','modules',0,0,0,0,0,0,0,0,''),(13,10,99,'modules_menu','Modules Menus','moduleid','moduleid','modules_menu','modules_menu',0,0,0,0,0,0,0,0,''),(14,10,15,'banners','Banners','bid','id','banner','banners',0,0,0,0,0,0,0,0,''),(15,25,99,'banners','Banners','id','id','banners','banners',0,0,0,0,0,0,0,0,''),(16,10,15,'banners_clients','Banners Clients','cid','cid','bannerclient','banner_clients',0,0,0,0,0,0,0,0,''),(17,25,99,'banners_clients','Banners Clients','id','id','banner_clients','banner_clients',0,0,0,0,0,0,0,0,''),(18,15,15,'banners_tracks','Banners Tracks','banner_id','banner_id','bannertrack','banner_tracks',0,0,0,0,0,0,0,0,''),(19,25,99,'banners_tracks','Banners Tracks','banner_id','banner_id','banner_tracks','banner_tracks',0,0,0,0,0,0,0,0,''),(20,10,99,'contacts','Contacts','id','id','contact_details','contact_details',0,0,0,0,0,0,0,0,''),(21,10,99,'newsfeeds','NewsFeeds','id','id','newsfeeds','newsfeeds',0,0,0,0,0,0,0,0,''),(22,10,33,'weblinks','Weblinks','id','id','weblinks','weblinks',0,0,0,0,0,0,0,0,'');


SELECT setval('#__jupgradepro_steps_id_seq', 23, false);

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
