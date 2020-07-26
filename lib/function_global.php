<?php 
/*
 * @return array Empty if validation correct
 */
function registrationCorrect($db)
{
  $errors = [];

  if ($_POST['login'] == "") {
      $errors[] = "пусто поле логин";
  }
  if ($_POST['password'] == "") {
      $errors[] = "пусто поле пароль";
  }
  if ($_POST['password2'] == "") {
      $errors[] = "пусто подтверждение пароля";
  }
  if ($_POST['mail'] == "") {
      $errors[] = "пусто поле email";
  }
  if ($_POST['license'] != "ok") {
      $errors[] = "не принято пользовательское соглашение";
  }
  if (!preg_match('/@/', $_POST['mail'])) {
      $errors[] = "некорректно поле email";
  }
  if (!preg_match('/^([a-zA-Z0-9])(\w|-|_)+([a-z0-9])$/is', $_POST['login'])) {
      $errors[] = "некорректно поле логин";
  }
  if (strlen($_POST['password']) < 5) {
      $errors[] = "длинна пароля менее 5 символов";
  }
  if ($_POST['password'] != $_POST['password2']) {
      $errors[] = "подтверждение пароля не совпадает с паролем";
  }
  $login = $_POST['login'];
  $rez = mysqli_query($db, "SELECT * FROM user WHERE login = '" . htmlspecialchars($login) . "'");
  if (mysqli_num_rows($rez) != 0) {
      $errors[] = "этот логин уже занят";
  }

  return $errors; 
}

/*
 * @return array Empty if validation correct
 */
function enter($db)
{
    $errors = array();
    // Если поля заполнены
    if ($_POST['login'] != "" && $_POST['password'] != "") {
        $login = $_POST['login'];
        $password = $_POST['password'];

        $rez = mysqli_query($db, "SELECT * FROM user WHERE login = '" . htmlspecialchars($login) . "'");
        if (mysqli_num_rows($rez) != 0) {
            $row = mysqli_fetch_assoc($rez);
            // Сравниваем хэшированный пароль из БД с хэшированными паролем, введённым пользователем и солью
            if (md5(md5($password) . $row['salt']) == $row['password']) {
                // Пишем логин и хэшированный пароль в cookie, также создаём переменную сессии
                setcookie ("login", $row['login'], time() + 60*60*12, '/'); // на 12 часов
                setcookie (
                    "password",
                    md5($row['login'] . $row['password']),
                    time() + 60*60*12, // на 12 часов
                    '/'
                );
                $_SESSION['id'] = $row['id']; // Записываем в сессию id пользователя

                $id = $_SESSION['id'];
                lastAct($db, $id);
                return $errors;
            } else {
                $errors[] = "Неверный пароль";
                return $errors;
            }
        } else {
            $errors[] = "Неверный логин и пароль";
            return $errors;
        }
    } else {
        $errors[] = "Поля не должны быть пустыми!";
        return $errors;
    }
}

/*
 * Setting the time of the last user activity
 * @return void
 */
function lastAct($db, $id)
{
    $tm = time();
    mysqli_query($db, "
        UPDATE user
        SET online = " . $tm . ", 
            last_act = " . $tm . ", 
        WHERE id = " . $id . ", 
    ");
}

/*
 * @return bool
 */
function is_authorized($db)
{
    ini_set("session.use_trans_sid", true);
    session_start();
    if (isset($_SESSION['id'])) {
        // Если сесcия есть
        if (isset($_COOKIE['login']) && isset($_COOKIE['password'])) {
            // Если cookie есть, то просто обновим время их жизни и вернём true 		{
            setcookie("login", "", time() - 60*60*12, '/');
            setcookie("password", "", time() - 60*60*12, '/');
            unset($_COOKIE['login']);
            unset($_COOKIE['password']);
            setcookie("login", $_COOKIE['login'], time() + 60*60*12, '/'); // на 12 часов
            setcookie("password", $_COOKIE['password'], time() + 60*60*12, '/'); // на 12 часов

            $id = $_SESSION['id'];
            lastAct($db, $id);
            return true;
        } else {
            // Есть ли в базе юзер с искомым id
            $rez = mysqli_query($db, "SELECT * FROM user WHERE id='{$_SESSION['id']}'");
            if (mysqli_num_rows($rez) != 0) {
                // Если получена строка, записываем её в ассоциативный массив
                $row = mysqli_fetch_assoc($rez);

                // Добавим cookie с логином и паролем, чтобы после перезапуска браузера сессия не слетала
                setcookie("login", $row['login'], time() + 60*60*12, '/'); // на 12 часов
                setcookie(
                    "password",
                    md5($row['login'] . $row['password']),
                    time() + 60*60*12, // на 12 часов
                    '/'
                );

                $id = $_SESSION['id'];
                lastAct($db, $id);
                return true;
            } else {
                return false;
            }
        }
    } else {
        // Если сессии нет, то проверим существование cookie. Если они существуют, то проверим их валидность по БД
        if (isset($_COOKIE['login']) && isset($_COOKIE['password'])) {
            // Если куки существуют.
            $rez = mysqli_query($db, "SELECT * FROM user WHERE login='{$_COOKIE['login']}'");
            $row = mysqli_fetch_assoc($rez);

            if (mysqli_num_rows($rez) != 0
                && md5($row['login'] . $row['password']) == $_COOKIE['password']
            ) {
                // Если логин и пароль нашлись в БД
                $_SESSION['id'] = $row['id']; // Записываем в сесиию id
                $id = $_SESSION['id'];
                lastAct($db, $id);
                return true;
            } else {
                // Если данные из cookie не подошли, то удаляем эти куки
                setcookie("login", "", time() - 60*60*12, '/');
                setcookie("password", "", time() - 60*60*12, '/');
                unset($_COOKIE['login']);
                unset($_COOKIE['password']);
                return false;
            }
        } else {
            // Если куки не существуют
            return false;
        }
    }
}

/*
 * @return bool
 */
function is_admin($db, $id)
{
    $rez = mysqli_query($db, "SELECT rights FROM user WHERE id = " . $id);
    $row = mysqli_fetch_assoc($rez);
    if (mysqli_num_rows($rez) != 0) {
        if ($row['rights'] == 1) {
            return true;
        }
    }

    return false;
}

/*
 * @return void
 */
function out()
{
    session_start();

    unset($_SESSION['id']); //удаляем переменную сессии
    setcookie("login", "", time() - 60*60*12, '/'); //удаляем cookie с логином
    setcookie("password", "", time() - 60*60*12, '/'); //удаляем cookie с паролем
    unset($_COOKIE['login']);
    unset($_COOKIE['password']);

    // Перенаправляем на главную страницу сайта
    //header('Location: http://' . $_SERVER['HTTP_HOST'] . '/');
    header('Location: http://localhost/authorization/');
}