<?php
/* отладка */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
/* отладка - конец */

include ('lib/connect.php'); // Подключаемся к БД
include ('lib/function_global.php'); // Подключаем файл с глобальными функциями

// Проверяем, авторизирован юзер или нет
if (is_authorized($db)) {
    $user_id = $_SESSION['id']; // Если юзер авторизирован, присвоим переменной $user_id его id
    $admin = is_admin($db, $user_id); // Определяем, админ ли юзер
    // Редирект на личный кабинет
    header('Location: http://localhost/authorization/personal-account');
} else {
    // Если пользователь не авторизирован, то проверим, была ли нажата кнопка входа на сайт
    if (isset($_POST['log_in'])) {
        $error = enter($db); // Функция входа на сайт
        if (count($error) == 0) {
            // Если нет ошибок, авторизируем юзера
            $user_id = $_SESSION['id'];
            $admin = is_admin($db, $user_id);
            // Редирект на личный кабинет
            header('Location: http://localhost/authorization/personal-account');
        }
    }
    include('template/main.php'); // Подключаем файл с формой
}