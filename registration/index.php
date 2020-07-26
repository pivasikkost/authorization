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

// Проверим, быть может пользователь уже авторизирован. Если это так, перенаправим его на главную страницу сайта
if (isset($_SESSION['id']) 
  || (isset($_COOKIE['login']) && isset($_COOKIE['password']))
) {
    header('Location: http://localhost/authorization');
} else {
    // Если была нажата кнопка регистрации, проверим данные на корректность и, 
    // если данные введены и введены правильно, добавим запись с новым пользователем в БД
    if (isset($_POST['register'])) {
        // Записываем в переменную результат работы функции registrationCorrect(), 
        // которая возвращает true, если введённые данные верны и false в противном случае
        $errors = registrationCorrect($db);
        // Если данные верны, запишем их в базу данных
        if (empty($errors)) {
            $login = htmlspecialchars($_POST['login']);
            $password = $_POST['password'];
            $mail = htmlspecialchars($_POST['mail']);
            $full_name = htmlspecialchars($_POST['full_name']);
            $salt = mt_rand(100, 999);
            $tm = time();
            $password = md5(md5($password) . $salt);
            // Пишем данные в БД и авторизовываем пользователя
            if (mysqli_query($db, "
                INSERT INTO user (login, password, salt, mail_reg, mail, reg_date, last_act, full_name) 
                VALUES ('" . $login . "', '" . $password . "', '" . $salt . "', '" . $mail . "', '" . $mail . "', '" . $tm . "', '" . $tm . "', '" . $full_name . "')
            ")) {
                setcookie ("login", $login, time() + 60*60*12, '/'); // на 12 часов
                setcookie ("password", md5($login.$password), time() + 60*60*12, '/'); // на 12 часов
                $rez = mysqli_query($db, "SELECT * FROM user WHERE login = " . $login);
                $row = mysqli_fetch_assoc($rez);
                $_SESSION['id'] = $row['id'];
                // Редирект на главную
                header('Location: http://localhost/authorization/');
            }
        } else {
            // Подключаем шаблон в случае некорректности данных
            include_once ("template/registration.php"); 
        }
    } else {
        // Подключаем шаблон в случае если кнопка регистрации нажата не была,
        // то есть, пользователь только перешёл на страницу регистрации
        include_once ("template/registration.php"); 
    }
}