--
-- Table structure for table `jupgrade_steps`
--

DROP TABLE IF EXISTS `jupgrade_steps`;
CREATE TABLE IF NOT EXISTS `jupgrade_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `cid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `jupgrade_steps`
--

INSERT INTO `jupgrade_steps` (`id`, `name`, `cid`) VALUES
(1, 'users', 0),
(2, 'arogroup', 0),
(3, 'usergroupmap', 0),
(4, 'categories', 0),
(5, 'sections', 0),
(6, 'contents', 0),
(7, 'contents_frontpage', 0),
(8, 'menus', 0),
(9, 'menus_types', 0),
(10, 'modules', 0),
(11, 'modules_menu', 0),
(12, 'banners', 0),
(13, 'banners_clients', 0),
(14, 'banners_tracks', 0),
(15, 'contacts', 0),
(16, 'newsfeeds', 0),
(17, 'weblinks', 0),
(18, 'extensions', 0);
