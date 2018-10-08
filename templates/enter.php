<nav class="nav">
  <ul class="nav__list container">
  <?php foreach ($categories as $key => $value) { ?>
    <li class="nav__item">
      <a href="all-lots.html"><?=$value['name'] ?></a>
  <?php 
  } ?>
  </ul>
</nav>
<?php $classname = isset($errors) ? "form--invalid" : ""; ?>
<form class="form container <?=$classname ?>" action="../enter.php" method="post"> <!-- form--invalid -->
  <h2>Вход</h2>
  <?php $classname = isset($errors['email']) ? "form__item--invalid" : "";
        $value = isset($loginForm['email']) ? $loginForm['email'] : ""; ?>
  <div class="form__item <?=$classname ?>"> <!-- form__item--invalid -->
    <label for="email">E-mail*</label>
    <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?=$value ?>" required>
    <span class="form__error"><?=$errors['email'] ?></span>
  </div>
  <?php $classname = isset($errors['password']) ? "form__item--invalid" : ""; ?>
  <div class="form__item form__item--last <?=$classname ?>">
    <label for="password">Пароль*</label>
    <input id="password" type="password" name="password" placeholder="Введите пароль" required>
    <span class="form__error"><?=$errors['password'] ?></span>
  </div>
  <button type="submit" class="button">Войти</button>
</form>