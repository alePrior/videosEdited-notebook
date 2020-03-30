CREATE TABLE users (
	`id_user` INT(3) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(20),
	`type_id` INT(2),
	`mail` VARCHAR(50),
	`password` VARCHAR(255),
  `active` TINYINT (1) NOT NULL,
  `inserted` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_user`)
);

CREATE TABLE users_type (
	`id_type` INT(2) NOT NULL AUTO_INCREMENT,
    `type` VARCHAR(10),
    PRIMARY KEY (`id_type`)
);

CREATE TABLE `languages` (
  `id_language` int(5) NOT NULL AUTO_INCREMENT,
  `language` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_language`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `markets` (
  `id_market` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `language_id` int(11) NOT NULL,
  PRIMARY KEY (`id_market`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `notes` (
  `id_note` int(11) NOT NULL AUTO_INCREMENT,
  `note` varchar(255) DEFAULT NULL,
  `inserted` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_note`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `videos` (
  `id_video` int(11) NOT NULL AUTO_INCREMENT,
  `videoID` int(5) DEFAULT NULL,
  `comment_general` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_video`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `videos_edited` (
  `market_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `loaded` smallint(1) DEFAULT NULL,
  `inserted` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`market_id`,`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;