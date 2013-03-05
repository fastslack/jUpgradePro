--
-- Table structure for table `jupgrade_categories`
--

DROP TABLE IF EXISTS `jupgrade_categories`;
CREATE TABLE IF NOT EXISTS `jupgrade_categories` (
  `old` int(11) NOT NULL,
  `new` int(11) NOT NULL,
  `section` varchar(255) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `jupgrade_menus`
--

DROP TABLE IF EXISTS `jupgrade_menus`;
CREATE TABLE IF NOT EXISTS `jupgrade_menus` (
  `old` int(11) NOT NULL,
  `new` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `jupgrade_menus`
--

INSERT INTO `jupgrade_menus` VALUES(0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `jupgrade_steps`
--

DROP TABLE IF EXISTS `jupgrade_steps`;
CREATE TABLE IF NOT EXISTS `jupgrade_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `cid` int(11) NOT NULL DEFAULT '0',
  `class` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `cache` int(11) NOT NULL,
  `extension` int(1) NOT NULL DEFAULT '0',
  `total` int(11) NOT NULL,
  `start` int(11) NOT NULL,
  `stop` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `jupgrade_steps`
--

INSERT INTO `jupgrade_steps` (`id`, `name`, `title`, `cid`, `class`, `status`, `cache`, `extension`, `total`, `start`, `stop`) VALUES
(1, 'users', 'Users', 0, 'jUpgradeUsers', 0, 0, 0, 0, 0, 0),
(2, 'arogroup', 'Users Groups', 0, 'jUpgradeUsergroups', 0, 0, 0, 0, 0, 0),
(3, 'usergroupmap', 'Users Groups', 0, 'jUpgradeUsergroupMap', 0, 0, 0, 0, 0, 0),
(4, 'categories', 'Categories', 0, 'jUpgradeCategories', 0, 0, 0, 0, 0, 0),
(5, 'sections', 'Sections', 0, 'jUpgradeSections', 0, 0, 0, 0, 0, 0),
(6, 'contents', 'Contents', 0, 'jUpgradeContent', 0, 0, 0, 0, 0, 0),
(7, 'contents_frontpage', 'FrontPage Contents', 0, 'jUpgradeContentFrontpage', 0, 0, 0, 0, 0, 0),
(8, 'menus', 'Menus', 0, 'jUpgradeMenu', 0, 0, 0, 0, 0, 0),
(9, 'menus_types', 'Menus Types', 0, 'jUpgradeMenusTypes', 0, 0, 0, 0, 0, 0),
(10, 'modules', 'Core Modules', 0, 'jUpgradeModules', 0, 0, 0, 0, 0, 0),
(11, 'modules_menu', 'Modules Menus', 0, 'jUpgradeModulesMenu', 0, 0, 0, 0, 0, 0),
(12, 'banners', 'Banners', 0, 'jUpgradeBanners', 0, 0, 0, 0, 0, 0),
(13, 'banners_clients', 'Banners Clients', 0, 'jUpgradeBannersClients', 0, 0, 0, 0, 0, 0),
(14, 'banners_tracks', 'Banners Tracks', 0, 'jUpgradeBannersTracks', 0, 0, 0, 0, 0, 0),
(15, 'contacts', 'Contacts', 0, 'jUpgradeContacts', 0, 0, 0, 0, 0, 0),
(16, 'newsfeeds', 'NewsFeeds', 0, 'jUpgradeNewsfeeds', 0, 0, 0, 0, 0, 0),
(17, 'weblinks', 'Weblinks', 0, 'jUpgradeWeblinks', 0, 0, 0, 0, 0, 0);

--
-- Table structure for table `jupgrade_modules`
--

DROP TABLE IF EXISTS `jupgrade_modules`;
CREATE TABLE IF NOT EXISTS `jupgrade_modules` (
  `old` int(11) NOT NULL,
  `new` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `jupgrade_errors`
--

DROP TABLE IF EXISTS `jupgrade_errors`;
CREATE TABLE IF NOT EXISTS `jupgrade_errors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `method` varchar(255) NOT NULL,
  `step` varchar(255) NOT NULL,
  `cid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Table structure for table `jupgrade_extensions`
--

DROP TABLE IF EXISTS `jupgrade_extensions`;
CREATE TABLE IF NOT EXISTS `jupgrade_extensions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `cid` int(11) NOT NULL DEFAULT '0',
  `class` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `cache` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `jupgrade_extensions`
--

INSERT INTO `jupgrade_extensions` (`id`, `name`, `title`, `cid`, `class`, `status`, `cache`) VALUES
(1, 'extensions', 'Check extensions', 0, 'jUpgradeCheckExtensions', 0, 0),
(2, 'ext_components', 'Check components', 0, 'jUpgradeExtensionsComponents', 0, 0),
(3, 'ext_modules', 'Check modules', 0, 'jUpgradeExtensionsModules', 0, 0),
(4, 'ext_plugins', 'Check plugins', 0, 'jUpgradeExtensionsPlugins', 0, 0);

--
-- Table structure for table `jupgrade_extensions_tables`
--

DROP TABLE IF EXISTS `jupgrade_extensions_tables`;
CREATE TABLE IF NOT EXISTS `jupgrade_extensions_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `element` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `cid` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `cache` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `start` int(11) NOT NULL,
  `stop` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

--
-- Table structure for table `jupgrade_files_images`
--

DROP TABLE IF EXISTS `jupgrade_files_images`;
CREATE TABLE IF NOT EXISTS `jupgrade_files_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `jupgrade_files_media`
--

DROP TABLE IF EXISTS `jupgrade_files_media`;
CREATE TABLE IF NOT EXISTS `jupgrade_files_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `jupgrade_files_templates`
--

DROP TABLE IF EXISTS `jupgrade_files_templates`;
CREATE TABLE IF NOT EXISTS `jupgrade_files_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------
