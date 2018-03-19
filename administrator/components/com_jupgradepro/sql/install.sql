--
-- Table structure for table `#__jupgradepro_errors`
--

DROP TABLE IF EXISTS `#__jupgradepro_errors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `#__jupgradepro_errors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `method` varchar(255) NOT NULL,
  `step` varchar(255) NOT NULL,
  `cid` int(11) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `#__jupgradepro_errors`
--

LOCK TABLES `#__jupgradepro_errors` WRITE;
/*!40000 ALTER TABLE `#__jupgradepro_errors` DISABLE KEYS */;
/*!40000 ALTER TABLE `#__jupgradepro_errors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `#__jupgradepro_extensions`
--

DROP TABLE IF EXISTS `#__jupgradepro_extensions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `#__jupgradepro_extensions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `tbl_key` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `cid` int(11) NOT NULL DEFAULT '0',
  `class` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `cache` int(11) NOT NULL,
  `xmlpath` varchar(255) NOT NULL,
  `debug` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `#__jupgradepro_extensions`
--

LOCK TABLES `#__jupgradepro_extensions` WRITE;
/*!40000 ALTER TABLE `#__jupgradepro_extensions` DISABLE KEYS */;
INSERT INTO `#__jupgradepro_extensions` VALUES (7,'1.0','extensions','Check extensions','','','',0,'JUpgradeproCheckExtensions',0,0,'',''),(8,'1.0','ext_components','Check components','id','components','extensions',0,'JUpgradeproExtensionsComponents',0,0,'',''),(9,'1.0','ext_modules','Check modules','id','modules','extensions',0,'JUpgradeproExtensionsModules',0,0,'',''),(10,'1.0','ext_plugins','Check plugins','id','mambots','extensions',0,'JUpgradeproExtensionsPlugins',0,0,'',''),(11,'1.5','extensions','Check extensions','','','',0,'JUpgradeproCheckExtensions',0,0,'',''),(12,'1.5','ext_components','Check components','id','components','extensions',0,'JUpgradeproExtensionsComponents',0,0,'',''),(13,'1.5','ext_modules','Check modules','id','modules','extensions',0,'JUpgradeproExtensionsModules',0,0,'',''),(14,'1.5','ext_plugins','Check plugins','id','plugins','extensions',0,'JUpgradeproExtensionsPlugins',0,0,'',''),(15,'2.5','extensions','Check extensions','','','',0,'JUpgradeproCheckExtensions',0,0,'',''),(16,'2.5','ext_components','Check components','extension_id','extensions','extensions',0,'JUpgradeproExtensionsComponents',0,0,'',''),(17,'2.5','ext_modules','Check modules','extension_id','extensions','extensions',0,'JUpgradeproExtensionsModules',0,0,'',''),(18,'2.5','ext_plugins','Check plugins','extension_id','extensions','extensions',0,'JUpgradeproExtensionsPlugins',0,0,'',''),(19,'3.1','extensions','Check extensions','','','',0,'JUpgradeproCheckExtensions',0,0,'',''),(20,'3.1','ext_components','Check components','extension_id','extensions','extensions',0,'JUpgradeproExtensionsComponents',0,0,'',''),(21,'3.1','ext_modules','Check modules','extension_id','extensions','extensions',0,'JUpgradeproExtensionsModules',0,0,'',''),(22,'3.1','ext_plugins','Check plugins','extension_id','extensions','extensions',0,'JUpgradeproExtensionsPlugins',0,0,'',''),(23,'3.2','extensions','Check extensions','','','',0,'JUpgradeproCheckExtensions',0,0,'',''),(24,'3.2','ext_components','Check components','extension_id','extensions','extensions',0,'JUpgradeproExtensionsComponents',0,0,'',''),(25,'3.2','ext_modules','Check modules','extension_id','extensions','extensions',0,'JUpgradeproExtensionsModules',0,0,'',''),(26,'3.2','ext_plugins','Check plugins','extension_id','extensions','extensions',0,'JUpgradeproExtensionsPlugins',0,0,'',''),(27,'3.3','extensions','Check extensions','','','',0,'JUpgradeproCheckExtensions',0,0,'',''),(28,'3.3','ext_components','Check components','extension_id','extensions','extensions',0,'JUpgradeproExtensionsComponents',0,0,'',''),(29,'3.3','ext_modules','Check modules','extension_id','extensions','extensions',0,'JUpgradeproExtensionsModules',0,0,'',''),(30,'3.3','ext_plugins','Check plugins','extension_id','extensions','extensions',0,'JUpgradeproExtensionsPlugins',0,0,'',''),(31,'3.4','extensions','Check extensions','','','',0,'JUpgradeproCheckExtensions',0,0,'',''),(32,'3.4','ext_components','Check components','extension_id','extensions','extensions',0,'JUpgradeproExtensionsComponents',0,0,'',''),(33,'3.4','ext_modules','Check modules','extension_id','extensions','extensions',0,'JUpgradeproExtensionsModules',0,0,'',''),(34,'3.4','ext_plugins','Check plugins','extension_id','extensions','extensions',0,'JUpgradeproExtensionsPlugins',0,0,'','');
/*!40000 ALTER TABLE `#__jupgradepro_extensions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `#__jupgradepro_extensions_tables`
--

DROP TABLE IF EXISTS `#__jupgradepro_extensions_tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `#__jupgradepro_extensions_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eid` int(11) NOT NULL,
  `version` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `element` varchar(255) NOT NULL,
  `tbl_key` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `#__jupgradepro_extensions_tables`
--

LOCK TABLES `#__jupgradepro_extensions_tables` WRITE;
/*!40000 ALTER TABLE `#__jupgradepro_extensions_tables` DISABLE KEYS */;
/*!40000 ALTER TABLE `#__jupgradepro_extensions_tables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `#__jupgradepro_files_images`
--

DROP TABLE IF EXISTS `#__jupgradepro_files_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `#__jupgradepro_files_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `#__jupgradepro_files_images`
--

LOCK TABLES `#__jupgradepro_files_images` WRITE;
/*!40000 ALTER TABLE `#__jupgradepro_files_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `#__jupgradepro_files_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `#__jupgradepro_files_media`
--

DROP TABLE IF EXISTS `#__jupgradepro_files_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `#__jupgradepro_files_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `#__jupgradepro_files_media`
--

LOCK TABLES `#__jupgradepro_files_media` WRITE;
/*!40000 ALTER TABLE `#__jupgradepro_files_media` DISABLE KEYS */;
/*!40000 ALTER TABLE `#__jupgradepro_files_media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `#__jupgradepro_files_templates`
--

DROP TABLE IF EXISTS `#__jupgradepro_files_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `#__jupgradepro_files_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `#__jupgradepro_files_templates`
--

LOCK TABLES `#__jupgradepro_files_templates` WRITE;
/*!40000 ALTER TABLE `#__jupgradepro_files_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `#__jupgradepro_files_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `#__jupgradepro_old_ids`
--

DROP TABLE IF EXISTS `#__jupgradepro_old_ids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `#__jupgradepro_old_ids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table` varchar(200) DEFAULT NULL,
  `old_id` int(11) DEFAULT NULL,
  `new_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `#__jupgradepro_old_ids`
--

LOCK TABLES `#__jupgradepro_old_ids` WRITE;
/*!40000 ALTER TABLE `#__jupgradepro_old_ids` DISABLE KEYS */;
/*!40000 ALTER TABLE `#__jupgradepro_old_ids` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `#__jupgradepro_sites`
--

DROP TABLE IF EXISTS `#__jupgradepro_sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `#__jupgradepro_steps`
--

DROP TABLE IF EXISTS `#__jupgradepro_steps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `#__jupgradepro_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` int(11) NOT NULL DEFAULT '10',
  `to` int(11) NOT NULL DEFAULT '99',
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `tbl_key` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `#__jupgradepro_steps`
--

LOCK TABLES `#__jupgradepro_steps` WRITE;
/*!40000 ALTER TABLE `#__jupgradepro_steps` DISABLE KEYS */;
INSERT INTO `#__jupgradepro_steps` VALUES (1,10,99,'users','Users','id','users','users','JUpgradeproUsers',0,0,0,0,0,0,0,0,''),(2,25,99,'usergroups','User Groups','id','usergroups','usergroups','JUpgradeproUsergroups',0,0,0,0,0,0,0,0,''),(3,25,99,'viewlevels','View Access Levels','id','viewlevels','viewlevels','JUpgradeproViewlevels',0,0,0,0,0,0,0,0,''),(4,10,15,'usergroupmap','Users Groups','aro_id','core_acl_groups_aro_map','user_usergroup_map','JUpgradeproUsergroupMap',0,0,0,0,0,0,0,0,''),(5,25,99,'usergroupmap','Users Groups','user_id','user_usergroup_map','user_usergroup_map','JUpgradeproUsergroupMap',0,0,0,0,0,0,0,0,''),(6,10,99,'categories','Categories','id','categories','categories','JUpgradeproCategories',0,0,0,0,0,0,0,0,''),(7,10,15,'sections','Sections','id','sections','categories','JUpgradeproSections',0,0,0,0,0,0,0,0,''),(8,10,99,'contents','Contents','id','content','content','JUpgradeproContent',0,0,0,0,0,0,0,0,''),(9,10,99,'contents_frontpage','FrontPage Contents','content_id','content_frontpage','content_frontpage','JUpgradeproContentFrontpage',0,0,0,0,0,0,0,0,''),(10,10,99,'menus','Menus','id','menu','menu','JUpgradeproMenu',0,0,0,0,0,0,0,0,''),(11,10,99,'menus_types','Menus Types','id','menu_types','menu_types','JUpgradeproMenusTypes',0,0,0,0,0,0,0,0,''),(12,10,99,'modules','Core Modules','id','modules','modules','JUpgradeproModules',0,0,0,0,0,0,0,0,''),(13,10,99,'modules_menu','Modules Menus','moduleid','modules_menu','modules_menu','JUpgradeproModulesMenu',0,0,0,0,0,0,0,0,''),(14,10,15,'banners','Banners','bid','banner','banners','JUpgradeproBanners',0,0,0,0,0,0,0,0,''),(15,25,99,'banners','Banners','id','banners','banners','JUpgradeproBanners',0,0,0,0,0,0,0,0,''),(16,10,15,'banners_clients','Banners Clients','cid','bannerclient','banner_clients','JUpgradeproBannersClients',0,0,0,0,0,0,0,0,''),(17,25,99,'banners_clients','Banners Clients','id','banner_clients','banner_clients','JUpgradeproBannersClients',0,0,0,0,0,0,0,0,''),(18,15,15,'banners_tracks','Banners Tracks','banner_id','bannertrack','banner_tracks','JUpgradeproBannersTracks',0,0,0,0,0,0,0,0,''),(19,25,99,'banners_tracks','Banners Tracks','banner_id','banner_tracks','banner_tracks','JUpgradeproBannersTracks',0,0,0,0,0,0,0,0,''),(20,10,99,'contacts','Contacts','id','contact_details','contact_details','JUpgradeproContacts',0,0,0,0,0,0,0,0,''),(21,10,99,'newsfeeds','NewsFeeds','id','newsfeeds','newsfeeds','JUpgradeproNewsfeeds',0,0,0,0,0,0,0,0,''),(22,10,33,'weblinks','Weblinks','id','weblinks','weblinks','JUpgradeproWeblinks',0,0,0,0,0,0,0,0,'');
/*!40000 ALTER TABLE `#__jupgradepro_steps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `#__jupgradepro_version`
--

DROP TABLE IF EXISTS `#__jupgradepro_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `#__jupgradepro_version` (
  `new` varchar(255) NOT NULL,
  `old` varchar(255) NOT NULL,
  PRIMARY KEY (`new`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `#__jupgradepro_version`
--

LOCK TABLES `#__jupgradepro_version` WRITE;
/*!40000 ALTER TABLE `#__jupgradepro_version` DISABLE KEYS */;
INSERT INTO `#__jupgradepro_version` VALUES ('0','0');
/*!40000 ALTER TABLE `#__jupgradepro_version` ENABLE KEYS */;
UNLOCK TABLES;
