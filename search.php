<?php 

require_once('functions.php');

$userSes = startSession();

$categories = dbGetCategories();

$search = trim($_GET['search']) ?? '';

if ($search) {
	$cur_page = $_GET['page'] ?? 1;
	$page_items = 9;

	$offset = ($cur_page - 1) * $page_items;
	$foundLots = dbSearchLot($search, $page_items, $offset, 'pass');
	$countLots = dbSearchLot($search, $page_items, $offset, 'getCountLots');

	$pages_count = ceil(count($countLots) / $page_items);
	
	$pages = range(1, $pages_count);

	$href = 'search.php?search=' . $search;
	$pagination = include_template('pagination.php', ['cur_page' => $cur_page, 'pages_count' => $pages_count, 'pages' => $pages, 
															'search' => $search, 'href' => $href]);

	$page_content = include_template('search.php', ['categories' => $categories, 'foundLots' => $foundLots, 'search' => $search, 
													'pagination' => $pagination]);

	$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Результаты поиска', 
        'user_name' => $userSes['user_name'], 'user_avatar' => $userSes['user_avatar'], 'categories' => $categories]);

	print($layout_content);
}


?>