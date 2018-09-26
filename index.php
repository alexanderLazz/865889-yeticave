<?php

require_once('functions.php');

$link = mysqli_connect("localhost", "user", "", "yeticave");
mysqli_set_charset($link, "utf8");

if (!$link) {
    printf("Не удалось подключиться: %s\n", mysqli_connect_error());
}
else {
    /* получаем список категорий из БД */
    $query_get_categories = "SELECT `id`, `name` FROM `category`";
    
    $result_get_categories = mysqli_query($link, $query_get_categories);

    if ($result_get_categories) {
        $categories = mysqli_fetch_all($result_get_categories, MYSQLI_ASSOC);
    }
    else {
        printf("Не удалось выполнить запрос: %s\n", mysqli_error());
    }

    /* получаем список лотов из БД */
    $query_get_lots = "SELECT `lot`.`name` as 'item', `category`.`name` as 'category', `starting_price`, `image_url` FROM `lot` 
                        JOIN `category` ON `category`.`id` = `lot`.`category_id`
                        WHERE `lot`.`closing_date` >= CURDATE()
                        ORDER BY `lot`.`creation_date` DESC";

    $result_get_lots = mysqli_query($link, $query_get_lots);

    if ($result_get_lots) {
        $adverts = mysqli_fetch_all($result_get_lots, MYSQLI_ASSOC);
    }
    else {
        printf("Не удалось выполнить запрос: %s\n", mysqli_error());
    }
}

$is_auth = rand(0, 1);

$user_name = 'Alexander';
$user_avatar = 'img/user.jpg';

/* время жизни лота - кол-во часов и минут, оставшихся до полуночи */
$lifetime_lot = gmdate('H:i', strtotime("tomorrow") - time());

$page_content = include_template('index.php', ['categories' => $categories, 'adverts' => $adverts, 'lifetime_lot' => $lifetime_lot]);

$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Yeticave главная', 'user_name' => $user_name, 'user_avatar' => $user_avatar, 'categories' => $categories, 'is_auth' => $is_auth]);

print($layout_content);

?>
