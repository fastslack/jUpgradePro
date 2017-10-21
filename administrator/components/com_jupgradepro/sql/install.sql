-- --------------------------------------------------------

--
-- Table structure for table `#__jupgradepro_categories`
--

DROP TABLE IF EXISTS `#__jupgradepro_categories`;
CREATE TABLE IF NOT EXISTS `#__jupgradepro_categories` (
  `old` int(11) NOT NULL,
  `new` int(11) NOT NULL,
  `section` varchar(255) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__jupgradepro_errors`
--

DROP TABLE IF EXISTS `#__jupgradepro_errors`;
CREATE TABLE IF NOT EXISTS `#__jupgradepro_errors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `method` varchar(255) NOT NULL,
  `step` varchar(255) NOT NULL,
  `cid` int(11) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__jupgradepro_extensions`
--

DROP TABLE IF EXISTS `#__jupgradepro_extensions`;
CREATE TABLE IF NOT EXISTS `#__jupgradepro_extensions` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `#__jupgradepro_extensions`
--

INSERT INTO `#__jupgradepro_extensions` (`id`, `version`, `name`, `title`, `tbl_key`, `source`, `destination`, `cid`, `class`, `status`, `cache`, `xmlpath`) VALUES
(NULL, '1.0', 'extensions', 'Check extensions', '', '', '', 0, 'JUpgradeproCheckExtensions', 0, 0, ''),
(NULL, '1.0', 'ext_components', 'Check components', 'id', 'components', 'extensions', 0, 'JUpgradeproExtensionsComponents', 0, 0, ''),
(NULL, '1.0', 'ext_modules', 'Check modules', 'id', 'modules', 'extensions', 0, 'JUpgradeproExtensionsModules', 0, 0, ''),
(NULL, '1.0', 'ext_plugins', 'Check plugins', 'id', 'mambots', 'extensions', 0, 'JUpgradeproExtensionsPlugins', 0, 0, ''),
(NULL, '1.5', 'extensions', 'Check extensions', '', '', '', 0, 'JUpgradeproCheckExtensions', 0, 0, ''),
(NULL, '1.5', 'ext_components', 'Check components', 'id', 'components', 'extensions', 0, 'JUpgradeproExtensionsComponents', 0, 0, ''),
(NULL, '1.5', 'ext_modules', 'Check modules', 'id', 'modules', 'extensions', 0, 'JUpgradeproExtensionsModules', 0, 0, ''),
(NULL, '1.5', 'ext_plugins', 'Check plugins', 'id', 'plugins', 'extensions', 0, 'JUpgradeproExtensionsPlugins', 0, 0, ''),
(NULL, '2.5', 'extensions', 'Check extensions', '', '', '', 0, 'JUpgradeproCheckExtensions', 0, 0, ''),
(NULL, '2.5', 'ext_components', 'Check components', 'extension_id', 'extensions', 'extensions', 0, 'JUpgradeproExtensionsComponents', 0, 0, ''),
(NULL, '2.5', 'ext_modules', 'Check modules', 'extension_id', 'extensions', 'extensions', 0, 'JUpgradeproExtensionsModules', 0, 0, ''),
(NULL, '2.5', 'ext_plugins', 'Check plugins', 'extension_id', 'extensions', 'extensions', 0, 'JUpgradeproExtensionsPlugins', 0, 0, ''),
(NULL, '3.1', 'extensions', 'Check extensions', '', '', '', 0, 'JUpgradeproCheckExtensions', 0, 0, ''),
(NULL, '3.1', 'ext_components', 'Check components', 'extension_id', 'extensions', 'extensions', 0, 'JUpgradeproExtensionsComponents', 0, 0, ''),
(NULL, '3.1', 'ext_modules', 'Check modules', 'extension_id', 'extensions', 'extensions', 0, 'JUpgradeproExtensionsModules', 0, 0, ''),
(NULL, '3.1', 'ext_plugins', 'Check plugins', 'extension_id', 'extensions', 'extensions', 0, 'JUpgradeproExtensionsPlugins', 0, 0, ''),
(NULL, '3.2', 'extensions', 'Check extensions', '', '', '', 0, 'JUpgradeproCheckExtensions', 0, 0, ''),
(NULL, '3.2', 'ext_components', 'Check components', 'extension_id', 'extensions', 'extensions', 0, 'JUpgradeproExtensionsComponents', 0, 0, ''),
(NULL, '3.2', 'ext_modules', 'Check modules', 'extension_id', 'extensions', 'extensions', 0, 'JUpgradeproExtensionsModules', 0, 0, ''),
(NULL, '3.2', 'ext_plugins', 'Check plugins', 'extension_id', 'extensions', 'extensions', 0, 'JUpgradeproExtensionsPlugins', 0, 0, ''),
(NULL, '3.3', 'extensions', 'Check extensions', '', '', '', 0, 'JUpgradeproCheckExtensions', 0, 0, ''),
(NULL, '3.3', 'ext_components', 'Check components', 'extension_id', 'extensions', 'extensions', 0, 'JUpgradeproExtensionsComponents', 0, 0, ''),
(NULL, '3.3', 'ext_modules', 'Check modules', 'extension_id', 'extensions', 'extensions', 0, 'JUpgradeproExtensionsModules', 0, 0, ''),
(NULL, '3.3', 'ext_plugins', 'Check plugins', 'extension_id', 'extensions', 'extensions', 0, 'JUpgradeproExtensionsPlugins', 0, 0, ''),
(NULL, '3.4', 'extensions', 'Check extensions', '', '', '', 0, 'JUpgradeproCheckExtensions', 0, 0, ''),
(NULL, '3.4', 'ext_components', 'Check components', 'extension_id', 'extensions', 'extensions', 0, 'JUpgradeproExtensionsComponents', 0, 0, ''),
(NULL, '3.4', 'ext_modules', 'Check modules', 'extension_id', 'extensions', 'extensions', 0, 'JUpgradeproExtensionsModules', 0, 0, ''),
(NULL, '3.4', 'ext_plugins', 'Check plugins', 'extension_id', 'extensions', 'extensions', 0, 'JUpgradeproExtensionsPlugins', 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `#__jupgradepro_extensions_tables`
--

DROP TABLE IF EXISTS `#__jupgradepro_extensions_tables`;
CREATE TABLE IF NOT EXISTS `#__jupgradepro_extensions_tables` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__jupgradepro_files_images`
--

DROP TABLE IF EXISTS `#__jupgradepro_files_images`;
CREATE TABLE IF NOT EXISTS `#__jupgradepro_files_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__jupgradepro_files_media`
--

DROP TABLE IF EXISTS `#__jupgradepro_files_media`;
CREATE TABLE IF NOT EXISTS `#__jupgradepro_files_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__jupgradepro_files_templates`
--

DROP TABLE IF EXISTS `#__jupgradepro_files_templates`;
CREATE TABLE IF NOT EXISTS `#__jupgradepro_files_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__jupgradepro_menus`
--

DROP TABLE IF EXISTS `#__jupgradepro_menus`;
CREATE TABLE IF NOT EXISTS `#__jupgradepro_menus` (
  `old` int(11) NOT NULL,
  `new` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__jupgradepro_menus`
--

INSERT INTO `#__jupgradepro_menus` (`old`, `new`) VALUES
(0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `#__jupgradepro_modules`
--

DROP TABLE IF EXISTS `#__jupgradepro_modules`;
CREATE TABLE IF NOT EXISTS `#__jupgradepro_modules` (
  `old` int(11) NOT NULL,
  `new` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__jupgradepro_steps`
--

DROP TABLE IF EXISTS `#__jupgradepro_steps`;
CREATE TABLE `#__jupgradepro_steps` (
  `id` int(11) NOT NULL,
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
  `debug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__jupgradepro_steps`
--

INSERT INTO `#__jupgradepro_steps` (`id`, `from`, `to`, `name`, `title`, `tbl_key`, `source`, `destination`, `class`, `cid`, `status`, `cache`, `total`, `start`, `stop`, `first`, `extension`, `debug`) VALUES
(1, 10, 99, 'users', 'Users', 'id', 'users', 'users', 'JUpgradeproUsers', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(2, 25, 99, 'usergroups', 'User Groups', 'id', 'usergroups', 'usergroups', 'JUpgradeproUsergroups', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(3, 25, 99, 'viewlevels', 'View Access Levels', 'id', 'viewlevels', 'viewlevels', 'JUpgradeproViewlevels', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(4, 10, 15, 'usergroupmap', 'Users Groups', 'aro_id', 'core_acl_groups_aro_map', 'user_usergroup_map', 'JUpgradeproUsergroupMap', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(5, 25, 99, 'usergroupmap', 'Users Groups', 'user_id', 'user_usergroup_map', 'user_usergroup_map', 'JUpgradeproUsergroupMap', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(6, 10, 99, 'categories', 'Categories', 'id', 'categories', 'categories', 'JUpgradeproCategories', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(7, 10, 15, 'sections', 'Sections', 'id', 'sections', 'categories', 'JUpgradeproSections', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(8, 10, 99, 'contents', 'Contents', 'id', 'content', 'content', 'JUpgradeproContent', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(9, 10, 99, 'contents_frontpage', 'FrontPage Contents', 'content_id', 'content_frontpage', 'content_frontpage', 'JUpgradeproContentFrontpage', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(10, 10, 99, 'menus', 'Menus', 'id', 'menu', 'menu', 'JUpgradeproMenu', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(11, 10, 99, 'menus_types', 'Menus Types', 'id', 'menu_types', 'menu_types', 'JUpgradeproMenusTypes', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(12, 10, 99, 'modules', 'Core Modules', 'id', 'modules', 'modules', 'JUpgradeproModules', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(13, 10, 99, 'modules_menu', 'Modules Menus', 'moduleid', 'modules_menu', 'modules_menu', 'JUpgradeproModulesMenu', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(14, 10, 15, 'banners', 'Banners', 'id', 'banner', 'banners', 'JUpgradeproBanners', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(15, 25, 99, 'banners', 'Banners', 'id', 'banners', 'banners', 'JUpgradeproBanners', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(16, 10, 15, 'banners_clients', 'Banners Clients', 'cid', 'bannerclient', 'banner_clients', 'JUpgradeproBannersClients', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(17, 25, 99, 'banners_clients', 'Banners Clients', 'id', 'banner_clients', 'banner_clients', 'JUpgradeproBannersClients', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(18, 15, 15, 'banners_tracks', 'Banners Tracks', 'banner_id', 'bannertrack', 'banner_tracks', 'JUpgradeproBannersTracks', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(19, 25, 99, 'banners_tracks', 'Banners Tracks', 'banner_id', 'banner_tracks', 'banner_tracks', 'JUpgradeproBannersTracks', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(20, 10, 99, 'contacts', 'Contacts', 'id', 'contact_details', 'contact_details', 'JUpgradeproContacts', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(21, 10, 99, 'newsfeeds', 'NewsFeeds', 'id', 'newsfeeds', 'newsfeeds', 'JUpgradeproNewsfeeds', 0, 0, 0, 0, 0, 0, 0, 0, ''),
(22, 10, 33, 'weblinks', 'Weblinks', 'id', 'weblinks', 'weblinks', 'JUpgradeproWeblinks', 0, 0, 0, 0, 0, 0, 0, 0, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `#__jupgradepro_steps`
--
ALTER TABLE `#__jupgradepro_steps`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `#__jupgradepro_steps`
--
ALTER TABLE `#__jupgradepro_steps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;COMMIT;

-- --------------------------------------------------------


-- --------------------------------------------------------

--
-- Table structure for table `#__jupgradepro_default_menus`
--

DROP TABLE IF EXISTS `#__jupgradepro_default_menus`;
CREATE TABLE IF NOT EXISTS `#__jupgradepro_default_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `root_id` int(10) unsigned NOT NULL DEFAULT '0',
  `menutype` varchar(24) NOT NULL COMMENT 'The type of menu this item belongs to. FK to #__menu_types.menutype',
  `title` varchar(255) NOT NULL COMMENT 'The display title of the menu item.',
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'The SEF alias of the menu item.',
  `note` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(1024) NOT NULL COMMENT 'The computed path of the menu item based on the alias field.',
  `link` varchar(1024) NOT NULL COMMENT 'The actually link the menu item refers to.',
  `type` varchar(16) NOT NULL COMMENT 'The type of link: Component, URL, Alias, Separator',
  `published` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'The published state of the menu link.',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '1' COMMENT 'The parent menu item in the menu tree.',
  `component_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to #__extensions.id',
  `ordering` int(11) NOT NULL DEFAULT '0' COMMENT 'The relative ordering of the menu item in the tree.',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to #__users.id',
  `checked_out_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'The time the menu item was checked out.',
  `browserNav` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'The click behaviour of the link.',
  `access` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The access level required to view the menu item.',
  `img` varchar(255) NOT NULL COMMENT 'The image of the menu item.',
  `template_style_id` int(10) unsigned NOT NULL DEFAULT '0',
  `params` text NOT NULL COMMENT 'JSON encoded data for the menu item.',
  `home` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Indicates if this menu item is the home or default page.',
  `language` char(7) NOT NULL DEFAULT '',
  `client_id` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=101 ;

--
-- Dumping data for table `#__jupgradepro_default_menus`
--

INSERT INTO `#__jupgradepro_default_menus` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `home`, `language`, `client_id`) VALUES
(1, 'menu', 'com_banners', 'Banners', '', 'Banners', 'index.php?option=com_banners', 'component', 0, 1, 4, 0, 0, '0000-00-00 00:00:00', 0, 0, 'class:banners', 0, '', 0, '*', 1),
(2, 'menu', 'com_banners', 'Banners', '', 'Banners/Banners', 'index.php?option=com_banners', 'component', 0, 2, 4, 0, 0, '0000-00-00 00:00:00', 0, 0, 'class:banners', 0, '', 0, '*', 1),
(3, 'menu', 'com_banners_categories', 'Categories', '', 'Banners/Categories', 'index.php?option=com_categories&extension=com_banners', 'component', 0, 2, 6, 0, 0, '0000-00-00 00:00:00', 0, 0, 'class:banners-cat', 0, '', 0, '*', 1),
(4, 'menu', 'com_banners_clients', 'Clients', '', 'Banners/Clients', 'index.php?option=com_banners&view=clients', 'component', 0, 2, 4, 0, 0, '0000-00-00 00:00:00', 0, 0, 'class:banners-clients', 0, '', 0, '*', 1),
(5, 'menu', 'com_banners_tracks', 'Tracks', '', 'Banners/Tracks', 'index.php?option=com_banners&view=tracks', 'component', 0, 2, 4, 0, 0, '0000-00-00 00:00:00', 0, 0, 'class:banners-tracks', 0, '', 0, '*', 1),
(6, 'menu', 'com_contact', 'Contacts', '', 'Contacts', 'index.php?option=com_contact', 'component', 0, 1, 8, 0, 0, '0000-00-00 00:00:00', 0, 0, 'class:contact', 0, '', 0, '*', 1),
(7, 'menu', 'com_contact', 'Contacts', '', 'Contacts/Contacts', 'index.php?option=com_contact', 'component', 0, 7, 8, 0, 0, '0000-00-00 00:00:00', 0, 0, 'class:contact', 0, '', 0, '*', 1),
(8, 'menu', 'com_contact_categories', 'Categories', '', 'Contacts/Categories', 'index.php?option=com_categories&extension=com_contact', 'component', 0, 7, 6, 0, 0, '0000-00-00 00:00:00', 0, 0, 'class:contact-cat', 0, '', 0, '*', 1),
(9, 'menu', 'com_messages', 'Messaging', '', 'Messaging', 'index.php?option=com_messages', 'component', 0, 1, 15, 0, 0, '0000-00-00 00:00:00', 0, 0, 'class:messages', 0, '', 0, '*', 1),
(10, 'menu', 'com_messages_add', 'New Private Message', '', 'Messaging/New Private Message', 'index.php?option=com_messages&task=message.add', 'component', 0, 10, 15, 0, 0, '0000-00-00 00:00:00', 0, 0, 'class:messages-add', 0, '', 0, '*', 1),
(11, 'menu', 'com_messages_read', 'Read Private Message', '', 'Messaging/Read Private Message', 'index.php?option=com_messages', 'component', 0, 10, 15, 0, 0, '0000-00-00 00:00:00', 0, 0, 'class:messages-read', 0, '', 0, '*', 1),
(12, 'menu', 'com_newsfeeds', 'News Feeds', '', 'News Feeds', 'index.php?option=com_newsfeeds', 'component', 0, 1, 17, 0, 0, '0000-00-00 00:00:00', 0, 0, 'class:newsfeeds', 0, '', 0, '*', 1),
(13, 'menu', 'com_newsfeeds_feeds', 'Feeds', '', 'News Feeds/Feeds', 'index.php?option=com_newsfeeds', 'component', 0, 13, 17, 0, 0, '0000-00-00 00:00:00', 0, 0, 'class:newsfeeds', 0, '', 0, '*', 1),
(14, 'menu', 'com_newsfeeds_categories', 'Categories', '', 'News Feeds/Categories', 'index.php?option=com_categories&extension=com_newsfeeds', 'component', 0, 13, 6, 0, 0, '0000-00-00 00:00:00', 0, 0, 'class:newsfeeds-cat', 0, '', 0, '*', 1),
(15, 'menu', 'com_redirect', 'Redirect', '', 'Redirect', 'index.php?option=com_redirect', 'component', 0, 1, 24, 0, 0, '0000-00-00 00:00:00', 0, 0, 'class:redirect', 0, '', 0, '*', 1),
(16, 'menu', 'com_search', 'Basic Search', '', 'Basic Search', 'index.php?option=com_search', 'component', 0, 1, 19, 0, 0, '0000-00-00 00:00:00', 0, 0, 'class:search', 0, '', 0, '*', 1),
(17, 'menu', 'com_weblinks', 'Weblinks', '', 'Weblinks', 'index.php?option=com_weblinks', 'component', 0, 1, 21, 0, 0, '0000-00-00 00:00:00', 0, 0, 'class:weblinks', 0, '', 0, '*', 1),
(18, 'menu', 'com_weblinks_links', 'Links', '', 'Weblinks/Links', 'index.php?option=com_weblinks', 'component', 0, 18, 21, 0, 0, '0000-00-00 00:00:00', 0, 0, 'class:weblinks', 0, '', 0, '*', 1),
(19, 'menu', 'com_weblinks_categories', 'Categories', '', 'Weblinks/Categories', 'index.php?option=com_categories&extension=com_weblinks', 'component', 0, 18, 6, 0, 0, '0000-00-00 00:00:00', 0, 0, 'class:weblinks-cat', 0, '', 0, '*', 1),
(20, 'menu', 'com_finder', 'Smart Search', '', 'Smart Search', 'index.php?option=com_finder', 'component', 0, 1, 27, 0, 0, '0000-00-00 00:00:00', 0, 0, 'class:finder', 0, '', 0, '*', 1),
(21, 'menu', 'com_joomlaupdate', 'Joomla! Update', '', 'Joomla! Update', 'index.php?option=com_joomlaupdate', 'component', 0, 1, 28, 0, 0, '0000-00-00 00:00:00', 0, 0, 'class:joomlaupdate', 0, '', 0, '*', 1);


-- --------------------------------------------------------

--
-- Table structure for table `#__jupgradepro_default_categories`
--

DROP TABLE IF EXISTS `#__jupgradepro_default_categories`;
CREATE TABLE IF NOT EXISTS `#__jupgradepro_default_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `root_id` int(10) unsigned NOT NULL DEFAULT '0',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `path` varchar(255) NOT NULL DEFAULT '',
  `extension` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `note` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` int(10) unsigned NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `metadesc` varchar(1024) NOT NULL COMMENT 'The meta description for the page.',
  `metakey` varchar(1024) NOT NULL COMMENT 'The meta keywords for the page.',
  `metadata` varchar(2048) NOT NULL COMMENT 'JSON encoded metadata properties.',
  `created_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL,
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

--
-- Table structure for table `#__jupgradepro_version`
--

DROP TABLE IF EXISTS `#__jupgradepro_version`;
CREATE TABLE IF NOT EXISTS `#__jupgradepro_version` (
  `new` varchar(255) NOT NULL,
  `old` varchar(255) NOT NULL,
  PRIMARY KEY (`new`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ze1f4_jupgradepro_version`
--

INSERT INTO `#__jupgradepro_version` (`new`, `old`) VALUES ('0', '0');
