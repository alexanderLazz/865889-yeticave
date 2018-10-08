<?php

require_once('functions.php');

$userSes = startSession();

$categories = dbGetCategories();

$limit_show_lots = 6; // количество лотов на главной странице
$adverts = dbGetAdverts($limit_show_lots);

$page_content = include_template('index.php', ['categories' => $categories, 'adverts' => $adverts]);

$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Yeticave главная', 
        'user_name' => $userSes['user_name'], 'user_avatar' => $userSes['user_avatar'], 'categories' => $categories]);

print($layout_content);

?>
