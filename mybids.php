<?php 

require_once('functions.php');

$userSes = startSession();

$categories = dbGetCategories();

if (isset($_SESSION['user'])) {
	$limitShow = 15;
	$myBids = dbGetUserbids($_SESSION['user']['id'], $limitShow);
}
else {
	header("Location: index.php");
	die();
}

$page_content = include_template('mybids.php', ['myBids' => $myBids, 'categories' => $categories]);

$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Мои ставки', 
		'user_name' => $userSes['user_name'], 'user_avatar' => $userSes['user_avatar'], 'categories' => $categories]);

print($layout_content);

?>