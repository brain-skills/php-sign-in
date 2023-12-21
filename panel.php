<?php
session_start();

// Проверяем, что пользователь аутентифицирован
if (!isset($_SESSION['user_id']) && !isset($_COOKIE['user_id'])) {
    header('Location: index.php');
    exit();
}

// Если пользователь аутентифицирован через сессию
if (isset($_SESSION['user_id'])) {
    echo 'Success! Добро пожаловать, ' . $_SESSION['user_email'];
}
// Если пользователь аутентифицирован через cookie
elseif (isset($_COOKIE['user_id'])) {
    require_once('config.php');

    $user_id = $_COOKIE['user_id'];
    $query = "SELECT * FROM users WHERE id = $user_id";
    $result = mysqli_query($db_connect, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        // Сохраняем информацию о пользователе в сессии
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];

        echo 'Success! Добро пожаловать, ' . $_SESSION['user_email'];
    } else {
        // Проблема с cookie, перенаправляем на страницу входа
        header('Location: /');
        exit();
    }
}
?>