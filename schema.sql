CREATE DATABASE yeticave
	DEFAULT CHARACTER SET utf8
	DEFAULT COLLATE utf8_general_ci;

USE yeticave;

CREATE TABLE category (
	id		INT AUTO_INCREMENT PRIMARY KEY,
	name	CHAR(100) NOT NULL
);

CREATE TABLE lot (
	id				INT AUTO_INCREMENT PRIMARY KEY,
	creation_date	DATE NOT NULL,
	name			CHAR(130) NOT NULL,
	description		VARCHAR(170) NOT NULL,
	image_url		VARCHAR(120) NOT NULL,
	starting_price	FLOAT NOT NULL,
	closing_date	DATE NOT NULL,
	bid_step		FLOAT NOT NULL
);

CREATE TABLE bid (
	id			INT AUTO_INCREMENT PRIMARY KEY,
	date_of		DATE NOT NULL
);

CREATE TABLE user (
	id			INT AUTO_INCREMENT PRIMARY KEY,
	reg_date	DATE NOT NULL,
	email		CHAR(120) NOT NULL,
	name		CHAR(130) NOT NULL,
	password	CHAR(70) NOT NULL,
	avatar		CHAR(120),
	contacts	VARCHAR(150) NOT NULL
);


CREATE UNIQUE INDEX name ON category(name);
CREATE UNIQUE INDEX name ON user(name);
CREATE UNIQUE INDEX email ON user(email);


CREATE INDEX name_cat ON category(name);
CREATE INDEX name_lot ON lot(name);

