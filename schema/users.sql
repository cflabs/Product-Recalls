CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `frequency` int(11) NOT NULL DEFAULT '1',
  `date_last_sent` datetime DEFAULT NULL,
  `live` tinyint(1) NOT NULL DEFAULT '0',
  `category` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1
