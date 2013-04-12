-- --------------------------------------------------------

--
-- Table structure for table `jupgrade_categories`
--

DROP TABLE IF EXISTS `jupgrade_categories`;
CREATE TABLE IF NOT EXISTS `jupgrade_categories` (
  `old` int(11) NOT NULL,
  `new` int(11) NOT NULL,
  `section` varchar(255) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

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

-- --------------------------------------------------------

--
-- Table structure for table `jupgrade_extensions`
--

DROP TABLE IF EXISTS `jupgrade_extensions`;
CREATE TABLE IF NOT EXISTS `jupgrade_extensions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `jupgrade_extensions`
--

INSERT INTO `jupgrade_extensions` (`id`, `name`, `title`, `tbl_key`, `source`, `destination`, `cid`, `class`, `status`, `cache`, `xmlpath`) VALUES
(1, 'extensions', 'Check extensions', '', '', '', 0, 'jUpgradeCheckExtensions', 0, 0, ''),
(2, 'ext_components', 'Check components', 'id', 'components', 'extensions', 0, 'jUpgradeExtensionsComponents', 0, 0, ''),
(3, 'ext_modules', 'Check modules', 'id', 'modules', 'extensions', 0, 'jUpgradeExtensionsModules', 0, 0, ''),
(4, 'ext_plugins', 'Check plugins', 'id', 'plugins', 'extensions', 0, 'jUpgradeExtensionsPlugins', 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `jupgrade_extensions_tables`
--

DROP TABLE IF EXISTS `jupgrade_extensions_tables`;
CREATE TABLE IF NOT EXISTS `jupgrade_extensions_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eid` int(11) NOT NULL,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `jupgrade_files_images`
--

DROP TABLE IF EXISTS `jupgrade_files_images`;
CREATE TABLE IF NOT EXISTS `jupgrade_files_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `jupgrade_files_media`
--

DROP TABLE IF EXISTS `jupgrade_files_media`;
CREATE TABLE IF NOT EXISTS `jupgrade_files_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

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

INSERT INTO `jupgrade_menus` (`old`, `new`) VALUES
(0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `jupgrade_modules`
--

DROP TABLE IF EXISTS `jupgrade_modules`;
CREATE TABLE IF NOT EXISTS `jupgrade_modules` (
  `old` int(11) NOT NULL,
  `new` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `jupgrade_steps`
--

DROP TABLE IF EXISTS `jupgrade_steps`;
CREATE TABLE IF NOT EXISTS `jupgrade_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `tbl_key` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `cid` int(11) NOT NULL DEFAULT '0',
  `class` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `cache` int(11) NOT NULL,
  `extension` int(1) NOT NULL DEFAULT '0',
  `total` int(11) NOT NULL,
  `start` int(11) NOT NULL,
  `stop` int(11) NOT NULL,
  `first` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `jupgrade_steps`
--

INSERT INTO `jupgrade_steps` (`id`, `name`, `title`, `tbl_key`, `source`, `destination`, `cid`, `class`, `status`, `cache`, `extension`, `total`, `start`, `stop`, `first`) VALUES
(1, 'users', 'Users', 'id', 'users', 'users', 0, 'jUpgradeUsers', 0, 0, 0, 0, 0, 0, 0),
(2, 'arogroup', 'Users Groups', 'id', 'core_acl_aro_groups', 'usergroups', 0, 'jUpgradeUsergroups', 0, 0, 0, 0, 0, 0, 0),
(3, 'usergroupmap', 'Users Groups', 'aro_id', 'core_acl_groups_aro_map', 'user_usergroup_map', 0, 'jUpgradeUsergroupMap', 0, 0, 0, 0, 0, 0, 0),
(4, 'categories', 'Categories', 'id', 'categories', 'categories', 0, 'jUpgradeCategories', 0, 0, 0, 0, 0, 0, 0),
(5, 'sections', 'Sections', 'id', 'sections', 'categories', 0, 'jUpgradeSections', 0, 0, 0, 0, 0, 0, 0),
(6, 'contents', 'Contents', 'id', 'content', 'content', 0, 'jUpgradeContent', 0, 0, 0, 0, 0, 0, 0),
(7, 'contents_frontpage', 'FrontPage Contents', 'content_id', 'content_frontpage', 'content_frontpage', 0, 'jUpgradeContentFrontpage', 0, 0, 0, 0, 0, 0, 0),
(8, 'menus', 'Menus', 'id', 'menu', 'menu', 0, 'jUpgradeMenu', 0, 0, 0, 0, 0, 0, 0),
(9, 'menus_types', 'Menus Types', 'id', 'menu_types', 'menu_types', 0, 'jUpgradeMenusTypes', 0, 0, 0, 0, 0, 0, 0),
(10, 'modules', 'Core Modules', 'id', 'modules', 'modules', 0, 'jUpgradeModules', 0, 0, 0, 0, 0, 0, 0),
(11, 'modules_menu', 'Modules Menus', 'moduleid', 'modules_menu', 'modules_menu', 0, 'jUpgradeModulesMenu', 0, 0, 0, 0, 0, 0, 0),
(12, 'banners', 'Banners', 'id', 'banner', 'banners', 0, 'jUpgradeBanners', 0, 0, 0, 0, 0, 0, 0),
(13, 'banners_clients', 'Banners Clients', 'cid', 'bannerclient', 'banner_clients', 0, 'jUpgradeBannersClients', 0, 0, 0, 0, 0, 0, 0),
(14, 'banners_tracks', 'Banners Tracks', 'banner_id', 'bannertrack', 'bannes_tracks', 0, 'jUpgradeBannersTracks', 0, 0, 0, 0, 0, 0, 0),
(15, 'contacts', 'Contacts', 'id', 'contact_details', 'contact_details', 0, 'jUpgradeContacts', 0, 0, 0, 0, 0, 0, 0),
(16, 'newsfeeds', 'NewsFeeds', 'id', 'newsfeeds', 'newsfeeds', 0, 'jUpgradeNewsfeeds', 0, 0, 0, 0, 0, 0, 0),
(17, 'weblinks', 'Weblinks', 'id', 'weblinks', 'weblinks', 0, 'jUpgradeWeblinks', 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `jupgrade_menus_default`
--

DROP TABLE IF EXISTS `jupgrade_menus_default`;
CREATE TABLE IF NOT EXISTS `jupgrade_menus_default` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `component_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
