<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
    <ul class="promo__list">
        <?php foreach ($categories as $key => $value) { ?>
        <li class="promo__item promo__item--boards">
            <a class="promo__link" href="category.php?id=<?=$value['id'] ?>"><?=$value['name'] ?></a>
        </li>
        <?php 
        } ?>
    </ul>
</section>
<section class="lots">
    <div class="content__main-col">

    <header class="content__header">
        <h2 class="content__header-text">Результаты поиска по запросу <?=htmlspecialchars($search) ?></h2>
        <p><a class="button button--transparent content__header-button" href="/">Назад</a></p>
    </header>
	</div>
    <ul class="lots__list">
        <?php foreach ($foundLots as $key => $value) { ?>
        <li class="lots__item lot">
            <div class="lot__image">
                <img src="<?=$value['image_url'] ?>" width="350" height="260" alt="<?=$value['item'] ?>">
            </div>
            <div class="lot__info">
                <span class="lot__category"><?=$value['category'] ?></span>
                <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?=$value['id'] ?>"><?=$value['item'] ?></a></h3>
                <div class="lot__state">
                    <div class="lot__rate">
                        <span class="lot__amount">Стартовая цена</span>
                        <span class="lot__cost"><?=formatPrice($value['starting_price']) ?></span>
                    </div>
                    <div class="lot__timer timer">
                        <?=lifetimeLot($value['closing_date']) ?>
                    </div>
                </div>
            </div>
        </li>
        <?php 
        } ?>
    </ul>
    <?php print($pagination) ?>
</section>
