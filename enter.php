<?php 

require_once('functions.php');
require_once('settings.php');

$categories = dbGetCategories();

$page_content = include_template('enter.php', ['categories' => $categories]);

$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => "Yeticave - авторизация", 
			'user_name' => $user_name, 'user_avatar' => $user_avatar, 'categories' => $categories, 'is_auth' => $is_auth]);

print($layout_content);  

?>