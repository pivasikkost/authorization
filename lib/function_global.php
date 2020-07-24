<?php 
/*
 * @return array Empty if validation correct
 */
function registrationCorrect($db) {
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
  $rez = mysqli_query($db, "SELECT * FROM user WHERE login = " . htmlspecialchars($login));
  if (mysqli_num_rows($rez) != 0) {
      $errors[] = "этот логин уже занят";
  }

  return $errors; 
}