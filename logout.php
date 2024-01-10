<?php
session_start();

// Удаляем все переменные сессии
$_SESSION = array();

// Уничтожаем сессию
session_destroy();

// Удаляем cookie
setcookie('user_id', '', time() - 3600);
setcookie('PHPSESSID', '', time() - 3600);

// Перенаправляем пользователя на страницу входа
header('Location: index.php');
exit();
?>