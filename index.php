<?php

require_once('functions.php');
require_once('settings.php');


$categories = dbGetCategories();

$limit_show_lots = 6; // количество лотов на главной странице
$adverts = dbGetAdverts($limit_show_lots);

$page_content = include_template('index.php', ['categories' => $categories, 'adverts' => $adverts]);

$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Yeticave главная', 'user_name' => $user_name, 'user_avatar' => $user_avatar, 'categories' => $categories, 'is_auth' => $is_auth]);

print($layout_content);

?>
