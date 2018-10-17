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

/* проверка - делал ли пользователь ставки на данный лот */
$bidAlreadyDone = False;
if (isset($_SESSION['user'])) {
	if (dbCheckUserBids($lotId, $_SESSION['user']['id'])) {
		$bidAlreadyDone = True;
	}
}

/* получаем историю ставок для данного лота */
$limitRows = 10; // отображать кол-во ставок
$historyBid = dbGetHistoryBids($lotId, $limitRows);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$bid = (int) $_POST['bid'];
	$errorBid = '';

	/* Проверка на введенную ставку */
	if (!empty($bid)) {
		if ($bid < $next_avail_bid) {
			$errorBid = 'Ставка должна быть больше текущей цены';
		}
	}
	else {
		$errorBid = 'Заполните поле';
	}

	/* Если были ошибки в заполнении формы показать их */
	if ($errorBid) {
		$page_content = include_template('lot.php', ['advert' => $advert, 'categories' => $categories, 'cur_price' => $cur_price, 
			'next_avail_bid' => $next_avail_bid, 'bidAlreadyDone' => $bidAlreadyDone, 'historyBid' => $historyBid, 'errorBid' => $errorBid]);
	}
	/* иначе выполнить запрос на добавление ставки */
	else {
		dbAddBid($bid, $lotId, $_SESSION['user']['id']);
		$page_content = include_template('lot.php', ['advert' => $advert, 'categories' => $categories, 'cur_price' => $cur_price, 
		'next_avail_bid' => $next_avail_bid, 'historyBid' => $historyBid, 'bidAlreadyDone' => $bidAlreadyDone]);
		echo "<meta http-equiv='refresh' content='0'>";
	}
}
else {
	$page_content = include_template('lot.php', ['advert' => $advert, 'categories' => $categories, 'cur_price' => $cur_price, 
		'next_avail_bid' => $next_avail_bid, 'historyBid' => $historyBid, 'bidAlreadyDone' => $bidAlreadyDone]);
}

$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => $advert['item'], 
		'user_name' => $userSes['user_name'], 'user_avatar' => $userSes['user_avatar'], 'categories' => $categories]);

print($layout_content);

?>