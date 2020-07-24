<?php
/*$params = [
    'host' => 'localhost',
    'dbname' => 'authorization',
    'user' => 'root',
    'password' => 'root1',
];
$dsn = "mysql:host={$params['host']};dbname={$params['dbname']}";
$db = new PDO($dsn, $params['user'], $params['password']);
$db->exec("set names utf8");
*/
$db = mysqli_connect("localhost", "root", "root1", "authorization");
