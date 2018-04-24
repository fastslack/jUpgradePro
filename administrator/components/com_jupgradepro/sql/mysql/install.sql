--
-- Table structure for table `#__jupgradepro_errors`
--

DROP TABLE IF EXISTS `#__jupgradepro_errors`;
CREATE TABLE `#__jupgradepro_errors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `method` varchar(255) NOT NULL,
  `step` varchar(255) NOT NULL,
  `cid` int(11) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `#__jupgradepro_extensions`
--

DROP TABLE IF EXISTS `#__jupgradepro_extensions`;

CREATE TABLE `#__jupgradepro_extensions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` varchar(255) NOT NULL,
  `to` varchar(45) NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `tbl_key` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `cid` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `cache` int(11) NOT NULL,
  `xmlpath` varchar(255) NOT NULL,
  `debug` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__jupgradepro_extensions`
--

INSERT INTO `#__jupgradepro_extensions` VALUES (1,'10','15','extensions','Check extensions','','','',0,0,0,'',''),(2,'10','15','extensions_components','Check components','id','components','extensions',0,0,0,'',''),(3,'10','15','extensions_modules','Check modules','id','modules','extensions',0,0,0,'',''),(4,'10','10','extensions_plugins','Check plugins','id','mambots','extensions',0,0,0,'',''),(5,'15','15','extensions_plugins','Check plugins','id','plugins','extensions',0,0,0,'',''),(6,'25','99','extensions','Check extensions','','','',0,0,0,'',''),(7,'25','99','extensions_components','Check components','extension_id','extensions','extensions',0,0,0,'',''),(8,'25','99','extensions_modules','Check modules','extension_id','extensions','extensions',0,0,0,'',''),(9,'25','99','extensions_plugins','Check plugins','extension_id','extensions','extensions',0,0,0,'','');

--
-- Table structure for table `#__jupgradepro_extensions_tables`
--
DROP TABLE IF EXISTS `#__jupgradepro_extensions_tables`;
CREATE TABLE `#__jupgradepro_extensions_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eid` int(11) NOT NULL,
  `from` varchar(255) NOT NULL,
  `to` varchar(45) NOT NULL,
  `name` varchar(255) NOT NULL,
  `tbl_key` varchar(255) NOT NULL,
  `element` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `cid` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `cache` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `start` int(11) NOT NULL,
  `stop` int(11) NOT NULL,
  `replace` varchar(255) NOT NULL,
  `first` tinyint(1) NOT NULL,
  `debug` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `#__jupgradepro_files_images`
--

DROP TABLE IF EXISTS `#__jupgradepro_files_images`;

CREATE TABLE `#__jupgradepro_files_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `#__jupgradepro_files_media`
--

DROP TABLE IF EXISTS `#__jupgradepro_files_media`;
CREATE TABLE `#__jupgradepro_files_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `#__jupgradepro_files_templates`
--

DROP TABLE IF EXISTS `#__jupgradepro_files_templates`;

CREATE TABLE `#__jupgradepro_files_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `#__jupgradepro_old_ids`
--

DROP TABLE IF EXISTS `#__jupgradepro_old_ids`;

CREATE TABLE `#__jupgradepro_old_ids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table` varchar(200) DEFAULT NULL,
  `old_id` int(11) DEFAULT NULL,
  `new_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `#__jupgradepro_sites`
--

DROP TABLE IF EXISTS `#__jupgradepro_sites`;

CREATE TABLE `#__jupgradepro_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `restful` json DEFAULT NULL,
  `database` json DEFAULT NULL,
  `skips` json DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `checked_out` datetime DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `method` varchar(45) DEFAULT NULL,
  `chunk_limit` int(11) DEFAULT NULL,
  `keep_ids` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `#__jupgradepro_steps`
--

DROP TABLE IF EXISTS `#__jupgradepro_steps`;

CREATE TABLE `#__jupgradepro_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` int(11) NOT NULL DEFAULT '10',
  `to` int(11) NOT NULL DEFAULT '99',
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `tbl_key` varchar(255) NOT NULL,
  `dest_tbl_key` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `cid` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL,
  `cache` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `start` int(11) NOT NULL,
  `stop` int(11) NOT NULL,
  `first` tinyint(1) NOT NULL,
  `extension` int(1) NOT NULL DEFAULT '0',
  `debug` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__jupgradepro_steps`
--

INSERT INTO `#__jupgradepro_steps` VALUES (1,10,99,'users','Users','id','id','users','users',0,0,0,0,0,0,0,0,''),(2,25,99,'usergroups','User Groups','id','id','usergroups','usergroups',0,0,0,0,0,0,0,0,''),(3,25,99,'viewlevels','View Access Levels','id','id','viewlevels','viewlevels',0,0,0,0,0,0,0,0,''),(4,10,15,'usergroupmap','Users Groups','aro_id','user_id','core_acl_groups_aro_map','user_usergroup_map',0,0,0,0,0,0,0,0,''),(5,25,99,'usergroupmap','Users Groups','user_id','user_id','user_usergroup_map','user_usergroup_map',0,0,0,0,0,0,0,0,''),(6,10,99,'categories','Categories','id','id','categories','categories',0,0,0,0,0,0,0,0,''),(7,10,15,'sections','Sections','id','id','sections','categories',0,0,0,0,0,0,0,0,''),(8,10,99,'contents','Contents','id','id','content','content',0,0,0,0,0,0,0,0,''),(9,10,99,'contents_frontpage','FrontPage Contents','content_id','content_id','content_frontpage','content_frontpage',0,0,0,0,0,0,0,0,''),(10,10,99,'menus','Menus','id','id','menu','menu',0,0,0,0,0,0,0,0,''),(11,10,99,'menus_types','Menus Types','id','id','menu_types','menu_types',0,0,0,0,0,0,0,0,''),(12,10,99,'modules','Core Modules','id','id','modules','modules',0,0,0,0,0,0,0,0,''),(13,10,99,'modules_menu','Modules Menus','moduleid','moduleid','modules_menu','modules_menu',0,0,0,0,0,0,0,0,''),(14,10,15,'banners','Banners','bid','id','banner','banners',0,0,0,0,0,0,0,0,''),(15,25,99,'banners','Banners','id','id','banners','banners',0,0,0,0,0,0,0,0,''),(16,10,15,'banners_clients','Banners Clients','cid','cid','bannerclient','banner_clients',0,0,0,0,0,0,0,0,''),(17,25,99,'banners_clients','Banners Clients','id','id','banner_clients','banner_clients',0,0,0,0,0,0,0,0,''),(18,15,15,'banners_tracks','Banners Tracks','banner_id','banner_id','bannertrack','banner_tracks',0,0,0,0,0,0,0,0,''),(19,25,99,'banners_tracks','Banners Tracks','banner_id','banner_id','banner_tracks','banner_tracks',0,0,0,0,0,0,0,0,''),(20,10,99,'contacts','Contacts','id','id','contact_details','contact_details',0,0,0,0,0,0,0,0,''),(21,10,99,'newsfeeds','NewsFeeds','id','id','newsfeeds','newsfeeds',0,0,0,0,0,0,0,0,''),(22,10,33,'weblinks','Weblinks','id','id','weblinks','weblinks',0,0,0,0,0,0,0,0,'');

--
-- Table structure for table `#__jupgradepro_version`
--

CREATE TABLE `#__jupgradepro_version` (
  `new` varchar(255) NOT NULL,
  `old` varchar(255) NOT NULL,
  PRIMARY KEY (`new`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `#__jupgradepro_version`
--

INSERT INTO `#__jupgradepro_version` VALUES ('0','0');
