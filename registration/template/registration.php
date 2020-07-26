<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <title>Регистрация</title>
    </head>
    <body>
        <h1>Регистрация</h1>
        <?php if (!empty($errors)) : ?>
            <?php $errors1 = implode('<br />', $errors); ?>
            Ошибка валидации <br />
            <?= $errors1 ?>
        <?php else : ?>
            <form method="post">
                Логин: <input id="login" type="text" name="login" /><br />
                Пароль: <input id="password" type="password" name="password" /><br />
                Подтверждение: <input id="password2" type="password" name="password2" /><br />
                Email: <input id="mail" type="text" name="mail" /><br />
                ФИО: <input id="full_name" type="text" name="full_name" /><br />
                <label><input id="agreement" type="checkbox" name="agreement" value="ok" />
                    Принимаю условия пользовательского соглашения<br />
                </label><br />
                <input type="submit" name="register" value="Регистрация">
            </form>
        <?php endif; ?>
    </body>
</html>