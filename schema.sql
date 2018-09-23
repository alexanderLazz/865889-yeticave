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
	`creation_date`		DATE NOT NULL,
	`name`				CHAR(130) NOT NULL,
	`description`		VARCHAR(170) NOT NULL,
	`image_url`			VARCHAR(120) NOT NULL,
	`starting_price`	FLOAT NOT NULL,
	`closing_date`		DATE NOT NULL,
	`bid_step`			FLOAT NOT NULL,
	`rid_author`		INT NOT NULL,
	`rid_winner`		INT NOT NULL,
	`rid_category`		INT NOT NULL
) ENGINE=InnoDB CHARACTER SET=utf8;

CREATE TABLE `bid` (
	`id`			INT AUTO_INCREMENT PRIMARY KEY,
	`date_of`		DATE NOT NULL,
	`rid_user`		INT NOT NULL,
	`rid_lot`		INT NOT NULL
) ENGINE=InnoDB CHARACTER SET=utf8;

CREATE TABLE `user` (
	`id`				INT AUTO_INCREMENT PRIMARY KEY,
	`reg_date`			DATE NOT NULL,
	`email`				CHAR(120) NOT NULL,
	`name`				CHAR(130) NOT NULL,
	`password`			CHAR(70) NOT NULL,
	`avatar`			CHAR(120),
	`contacts`			VARCHAR(150) NOT NULL,
	`rid_created_lots`	INT NOT NULL,
	`rid_bids`			INT NOT NULL
) ENGINE=InnoDB CHARACTER SET=utf8;


CREATE UNIQUE INDEX name ON category(name);
CREATE UNIQUE INDEX name ON user(name);
CREATE UNIQUE INDEX email ON user(email);


CREATE INDEX name_cat ON category(name);
CREATE INDEX name_lot ON lot(name);


ALTER TABLE `lot`
	ADD CONSTRAINT `fk_rid_author` FOREIGN KEY (`rid_author`) REFERENCES `user` (`id`)
		ON UPDATE CASCADE ON DELETE RESTRICT,
	ADD CONSTRAINT `fk_rid_winner` FOREIGN KEY (`rid_winner`) REFERENCES `user` (`id`)
		ON UPDATE CASCADE ON DELETE RESTRICT,
	ADD CONSTRAINT `fk_rid_category` FOREIGN KEY (`rid_category`) REFERENCES `category` (`id`)
		ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `bid`
	ADD CONSTRAINT `fk_rid_user` FOREIGN KEY (`rid_user`) REFERENCES `user` (`id`)
		ON UPDATE CASCADE ON DELETE RESTRICT,
	ADD CONSTRAINT `fk_rid_lot` FOREIGN KEY (`rid_lot`) REFERENCES `lot` (`id`)
		ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `user`
	ADD CONSTRAINT `fk_rid_created_lots` FOREIGN KEY (`rid_created_lots`) REFERENCES `lot` (`id`)
		ON UPDATE CASCADE ON DELETE RESTRICT,
	ADD CONSTRAINT `fk_rid_bids` FOREIGN KEY (`rid_bids`) REFERENCES `bid` (`id`)
		ON UPDATE CASCADE ON DELETE RESTRICT;
