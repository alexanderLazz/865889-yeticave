<?php 

require_once('functions.php');

$userSes = startSession();

$categories = dbGetCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$loginForm = array_map('htmlspecialchars', $_POST); 
	$required = ['email', 'password'];
	$errors = [];

	/* проверка на заполненность текстовых полей */
	foreach ($required as $key) {
		if (empty($loginForm[$key])) {
            $errors[$key] = 'Заполните поле';
		}
	}

	$user = dbGetUserData($loginForm['email']);

	if (empty($errors) and !empty($user)) {
		if (password_verify($loginForm['password'], $user['password'])) {
			$_SESSION['user'] = $user;
		}
		else {
			$errors['password'] = 'Вы ввели неверный пароль';
		}
	}
	else {
		$errors['email'] = 'Вы ввели неверный email';
	}

	if (empty($errors)) {
		header("Location: index.php");
		die();
	}
	else {
		$page_content = include_template('enter.php', ['loginForm' => $loginForm, 'errors' => $errors, 'categories' => $categories]);
	}
}
else {
   	$page_content = include_template('enter.php', ['categories' => $categories]);
}


$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => "Yeticave - авторизация", 
			'user_name' => $userSes['user_name'], 'user_avatar' => $userSes['user_avatar'], 'categories' => $categories]);

print($layout_content);  

?>