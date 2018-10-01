<?php

require_once('functions.php');
require_once('settings.php');

$limit_show_lots = 6; // количество лотов на главной странице

$categories = dbGetCategories();

$adverts = dbGetAdverts($limit_show_lots);

$lifetime_lot = timeToMidnight();

$page_content = include_template('index.php', ['categories' => $categories, 'adverts' => $adverts, 'lifetime_lot' => $lifetime_lot]);

$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Yeticave главная', 'user_name' => $user_name, 'user_avatar' => $user_avatar, 'categories' => $categories, 'is_auth' => $is_auth]);

print($layout_content);

?>
