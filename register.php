<?php 

require_once('functions.php');

$userSes = startSession();

$categories = dbGetCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$userData = array_map('htmlspecialchars', $_POST); 
	$required = ['email', 'password', 'name', 'message'];
	$errors = [];

	/* проверка на заполненность текстовых полей */
	foreach ($required as $key) {
		if (empty($userData[$key])) {
            $errors[$key] = 'Заполните поле';
		}
	}

	/* проверка на валидность и уникальность введенного email */
	if (filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
		if (dbCheckEmail($userData['email']) > 0) {
        	$errors['email'] = 'Пользователь с этим email уже зарегистрирован';
    	}
	} 
	else {
		$errors['email'] = 'Некорректно введен email';
	} 

	/* если был получен файл */
	if (!empty($_FILES['avatar']['name'])) {
		$tmp_name = $_FILES['avatar']['tmp_name'];
		$u_name_file = $_FILES['avatar']['name'];
		$resLoadImage = loadImg($tmp_name, $u_name_file);
		if ($resLoadImage !== -1) {
			$userData['path'] = $resLoadImage;
		}
		else {
			$errors['file'] = 'Необходимо загрузить файл в формате .jpg или .png';
		}
	}
	/* иначе аватар по умолчанию */
	else {
		$userData['path'] = NULL;
	}
	
	if (empty($errors)) {
		$userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);

		/* если пользователь был добавлен */
		if (dbAddUser($userData) === 1) {
			header("Location: enter.php");
			die();
		}
	}
	else {
		$page_content = include_template('register.php', ['userData' => $userData, 'errors' => $errors, 'categories' => $categories]);
	}

}
else {
	$page_content = include_template('register.php', ['categories' => $categories]);
}

$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => "Yeticave - регистрация пользователя", 
			'user_name' => $userSes['user_name'], 'user_avatar' => $userSes['user_avatar'], 'categories' => $categories]);

print($layout_content);    

?>