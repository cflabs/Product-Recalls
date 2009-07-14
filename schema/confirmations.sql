CREATE TABLE `confirmations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_table` varchar(100) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `user_key` varchar(100) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1
