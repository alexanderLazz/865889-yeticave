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
      <?php if (isset($_SESSION['user']) and $advert['closing_date'] <= time() 
                  and $advert['author_id'] != $_SESSION['user']['id'] and !$bidAlreadyDone) { 
          $classname = isset($errorBid) ? "form--invalid" : ""; ?>
      <form class="form container <?=$classname ?>" action="../lot.php?id=<?=$advert['id'] ?>" method="post"> <!-- form--invalid -->
        <h2>Ваша ставка</h2>
        <?php $classname = isset($errorBid) ? "form__item--invalid" : ""; ?>
        <div class="form__item <?=$classname ?>"> <!-- form__item--invalid -->
          <label for="bid">Сумма ставки*</label>
          <input id="bid" type="text" name="bid" placeholder="Введите вашу ставку" required>
          <span class="form__error"><?=$errorBid ?></span>
        </div>
        <button type="submit" class="button">Сделать ставку</button>
      </form>
      <?php 
      } ?>
      <table class="history-bids">
        <caption>История ставок</caption>
        <?php if (!empty($historyBid)) { 
          for ($i = 0; $i <= count($historyBid) - 1; $i++) { ?>
            <tr>
            <td><?=$historyBid[$i]['name'] ?></td>
            <td><?=$historyBid[$i]['sum'] ?></td>
            <td><?=printTimeBid($historyBid[$i]['date_of']) ?></td>
            </tr>
          <?php 
          } ?>
        <?php 
          } ?>
      </table>
    </div>
  </div>
</section>
