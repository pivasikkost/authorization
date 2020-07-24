<?php if (!empty($regged)) : ?>
  Вы успешно зарегистрировались! <br />
<?php elseif (!empty($errors)) : ?>
  <?php $errors1 = implode('<br />', $errors); ?>
  Ошибка валидации <br />
  <?= $errors1 ?>
<?php else : ?>
  <form method="post" action="index.php">
    Логин: <input id="login" type="text" name="login" /><br />
    Пароль: <input id="password" type="password" name="password" /><br />
    Подтверждение: <input id="password2" type="password" name="password2" /><br />
    Email: <input id="mail" type="text" name="mail" /><br />
    <label><input id="license" type="checkbox" name="license" value="ok" /> 
      Принимаю условия пользовательского соглашения<br />
    </label><br />
    <input type="submit" name="register" value="Регистрация">
  </form>
<?php endif; ?>