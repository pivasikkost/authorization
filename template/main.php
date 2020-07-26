<?php if (!empty($user_id)) : ?>
    Вы авторизованы <br />
    <a href="index.php?action=out">Выход</a>
<?php else : ?>
    <form action="index.php" method="post">
        Логин: <input type="text" name="login" /> <br />
        Пароль: <input type="password" name="password" /> <br />
        <input type="submit" value="Войти" name="log_in" />
    </form> <br />
    <a href="registration">Зарегистрироваться</a>
<?php endif; ?>