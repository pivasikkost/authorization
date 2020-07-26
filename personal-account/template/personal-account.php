<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <title>Личный кабинет</title>
    </head>
    <body>
        <h1>Личный кабинет</h1>>
        <?php if (!empty($update_successful)) : ?>
            Обновление прошло успешно <br />
            <a href="/authorization">На главную</a>
        <?php elseif (!empty($errors)) : ?>
          <?php $errors1 = implode('<br />', $errors); ?>
          Ошибка валидации <br />
          <?= $errors1 ?>
        <?php else : ?>
          <form method="post">
            ФИО: <input id="full_name" type="text" name="full_name"
                        value="<?= isset($_POST['full_name']) ? $_POST['full_name'] : $old_full_name ?>"
                 /><br />
            Старый пароль: <input id="old_password" type="password" name="old_password" /><br />
            Новый пароль: <input id="new_password" type="password" name="new_password" /><br />
            <input type="submit" name="update" value="Обновить информацию">
          </form>
          <a href="index.php?action=out">Выход</a>
        <?php endif; ?>
    </body>
</html>
