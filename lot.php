<?php 

require_once('functions.php');
require_once('settings.php');

if (isset($_GET['id'])) {
	$lotId = (int) $_GET['id']; // получить id лота

	$categories = dbGetCategories();

	$advert = dbGetLot($lotId);

	if (isset($advert['max_bid'])) {
		$cur_price = $advert['max_bid'];
	}
	else {
		$cur_price = $advert['starting_price'];
	}

	$next_avail_bid = $cur_price + $advert['bid_step'];

	$lifetime_lot = timeToMidnight();

	$page_content = include_template('lot.php', ['advert' => $advert, 'categories' => $categories, 'cur_price' => $cur_price, 
		'next_avail_bid' => $next_avail_bid, 'lifetime_lot' => $lifetime_lot]);

	$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => $advert['item'], 'user_name' => $user_name, 'user_avatar' => $user_avatar, 'categories' => $categories, 'is_auth' => $is_auth]);

	print($layout_content);
}
else {
	http_response_code(404);
}

?>