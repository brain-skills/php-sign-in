<?php
require_once('config.php');
require_once('libs/Smarty.class.php');

$smarty = new Smarty();

session_start();

$user_authenticated = isset($_SESSION['user_id']) || isset($_COOKIE['user_id']);
$error_message = '';

// Хешированные пароли
$adminPass = password_hash('12345', PASSWORD_DEFAULT);
$moderPass = password_hash('12345', PASSWORD_DEFAULT);
$writerPass = password_hash('12345', PASSWORD_DEFAULT);
$userPass = password_hash('12345', PASSWORD_DEFAULT);

// SQL-скрипт для создания таблиц и добавления групп и пользователей
$setupSQL = "
    -- Создание таблицы 'user_groups'
    CREATE TABLE IF NOT EXISTS `user_groups` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(50) DEFAULT NULL,
        `create` TINYINT(1) DEFAULT NULL,
        `read` TINYINT(1) DEFAULT NULL,
        `update` TINYINT(1) DEFAULT NULL,
        `del` TINYINT(1) DEFAULT NULL,
        `vote` TINYINT(1) DEFAULT NULL,
        `download` TINYINT(1) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=INNODB DEFAULT CHARSET=utf8;

    -- Добавление групп
    INSERT INTO `user_groups` (`id`,`name`,`create`,`read`,`update`,`del`,`vote`,`download`)
    VALUES 
        (1, 'Администраторы', 1, 1, 1, 1, 1, 1),
        (2, 'Модераторы', 0, 1, 1, 1, 1, 1),
        (3, 'Копирайтеры', 0, 1, 1, 0, 1, 1),
        (4, 'Пользователи', 0, 1, 0, 0, 1, 1),
        (5, 'Гости', 0, 1, 0, 0, 0, 0);

    -- Создание таблицы 'users'
    CREATE TABLE IF NOT EXISTS `users` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `email` VARCHAR(50) DEFAULT NULL,
        `password` varchar(255) DEFAULT NULL,
        `group_id` INT(11) DEFAULT 4, -- Default group: 'Пользователи'
        PRIMARY KEY (`id`),
        FOREIGN KEY (`group_id`) REFERENCES `user_groups` (`id`)
    ) ENGINE=INNODB DEFAULT CHARSET=utf8;

    -- Добавление пользователей
    INSERT INTO `users` (`email`, `password`, `group_id`) VALUES 
        ('admin@admin.com', '$adminPass', 1),
        ('moder@moder.com', '$moderPass', 2),
        ('writer@writer.com', '$writerPass', 3),
        ('user@user.com', '$userPass', 4);
";

// Разделение запросов и их выполнение
$queries = explode(';', $setupSQL);
foreach ($queries as $query) {
    if (!empty(trim($query))) {
        if (!$db_connect->query($query)) {
            // Ошибка при выполнении запроса
            $error_message = "Ошибка при выполнении запроса: " . $db_connect->error;
            break;
        }
    }
}

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT users.*, user_groups.name AS group_name FROM users LEFT JOIN user_groups ON users.group_id = user_groups.id WHERE email = ? LIMIT 1";
    $stmt = mysqli_prepare($db_connect, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_group'] = $user['group_name'];
                setcookie('user_id', $user['id'], time() + 3600 * 24 * 7);
                // открываем профиль
                header('Location: index.php');
                exit();
            } else {
                $error_message = "Неверный email или пароль";
            }
        } else {
            $error_message = "Неверный email или пароль";
        }
    } else {
        $error_message = "Ошибка подготовки запроса: " . mysqli_error($db_connect);
    }
    if (isset($error_message)) {
        $smarty->assign('error_message', $error_message);
    }
}

// После успешной аутентификации
if ($user_authenticated) {
    // Получаем группу пользователя
    $query = "SELECT user_groups.name AS group_name FROM users LEFT JOIN user_groups ON users.group_id = user_groups.id WHERE users.id = ? LIMIT 1";
    $stmt = mysqli_prepare($db_connect, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            // Присваиваем переменные Smarty
            $smarty->assign('user_email', $_SESSION['user_email']);
            $smarty->assign('user_group', $user['group_name']);
        }
    }
}

$smarty->assign('user_authenticated', $user_authenticated);
$smarty->display('login.tpl');
?>