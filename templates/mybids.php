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
        <h2 class="content__header-text">Мои лоты</h2>
    </header>
	</div>
    <table class="history-bids">
    <?php if (!empty($myBids)) { 
      for ($i = 0; $i <= count($myBids) - 1; $i++) { 
        if ($myBids[$i]['winner_id'] == $myBids[$i]['user_id']) {
            $classname = 'markable';
        } 
        else {
            $classname = '';        
        } ?>
        <tr class="<?=$classname ?>">
        <td><a class="text-link" href="lot.php?id=<?=$myBids[$i]['lot_id'] ?>"><?=$myBids[$i]['lot_name'] ?></a></td>
        <td><?=$myBids[$i]['sum'] ?></td>
        <td><?=$myBids[$i]['date_of'] ?></td>
        <?php if ($classname) { ?>
        <td><?=dbGetAuthorData($myBids[$i]['lot_id'])['contacts'] ?></td>
        <?php 
        } ?>
        </tr>
      <?php 
      } ?>
    <?php 
      } ?>
    </table>
</section>