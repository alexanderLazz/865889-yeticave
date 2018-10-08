<?php 

require_once('functions.php');

$userSes = startSession();

$categories = dbGetCategories();

$lotId = (int) $_GET['id']; // получить id лота
$advert = dbGetLot($lotId);

if (isset($advert['max_bid'])) {
	$cur_price = $advert['max_bid'];
}
else {
	$cur_price = $advert['starting_price'];
}

$next_avail_bid = $cur_price + $advert['bid_step'];

$page_content = include_template('lot.php', ['advert' => $advert, 'categories' => $categories, 'cur_price' => $cur_price, 
	'next_avail_bid' => $next_avail_bid]);

$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => $advert['item'], 
		'user_name' => $userSes['user_name'], 'user_avatar' => $userSes['user_avatar'], 'categories' => $categories]);

print($layout_content);

?>