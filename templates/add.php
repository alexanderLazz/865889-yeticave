<?php $classname = isset($errors) ? "form--invalid" : ""; ?>
<form class="form form--add-lot container <?=$classname ?>" action="../add.php" method="post" enctype="multipart/form-data"> <!-- form--invalid -->
  <h2>Добавление лота</h2>
  <div class="form__container-two">
    <?php $classname = isset($errors['lot-name']) ? "form__item--invalid" : "";
        $value = isset($adv['lot-name']) ? $adv['lot-name'] : ""; ?>
    <div class="form__item <?=$classname ?>"> <!-- form__item--invalid -->
      <label for="lot-name">Наименование</label>
      <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота" value="<?=$value ?>" required>
      <span class="form__error"><?=$errors['lot-name'] ?></span>
    </div>
    <?php $classname = isset($errors['category']) ? "form__item--invalid" : "";
        $value = isset($adv['category']) ? $adv['category'] : ""; ?>
    <div class="form__item <?=$classname ?>">
      <label for="category">Категория</label>
      <select id="category" name="category" required>
        <option>Выберите категорию</option>
        <?php foreach($categories as $cat) { ?>  
          <option value="<?=$cat['id'] ?>"><?=$cat['name'] ?></option>
        <?php 
        } ?>
      </select>
      <span class="form__error"><?=$errors['category'] ?></span>
    </div>
  </div>
  <?php $classname = isset($errors['message']) ? "form__item--invalid" : "";
        $value = isset($adv['message']) ? $adv['message'] : ""; ?>
  <div class="form__item form__item--wide <?=$classname ?>">
    <label for="message">Описание</label>
    <textarea id="message" name="message" placeholder="Напишите описание лота" required><?=$value ?></textarea>
    <span class="form__error"><?=$errors['message'] ?></span>
  </div>
  <?php $classname = isset($errors['file']) ? "form__item--invalid" : ""; ?>
  <div class="form__item form__item--file <?=$classname ?>"> <!-- form__item--uploaded -->
    <label>Изображение</label>
    <div class="preview">
      <button class="preview__remove" type="button">x</button>
      <div class="preview__img">
        <img src="img/avatar.jpg" width="113" height="113" alt="Изображение лота">
      </div>
    </div>
    <div class="form__input-file">
      <input class="visually-hidden" type="file" name="lot-img" id="photo2" value="">
      <label for="photo2">
        <span>+ Добавить</span>
      </label>
    </div>
    <span class="form__error"><?=$errors['file'] ?></span>
  </div>
  <div class="form__container-three">
    <?php $classname = isset($errors['lot-rate']) ? "form__item--invalid" : "";
        $value = isset($adv['lot-rate']) ? $adv['lot-rate'] : ""; ?>
    <div class="form__item form__item--small <?=$classname ?>">
      <label for="lot-rate">Начальная цена</label>
      <input id="lot-rate" type="number" name="lot-rate" placeholder="0" value="<?=$value ?>" required>
      <span class="form__error"><?=$errors['lot-rate'] ?></span>
    </div>
    <?php $classname = isset($errors['lot-step']) ? "form__item--invalid" : "";
        $value = isset($adv['lot-step']) ? $adv['lot-step'] : ""; ?>
    <div class="form__item form__item--small <?=$classname ?>">
      <label for="lot-step">Шаг ставки</label>
      <input id="lot-step" type="number" name="lot-step" placeholder="0" value="<?=$value ?>" required>
      <span class="form__error"><?=$errors['lot-step'] ?></span>
    </div>
    <?php $classname = isset($errors['lot-date']) ? "form__item--invalid" : "";
        $value = isset($adv['lot-date']) ? $adv['lot-date'] : ""; ?>
    <div class="form__item <?=$classname ?>">
      <label for="lot-date">Дата окончания торгов</label>
      <input class="form__input-date" id="lot-date" type="date" name="lot-date" value="<?=$value ?>" required>
      <span class="form__error"><?=$errors['lot-date'] ?></span>
    </div>
  </div>
  <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
  <button type="submit" class="button">Добавить лот</button>
</form>