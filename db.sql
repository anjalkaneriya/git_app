CREATE TABLE `repository` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `avatar_url` text NOT NULL,
 `name` text NOT NULL,
 `stargazers_count` double NOT NULL,
 `login` text NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8mb4