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

function formatPrice($dig) {
    return (number_format(ceil($dig), 0, "", " ") . " " . "\u{20BD}");
}

function lifetimeLot($end_date) {
    $diff_sec = strtotime($end_date) - time();
    $days = floor($diff_sec / 86400);
    $hours = floor(($diff_sec % 86400) / 3600);
    $minutes = floor(($diff_sec % 3600) / 60);

    if ($days > 0) {
        return $days . 'д ' . $hours . 'ч ' . $minutes . 'м';
    }
    else {
        return $hours . 'ч ' . $minutes . 'м';   
    }
}

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

/* получение категорий */
function dbGetCategories() {
    $link = dbConnect();

    $query_get_categories = "SELECT `id`, `name` FROM `category`";

    $result_get_categories = mysqli_query($link, $query_get_categories);

    if (!$result_get_categories) {
        printf("Не удалось выполнить запрос: %s\n", mysqli_error());
        die();
    }

    return mysqli_fetch_all($result_get_categories, MYSQLI_ASSOC);
}

/* получение списка объявлений на главную */
function dbGetAdverts($limit) {
    $link = dbConnect();

    $query_get_lots = "SELECT `lot`.`id`, `lot`.`name` as 'item', `category`.`name` as 'category', `starting_price`, `image_url`, `closing_date` 
                        FROM `lot` 
                        JOIN `category` ON `category`.`id` = `lot`.`category_id`
                        WHERE `lot`.`closing_date` >= CURDATE()
                        ORDER BY `lot`.`creation_date` DESC LIMIT $limit";

    $result_get_lots = mysqli_query($link, $query_get_lots);

    if (!$result_get_lots) {
        printf("Не удалось выполнить запрос: %s\n", mysqli_error());
        die();
    }

    return mysqli_fetch_all($result_get_lots, MYSQLI_ASSOC);
}

/* получение лота по id */
function dbGetLot($lotId) {
    $link = dbConnect();

    $query_get_lot = "SELECT `lot`.`id`, `lot`.`name` as 'item', `category`.`name` as 'category', `starting_price`, `image_url`, `description`, 
                                MAX(`bid`.`sum`) as 'max_bid', `bid_step`, `closing_date`
                        FROM `lot` 
                        LEFT JOIN `bid` ON `lot`.`id` = `bid`.`lot_id`
                        JOIN `category` ON `category`.`id` = `lot`.`category_id`
                        WHERE `lot`.`id` = $lotId
                        GROUP BY `lot`.`id`";

    $result_get_lot = mysqli_query($link, $query_get_lot);

    if (!$result_get_lot) {
        printf("Не удалось выполнить запрос: %s\n", mysqli_error());
        die();
    }

    if (mysqli_num_rows($result_get_lot) < 1) {
        http_response_code(404);
        die();
    }

    return mysqli_fetch_assoc($result_get_lot);
}

?>
