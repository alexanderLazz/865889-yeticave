<?php 

require_once('functions.php');

$userSes = startSession();

$categories = dbGetCategories();

$catID = (int) $_GET['id'];

if ($catID) {
	$cur_page = $_GET['page'] ?? 1;
	$page_items = 9;

	$offset = ($cur_page - 1) * $page_items;
	$foundLots = dbGetCatLots($catID, $page_items, $offset, 'pass');
	$countLots = dbGetCatLots($catID, $page_items, $offset, 'getCountLots');

	if ($foundLots) {
		$catName = $foundLots[0]['category'];

		$pages_count = ceil(count($countLots) / $page_items);
		
		$pages = range(1, $pages_count);

		$href = 'category.php?id=' . $catID;
		$pagination = include_template('pagination.php', ['cur_page' => $cur_page, 'pages_count' => $pages_count, 'pages' => $pages, 
																'href' => $href]);

		$page_content = include_template('category.php', ['categories' => $categories, 'foundLots' => $foundLots, 
														'pagination' => $pagination, 'catName' => $catName]);

		$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Результаты поиска', 
	        'user_name' => $userSes['user_name'], 'user_avatar' => $userSes['user_avatar'], 'categories' => $categories]);

		print($layout_content);
	}
	else {
		print("Лоты не найдены");
	}
}

?>