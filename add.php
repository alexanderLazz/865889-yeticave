<?php 

require_once('functions.php');
require_once('settings.php');

$categories = dbGetCategories();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$adv = array_map('htmlspecialchars', $_POST); 
	$required = ['lot-name', 'message', 'lot-rate', 'lot-step'];
	$required_num = ['lot-rate', 'lot-step'];
	$errors = [];

	/* проверка на заполненность текстовых полей */
	foreach ($required as $key) {
		if (empty($adv[$key])) {
            $errors[$key] = 'Заполните поле';
		}
	}

	/* проверка на корректность ввода цифровых значений */
	foreach ($required_num as $key) {
		if (!array_key_exists($key, $errors)) {
			if (!is_numeric($adv[$key]) or $adv[$key] <= 0) {
            	$errors[$key] = 'Введено некорректное число';
        	}
		}
	}

	/* проверка даты на валидность: 
	что указанная дата больше текущей даты хотя бы на один день */
	if (strtotime($adv['lot-date']) - time() < 86400) {
		$errors['lot-date'] = 'Некорректная дата';
	}

	/* проверка - была ли выбрана категория */
	$flag_ch_category = 0;
	foreach ($categories as $key => $value) {
		if ($value['id'] == $adv['category']) {
			$flag_ch_category = 1;
		}
	}
	if (!$flag_ch_category) {
		$errors['category'] = 'Выберите категорию';
	}

	/* если был получен файл */
	if (!empty($_FILES['lot-img']['name'])) {
		$tmp_name = $_FILES['lot-img']['tmp_name'];
		$u_name_file = $_FILES['lot-img']['name'];
		$resLoadImage = loadImg($tmp_name, $u_name_file);
		if ($resLoadImage != -1) {
			$adv['path'] = $resLoadImage;
		}
		else {
			$errors['file'] = 'Необходимо загрузить файл в формате .jpg или .png';
		}
	}
	else {
		$errors['file'] = 'Вы не загрузили файл';
	}

	/* если были ошибки в заполнении полей, показать их */
	if (count($errors)) {
		$page_content = include_template('add.php', ['adv' => $adv, 'errors' => $errors, 'categories' => $categories]);
	}
	/* иначе выполнить запрос на добавление нового лота и перейти на страницу с ним */
	else {
		$res_lot_id = dbAddLot($adv);

		header("Location: lot.php?id=" . $res_lot_id);
		die();
		}
}
else {
	$page_content = include_template('add.php', ['categories' => $categories]);
}


$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => "Yeticave - добавление лота", 
		'user_name' => $user_name, 'user_avatar' => $user_avatar, 'categories' => $categories, 'is_auth' => $is_auth]);

print($layout_content);

?>