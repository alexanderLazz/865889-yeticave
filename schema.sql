CREATE DATABASE IF NOT EXISTS yeticave
	DEFAULT CHARACTER SET utf8
	DEFAULT COLLATE utf8_general_ci;


USE yeticave;


DROP TABLE IF EXISTS `category`;
DROP TABLE IF EXISTS `lot`;
DROP TABLE IF EXISTS `bid`;
DROP TABLE IF EXISTS `user`;


CREATE TABLE `category` (
	`id`		INT AUTO_INCREMENT PRIMARY KEY,
	`name`		CHAR(100) NOT NULL
) ENGINE=InnoDB CHARACTER SET=utf8;
 
CREATE TABLE `lot` (
	`id`				INT AUTO_INCREMENT PRIMARY KEY,
	`creation_date`		DATETIME NOT NULL,
	`name`				CHAR(130) NOT NULL,
	`description`		VARCHAR(170) NOT NULL,
	`image_url`			VARCHAR(120) NOT NULL,
	`starting_price`	FLOAT NOT NULL,
	`closing_date`		DATETIME NOT NULL,
	`bid_step`			FLOAT NOT NULL,
	`author_id`			INT NOT NULL,
	`winner_id`			INT,
	`category_id`		INT NOT NULL
) ENGINE=InnoDB CHARACTER SET=utf8;

CREATE TABLE `bid` (
	`id`			INT AUTO_INCREMENT PRIMARY KEY,
	`date_of`		DATETIME NOT NULL,
	`sum`			FLOAT NOT NULL,
	`user_id`		INT NOT NULL,
	`lot_id`		INT NOT NULL
) ENGINE=InnoDB CHARACTER SET=utf8;

CREATE TABLE `user` (
	`id`				INT AUTO_INCREMENT PRIMARY KEY,
	`reg_date`			DATETIME NOT NULL,
	`email`				CHAR(120) NOT NULL,
	`name`				CHAR(130) NOT NULL,
	`password`			CHAR(70) NOT NULL,
	`avatar`			CHAR(120),
	`contacts`			VARCHAR(150) NOT NULL
) ENGINE=InnoDB CHARACTER SET=utf8;


CREATE UNIQUE INDEX name ON category(name);
CREATE UNIQUE INDEX name ON user(name);
CREATE UNIQUE INDEX email ON user(email);


CREATE INDEX name_cat ON category(name);
CREATE INDEX name_lot ON lot(name);


ALTER TABLE `lot`
	ADD CONSTRAINT `fk_author_id` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`)
		ON UPDATE CASCADE ON DELETE RESTRICT,
	ADD CONSTRAINT `fk_winner_id` FOREIGN KEY (`winner_id`) REFERENCES `user` (`id`)
		ON UPDATE CASCADE ON DELETE RESTRICT,
	ADD CONSTRAINT `fk_category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`)
		ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `bid`
	ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
		ON UPDATE CASCADE ON DELETE RESTRICT,
	ADD CONSTRAINT `fk_lot_id` FOREIGN KEY (`lot_id`) REFERENCES `lot` (`id`)
		ON UPDATE CASCADE ON DELETE RESTRICT;
