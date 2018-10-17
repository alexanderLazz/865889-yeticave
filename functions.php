<?php

function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!file_exists($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require_once $name;

    $result = ob_get_clean();

    return $result;
}

/**
* Форматирование цены лота - деление на разряды и добавление символа рубля
*
* @param int $dig цена
*
* @return string 
*/
function formatPrice($dig) {
    return (number_format(ceil($dig), 0, "", " ") . " " . "\u{20BD}");
}

/**
* Подсчет времени жизни лота
*
* @param date $end_date дата окончания торгов
*
* @return string 
*/
function lifetimeLot($end_date) {
    $diff_sec = strtotime($end_date) - time();
    $days = floor($diff_sec / 86400);
    $hours = floor(($diff_sec % 86400) / 3600);
    $minutes = floor(($diff_sec % 3600) / 60);

    if ($days > 0) {
        return $days . 'д ' . $hours . 'ч ' . $minutes . 'м';
    }
    elseif ($diff_sec <= 0) {
        return 'срок истек';
    }

    return $hours . 'ч ' . $minutes . 'м';

}

/**
* Подсчет времени с момента совершения ставки
*
* @param date $time
*
* @return string вывод в удобном для человка формате
*/
function printTimeBid($time) {
    $diff_sec = time() - strtotime($time);
    $days = floor($diff_sec / 86400);
    $hours = floor(($diff_sec % 86400) / 3600);
    $minutes = floor(($diff_sec % 3600) / 60);

    if ($days > 0) {
        return $days . ' д. назад';
    }
    elseif ($hours > 0) {
        return $hours . ' ч. назад';   
    }

    return $minutes . ' м. назад';   

}

/**
* Подключение к БД
*
* @return object 
*/
function dbConnect() {
    $dbParams = [
    'host' => 'localhost', // адрес сервера
    'database' => 'yeticave', // имя базы данных
    'user' => 'user', // имя пользователя
    'password' => '' // пароль
    ];

    $link = mysqli_connect($dbParams['host'], $dbParams['user'], $dbParams['password'], $dbParams['database']);
    mysqli_set_charset($link, "utf8");

    if (!$link) {
        printf("Не удалось подключиться: %s\n", mysqli_connect_error());
        die();
    }

    return $link;
}

function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = null;

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);
    }

    return $stmt;
}

/**
* Получение категорий из БД
*
* @return array массив категорий  
*/
function dbGetCategories() {
    $link = dbConnect();

    $query_get_categories = "SELECT `id`, `name` FROM `category`";

    $result_get_categories = mysqli_query($link, $query_get_categories);

    if (!$result_get_categories) {
        printf("Не удалось выполнить запрос: %s\n", mysqli_error($link));
        die();
    }

    return mysqli_fetch_all($result_get_categories, MYSQLI_ASSOC);
}

/**
* Получение списка лотов на главную страниуц
*
* @param int $limit количество лотов
*
* @return array 
*/
function dbGetAdverts($limit) {
    $link = dbConnect();

    $query_get_lots = "SELECT `lot`.`id`, `lot`.`name` as 'item', `category`.`name` as 'category', `starting_price`, `image_url`, 
                                `closing_date` 
                        FROM `lot` 
                        JOIN `category` ON `category`.`id` = `lot`.`category_id`
                        WHERE `lot`.`closing_date` >= CURDATE()
                        ORDER BY `lot`.`creation_date` DESC LIMIT $limit";

    $result_get_lots = mysqli_query($link, $query_get_lots);

    if (!$result_get_lots) {
        printf("Не удалось выполнить запрос: %s\n", mysqli_error($link));
        die();
    }

    return mysqli_fetch_all($result_get_lots, MYSQLI_ASSOC);
}

/**
* Получение информации о лоте по его id
*
* @param int $lotId 
*
* @return array 
*/
function dbGetLot($lotId) {
    $link = dbConnect();

    $query_get_lot = "SELECT `lot`.`id`, `lot`.`name` as 'item', `category`.`name` as 'category', `starting_price`, `image_url`, 
                                `description`, MAX(`bid`.`sum`) as 'max_bid', `bid_step`, `closing_date`, `author_id`
                        FROM `lot` 
                        LEFT JOIN `bid` ON `lot`.`id` = `bid`.`lot_id`
                        JOIN `category` ON `category`.`id` = `lot`.`category_id`
                        WHERE `lot`.`id` = $lotId
                        GROUP BY `lot`.`id`";

    $result_get_lot = mysqli_query($link, $query_get_lot);

    if (!$result_get_lot) {
        printf("Не удалось выполнить запрос: %s\n", mysqli_error($link));
        die();
    }

    if (mysqli_num_rows($result_get_lot) < 1) {
        http_response_code(404);
        die();
    }

    return mysqli_fetch_assoc($result_get_lot);
}

/**
* Добавление пользовательского лота в БД
*
* @param array $adv данные из формы
*
* @return int возвращает id добавленного лота
*/
function dbAddLot($adv) {
    $link = dbConnect();

    $sql = 'INSERT INTO `lot` (`creation_date`, `name`, `description`, `image_url`, `starting_price`, `closing_date`, `bid_step`, 
                `author_id`, `winner_id`, `category_id`) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, NULL, ?)';

    $stmt = db_get_prepare_stmt($link, $sql, [$adv['lot-name'], $adv['message'], $adv['path'], $adv['lot-rate'], $adv['lot-date'], 
                    $adv['lot-step'], $_SESSION['user']['id'], $adv['category']]);

    $res = mysqli_stmt_execute($stmt);

    if (!$res) {
        printf("Не удалось выполнить запрос: %s\n", mysqli_error($link));
        http_response_code(404);
        die();
    }

    return mysqli_insert_id($link);
}

/**
* Проверка - есть ли пользователь с введенным email в БД
*
* @param string $email
*
* @return int возвращает количество строк запроса
*/
function dbCheckEmail($email) {
    $link = dbConnect();

    $emailClear = mysqli_real_escape_string($link, $email);

    $query_check_email = "SELECT `id` FROM `user` WHERE `email` = '$emailClear'";

    $result_check_email = mysqli_query($link, $query_check_email);

    if (!$result_check_email) {
        printf("Не удалось выполнить запрос: %s\n", mysqli_error($link));
        http_response_code(404);
        die();
}

    return mysqli_num_rows($result_check_email);
}

/**
* Получение информации о пользователе в БД по введенному email
*
* @param string $email
*
* @return array
*/
function dbGetUserData($email) {
    $link = dbConnect();

    $emailClear = mysqli_real_escape_string($link, $email);

    $query_check_email = "SELECT * FROM `user` WHERE `email` = '$emailClear'";

    $result_check_email = mysqli_query($link, $query_check_email);

    if (!$result_check_email) {
        printf("Не удалось выполнить запрос: %s\n", mysqli_error($link));
        http_response_code(404);
        die();
}

    return mysqli_fetch_assoc($result_check_email);
}

/**
* Загрузка пользовательского изображения в директорию user_upload
*
* @param string $tmp_name временное имя файла 
* @param string $u_name пользовательское имя файла
*
* @return array or int если формат отсутствует в списке разрешенных, то возвращается -1
*/
function loadImg($tmp_name, $u_name) {
    $allowed_types = ['image/jpeg', 'image/png'];

    $gen_filename = 'image_'.uniqid();
    $split_name = explode('.', $u_name);
    $file_extension = end($split_name);
    $filename = $gen_filename . '.' . $file_extension;

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $file_type = finfo_file($finfo, $tmp_name);

    /* проверка - является ли файл формата jpeg или png */
    if (!in_array($file_type, $allowed_types)) {
        return -1;
    }
        
    move_uploaded_file($tmp_name, 'user_upload/' . $filename);
    return 'user_upload/' . $filename;

}

/**
* Регистрация пользователя в БД
*
* @param array $data данные пользователя из формы
*
* @return int в случае успеха, возвращается значение 1
*/
function dbAddUser($data) {
    $link = dbConnect();

    if (isset($data['path'])) {
        $sql = 'INSERT INTO `user` (`reg_date`, `email`, `name`, `password`, `avatar`, `contacts`) 
                    VALUES  (NOW(), ?, ?, ?, ?, ?)';
        $stmt = db_get_prepare_stmt($link, $sql, [$data['email'], $data['name'], $data['password'], $data['path'], $data['message']]);
    } 
    else {
        $sql = 'INSERT INTO `user` (`reg_date`, `email`, `name`, `password`, `avatar`, `contacts`) 
                    VALUES  (NOW(), ?, ?, ?, NULL, ?)';
        $stmt = db_get_prepare_stmt($link, $sql, [$data['email'], $data['name'], $data['password'], $data['message']]);  
    }

    $res = mysqli_stmt_execute($stmt);

    if (!$res) {
        printf("Не удалось выполнить запрос: %s\n", mysqli_error($link));
        http_response_code(404);
        die();
    }

    return 1;
}

/**
* Запуск сессии пользователя
*
* @return array имя и аватар пользователя
*/
function startSession() {
    session_start();
    $userSes = [];

    if (!empty($_SESSION['user'])) {
        $userSes['user_name'] = $_SESSION['user']['name'];
        $userSes['user_avatar'] = $_SESSION['user']['avatar'];
    }
    else {
        $userSes['user_name'] = $userSes['user_avatar'] = NULL;
    }

    return $userSes;

}

/**
* Добавление ставки в БД
*
* @param int $bid сумма ставки
* @param int $lot_id для какого лота
* @param int $user_id кто добавляет
*
*/
function dbAddBid($bid, $lot_id, $user_id) {
    $link = dbConnect();

    $sql = 'INSERT INTO `bid` (`date_of`, `sum`, `user_id`, `lot_id`)
                VALUES  (NOW(), ?, ?, ?)';

    $stmt = db_get_prepare_stmt($link, $sql, [$bid, $user_id, $lot_id]);

    $res = mysqli_stmt_execute($stmt);

    if (!$res) {
        printf("Не удалось выполнить запрос: %s\n", mysqli_error($link));
        http_response_code(404);
        die();
    }
}

/**
* Проверка - делал ли пользователь ставки на выбранный лот
*
* @param int $lot_id
* @param int $user_id
*
* @return int количество строк запроса
*/
function dbCheckUserBids($lot_id, $user_id) {
    $link = dbConnect();

    $sql = 'SELECT `id` 
            FROM `bid`
            WHERE `lot_id` = ?
                AND `user_id` = ?';

    $stmt = db_get_prepare_stmt($link, $sql, [$lot_id, $user_id]);

    $res = mysqli_stmt_execute($stmt);    

    mysqli_stmt_store_result($stmt);

    if (!$res) {
        printf("Не удалось выполнить запрос: %s\n", mysqli_error($link));
        http_response_code(404);
        die();
    }

    return mysqli_stmt_num_rows($stmt);
}

/**
* Дата сделанной ставки и сумма для истории ставок
*
* @param int $lot_id
* @param int $limitRows лимитированный вывод
*
* @return array
*/
function dbGetHistoryBids($lot_id, $limitRows) {
    $link = dbConnect();

    $lotClear = mysqli_real_escape_string($link, $lot_id);

    $query_get_history_bids = "SELECT `date_of`, `sum`, `user`.`name` as 'name' 
                                FROM `bid` 
                                JOIN `user` ON `bid`.`user_id` = `user`.`id`            
                                WHERE `lot_id` = $lotClear
                                GROUP BY `bid`.`id`
                                ORDER BY `bid`.`date_of` DESC LIMIT $limitRows ";

    $result_get_history_bids = mysqli_query($link, $query_get_history_bids);

    if (!$result_get_history_bids) {
        printf("Не удалось выполнить запрос: %s\n", mysqli_error($link));
        http_response_code(404);
        die();
    }

    return mysqli_fetch_all($result_get_history_bids, MYSQLI_ASSOC);
}

/**
* Данные победителей ставок
*
* @return array
*/
function dbGetWinner() {
    $link = dbConnect();

    $query_get_winner = "SELECT `bid`.`lot_id`, `bid`.`sum`, `bid`.`user_id`, `lot`.`name` as lot_name, `user`.`name` as user_name, 
                                `user`.`email` as user_email
                            FROM `bid`
                            JOIN (SELECT `bid`.`lot_id`, MAX(`bid`.`sum`) as 'max_bid' FROM `bid` GROUP BY `bid`.`lot_id`) as temp
                            ON `bid`.`lot_id` = temp.`lot_id` AND `bid`.`sum` = temp.`max_bid`
                            JOIN `user` on `bid`.`user_id` = `user`.`id`
                            JOIN `lot` on `lot`.`id` = `bid`.`lot_id`
                            WHERE `lot`.`closing_date` <= CURDATE()
                            AND `lot`.`winner_id` is NULL";

    $result_get_winner = mysqli_query($link, $query_get_winner);

    if (!$result_get_winner) {
        printf("Не удалось выполнить запрос: %s\n", mysqli_error($link));
        http_response_code(404);
        die();
    }

    if (mysqli_num_rows($result_get_winner)) {
        return mysqli_fetch_all($result_get_winner, MYSQLI_ASSOC);
    }

}

/**
* Определение победителя для завершенной ставки, 
* доблавение в БД
*
* @param array $winners
*
* @return int возвращает 1 в случае успеха
*/
function dbAddWinner($winners) {
    $link = dbConnect();

    foreach ($winners as $key => $value) {
        $user_id = $value['user_id'];
        $lot_id = $value['lot_id'];
        $query_add_winner = "UPDATE `lot` SET `lot`.`winner_id` = $user_id WHERE `lot`.`id` = $lot_id";

        $result_add_winner = mysqli_query($link, $query_add_winner);

        if (!$result_add_winner) {
            printf("Не удалось выполнить запрос: %s\n", mysqli_error($link));
            http_response_code(404);
            die();
        }
    }

    return 1;
}

/**
* Поиск по введенному значению из формы по названию и описанию лота
*
* @param string $str_search искомая строка
* @param int $page_items сколько выводить лотов на одной странице
* @param int $offset расчет смещения для выборки из БД
* @param string $choice при вводе getCountLots подсчитывает общее количество найденных лотов
*
* @return array возвращает найденные лоты
*/
function dbSearchLot($str_search, $page_items, $offset, $choice) {
    $link = dbConnect();

    mysqli_query($link, 'CREATE FULLTEXT INDEX `lot_ft_search` ON `lot`(`lot`.`name`, `lot`.`description`)');

    if ($choice === 'getCountLots') {

        $query_search_lot = "SELECT `lot`.`id` as 'id', `lot`.`name` as `item`, `lot`.`description`, `category`.`name` as 'category', 
                            `starting_price`, `image_url`, `closing_date`
                            FROM `lot`
                            JOIN `category` ON `category`.`id` = `lot`.`category_id`
                            WHERE MATCH(`lot`.`name`, `lot`.`description`) AGAINST(?)";
    }
    else {
        $query_search_lot = "SELECT `lot`.`id` as 'id', `lot`.`name` as `item`, `lot`.`description`, `category`.`name` as 'category', 
                            `starting_price`, `image_url`, `closing_date`
                            FROM `lot`
                            JOIN `category` ON `category`.`id` = `lot`.`category_id`
                            WHERE MATCH(`lot`.`name`, `lot`.`description`) AGAINST(?)
                            ORDER BY `lot`.`creation_date` DESC LIMIT $page_items OFFSET $offset";   
    }

    $stmt = db_get_prepare_stmt($link, $query_search_lot, [$str_search]);

    mysqli_stmt_execute($stmt);

    $result_search_lot = mysqli_stmt_get_result($stmt);

    if (!$result_search_lot) {
        printf("Не удалось выполнить запрос: %s\n", mysqli_error($link));
        http_response_code(404);
        die();
    }

    return mysqli_fetch_all($result_search_lot, MYSQLI_ASSOC);
}

/**
* Поиск всех лотов для выбранной категории
*
* @param int $lotID выбранная категория
* @param int $page_items сколько выводить лотов на одной странице
* @param int $offset расчет смещения для выборки из БД
* @param string $choice при вводе getCountLots подсчитывает общее количество найденных лотов
*
* @return array возвращает найденные лоты
*/
function dbGetCatLots($lotID, $page_items, $offset, $choice) {
    $link = dbConnect();

    if ($choice === 'getCountLots') {

        $query_search_lot = "SELECT `lot`.`id` as 'id', `lot`.`name` as `item`, `lot`.`description`, `category`.`name` as 'category', 
                            `starting_price`, `image_url`, `closing_date`
                            FROM `lot`
                            JOIN `category` ON `category`.`id` = `lot`.`category_id`
                            WHERE `lot`.`category_id` = $lotID";
    }
    else {
        $query_search_lot = "SELECT `lot`.`id` as 'id', `lot`.`name` as `item`, `lot`.`description`, `category`.`name` as 'category', 
                            `starting_price`, `image_url`, `closing_date`
                            FROM `lot`
                            JOIN `category` ON `category`.`id` = `lot`.`category_id`
                            WHERE `lot`.`category_id` = $lotID
                            ORDER BY `lot`.`creation_date` DESC LIMIT $page_items OFFSET $offset";
    }

    $result_search_lot = mysqli_query($link, $query_search_lot);

    if (!$result_search_lot) {
        printf("Не удалось выполнить запрос: %s\n", mysqli_error($link));
        http_response_code(404);
        die();
    }

    return mysqli_fetch_all($result_search_lot, MYSQLI_ASSOC);
}

/**
* Пользовательские ставки для раздела мои ставки
*
* @param int $userID 
* @param int $limit количество выводимых ставок            
*
* @return array
*/
function dbGetUserBids($userID, $limit) {
    $link = dbConnect();

    $query_get_user_bids = "SELECT `bid`.`lot_id`, `bid`.`sum`, `bid`.`user_id`, `lot`.`name` as lot_name, `user`.`name` as user_name, 
                                        `lot`.`winner_id`, `bid`.`date_of`
                            FROM `bid`
                            JOIN `user` on `bid`.`user_id` = `user`.`id`
                            JOIN `lot` on `lot`.`id` = `bid`.`lot_id`
                            WHERE `user`.`id` = $userID
                            ORDER BY `bid`.`date_of` DESC LIMIT $limit";

    $result_get_user_bids = mysqli_query($link, $query_get_user_bids);

    if (!$result_get_user_bids) {
        printf("Не удалось выполнить запрос: %s\n", mysqli_error($link));
        http_response_code(404);
        die();
    }

    return mysqli_fetch_all($result_get_user_bids, MYSQLI_ASSOC);
}

/**
* Получение контактных данных автора лота
*
* @param int $lot_id           
*
* @return array
*/
function dbGetAuthorData($lot_id) {
    $link = dbConnect();

    $query_get_author = "SELECT `user`.`contacts`, `user`.`id`
                            FROM `lot`
                            JOIN `user` on `user`.`id` = `lot`.`author_id`
                            WHERE `lot`.`id` = $lot_id";

    $result_get_author = mysqli_query($link, $query_get_author);

    if (!$result_get_author) {
        printf("Не удалось выполнить запрос: %s\n", mysqli_error($link));
        http_response_code(404);
        die();
    }

    return mysqli_fetch_assoc($result_get_author);
}