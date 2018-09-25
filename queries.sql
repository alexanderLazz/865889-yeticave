USE yeticave;

/* добавление категорий в таблицу category */
INSERT INTO `category` (`name`) 
	VALUES  ('Доски и лыжи'), 
			('Крепления'), 
			('Ботинки'), 
			('Одежда'), 
			('Инструменты'), 
			('Разное');

/* добавление пользователей в таблицу user */
INSERT INTO `user` (`reg_date`, `email`, `name`, `password`, `avatar`, `contacts`) 
	VALUES  ('2018-09-19', 'rv@user.com', 'Роман Виноградов',  'qwe', 'img/user.jpg', 'rv@user.com'),
			('2018-09-20', 'va@user.com', 'Василий Арбузов',   'rty', 'img/user.jpg', 'va@user.com'),
			('2018-09-20', 'mp@user.com', 'Максим Помидоркин', 'asd', 'img/user.jpg', 'mp@user.com');

/* добавление списка объявлений в таблицу lot */
INSERT INTO `lot` (`creation_date`, `name`, `description`, `image_url`, `starting_price`, `closing_date`, `bid_step`, `author_id`, `winner_id`, `category_id`)
	VALUES  ('2018-09-21', '2014 Rossignol District Snowboard',                 'сноуборд 2014 года', 'img/lot-1.jpg', 10999,  '2018-09-30', 100, 1, NULL, 1),
	 		('2018-09-21', 'DC Ply Mens 2016/2017 Snowboard',                   'сноуборд',           'img/lot-2.jpg', 159999, '2018-09-30', 100, 1, NULL, 1),
	 		('2018-09-22', 'Крепления Union Contact Pro 2015 года размер L/XL', 'крепления',          'img/lot-3.jpg', 8000,   '2018-09-30', 100, 1, NULL, 2),
	 		('2018-09-23', 'Ботинки для сноуборда DC Mutiny Charocal',          'ботинки',            'img/lot-4.jpg', 10999,  '2018-09-24', 100, 2, NULL, 3),
	 		('2018-09-23', 'Куртка для сноуборда DC Mutiny Charocal',           'куртка',             'img/lot-5.jpg', 7500,   '2018-09-30', 100, 2, NULL, 4),
	 		('2018-09-23', 'Маска Oakley Canopy',                               'маска',              'img/lot-6.jpg', 5400,   '2018-09-30', 100, 2, NULL, 6);

/* добавление ставок в таблицу bid */
INSERT INTO `bid` (`date_of`, `sum`, `user_id`, `lot_id`)
	VALUES  ('2018-09-23', 8500, 2, 3),
			('2018-09-24', 5700, 1, 6),
			('2018-09-24', 5900, 3, 6);



/* запрос на получение всех категорий */
SELECT `name` FROM `category`;

/* запрос на получение самых новых, открытых лотов */
SELECT `lot`.`name`, `starting_price`, `image_url`, COUNT(`bid`.`id`) as 'count_bids', MAX(`bid`.`sum`) as 'price', `category`.`name` as 'category'
FROM `lot`
JOIN `category` ON `category`.`id` = `lot`.`category_id`
LEFT JOIN `bid` ON `lot`.`id` = `bid`.`lot_id`
WHERE `lot`.`closing_date` >= CURDATE() 
AND `lot`.`winner_id` is NULL
GROUP BY `lot`.`name`, `lot`.`id`, `lot`.`starting_price`, `lot`.`image_url`, `lot`.`category_id`, `lot`.`creation_date`
ORDER BY `lot`.`creation_date` DESC LIMIT 3; 

/* запрос на получение лотов и категорий по id */
SELECT `date_of`, `sum`, `lot`.`name` as 'lot', `category`.`name` as `category`
FROM `bid`
JOIN `lot` ON `lot`.`id` = `bid`.`lot_id`
JOIN `category` ON `category`.`id` = `lot`.`category_id`;

/* запрос на обновление лота по идентификатору */
UPDATE `lot` SET `name` = 'Крепления Union Contact Pro 2015 года размер L/XXL' 
WHERE `lot`.`id` = 3;

/* список самых свежих ставок для лота по его идентификатору */
SELECT MAX(`date_of`) as 'date', MAX(`sum`) as 'last_bid', `lot`.`name` as 'lot'
FROM `bid`
JOIN `lot` ON `lot`.`id` = `bid`.`lot_id`
GROUP BY `lot`.`name`;



