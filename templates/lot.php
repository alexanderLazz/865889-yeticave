<nav class="nav">
  <ul class="nav__list container">
    <?php foreach ($categories as $key => $value) { ?>
    <li class="nav__item">
      <a href="all-lots.html"><?=$value['name'] ?></a>
    <?php 
    } ?>
  </ul>
</nav>
<section class="lot-item container">
  <h2><?=$advert['item'] ?></h2>
  <div class="lot-item__content">
    <div class="lot-item__left">
      <div class="lot-item__image">
        <img src="<?=$advert['image_url'] ?>" width="730" height="548" alt="<?=$advert['item'] ?>">
      </div>
      <p class="lot-item__category">Категория: <span><?=$advert['category'] ?></span></p>
      <p class="lot-item__description"><?=$advert['description'] ?></p>
    </div>
    <div class="lot-item__right">
      <?php if (isset($_SESSION['user'])) { ?>
      <div class="lot-item__state">
        <div class="lot-item__timer timer">
          <?=lifetimeLot($advert['closing_date']) ?>
        </div>
        <div class="lot-item__cost-state">
          <div class="lot-item__rate">
            <span class="lot-item__amount">Текущая цена</span>
            <span class="lot-item__cost"><?=$cur_price ?></span>
          </div>
          <div class="lot-item__min-cost">
            Мин. ставка <span><?=formatPrice($next_avail_bid) ?></span>
          </div>
        </div>
      </div>
      <?php 
      } ?>
    </div>
  </div>
</section>
