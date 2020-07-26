<?php
/* отладка */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
/* отладка - конец */

ini_set ("session.use_trans_sid", true);
session_start();
include ('../lib/connect.php'); // Подключаемся к БД
include ('../lib/function_global.php'); // Подключаем библиотеку функций

// Если передана переменная action, «разавторизируем» пользователя
if (isset($_GET['action']) && $_GET['action'] == "out") {
    out();
}

// Проверим, авторизован-ли пользователь
if (isset($_SESSION['id']) 
  || (isset($_COOKIE['login']) && isset($_COOKIE['password']))
) {
    $rez = mysqli_query($db, "SELECT * FROM user WHERE id='{$_SESSION['id']}'");
    $row = mysqli_fetch_assoc($rez);
    $old_full_name = $row['full_name'];
    if (isset($_POST['update'])) {
        // Записываем в переменную результат работы функции updateCorrect(),
        // которая возвращает true, если введённые данные верны и false в противном случае
        $errors = updateCorrect($db);
        // Если данные верны, запишем их в базу данных
        if (empty($errors)) {
            $full_name = htmlspecialchars($_POST['full_name']);
            $new_password = $_POST['new_password'];
            $salt = mt_rand(100, 999);
            $tm = time();
            $new_password = md5(md5($new_password) . $salt);
            // Пишем данные в БД и авторизовываем пользователя
            if (mysqli_query($db, "
                UPDATE user
                SET full_name = '" . $full_name . "', 
                    password = '" . $new_password . "', 
                    salt = '" . $salt . "', 
                    last_act = '" . $tm . "'
                WHERE id = '" . $_SESSION['id'] . "'
            ")) {
                $login = $row['login'];
                setcookie ("password", md5($login . $new_password), time() + 60*60*12, '/'); // на 12 часов
            }
            $update_successful = true;
        }
    }
    include_once("template/personal-account.php");
} else {
    header('Location: http://localhost/authorization');
}