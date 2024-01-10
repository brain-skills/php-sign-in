<?php
require_once('config.php');
require_once('libs/Smarty.class.php');

$smarty = new Smarty();

session_start();

$user_authenticated = isset($_SESSION['user_id']) || isset($_COOKIE['user_id']);
$error_message = '';

// Проверяем существование таблицы 'user_groups', создаем ее, если не существует
$userGroupssql = "SHOW TABLES LIKE 'user_groups'";
$userGroupstable = $db_connect->query($userGroupssql);

if ($userGroupstable->num_rows === 0) {
    $createUserGroupsTableSQL = "
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
    ";

    if ($db_connect->query($createUserGroupsTableSQL) === TRUE) {
        // Таблица 'user_groups' создана успешно

        // Добавляем пять групп
        $insertUserGroupsSQL = "
            INSERT INTO `user_groups` (`id`,`name`,`create`,`read`,`update`,`del`,`vote`,`download`)
            VALUES 
                (1, 'Администраторы', 1, 1, 1, 1, 1, 1),
                (2, 'Модераторы', 0, 1, 1, 1, 1, 1),
                (3, 'Копирайтеры', 0, 1, 1, 0, 1, 1),
                (4, 'Пользователи', 0, 1, 0, 0, 1, 1),
                (5, 'Гости', 0, 1, 0, 0, 0, 0);
        ";

        if ($db_connect->query($insertUserGroupsSQL) !== TRUE) {
            // Ошибка при добавлении групп
            $error_message = "Ошибка при добавлении групп: " . $db_connect->error;
        }
    } else {
        // Ошибка при создании таблицы 'user_groups'
        $error_message = "Ошибка при создании таблицы 'user_groups': " . $db_connect->error;
    }
}

// Проверяем существование таблицы 'users', создаем ее, если не существует
$usersql = "SHOW TABLES LIKE 'users'";
$usertable = $db_connect->query($usersql);

if ($usertable->num_rows === 0) {
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS `users` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `email` VARCHAR(50) DEFAULT NULL,
            `password` varchar(255) DEFAULT NULL,
            `group_id` INT(11) DEFAULT 4, -- Default group: 'Пользователи'
            PRIMARY KEY (`id`),
            FOREIGN KEY (`group_id`) REFERENCES `user_groups` (`id`)
        ) ENGINE=INNODB DEFAULT CHARSET=utf8;
    ";

    if ($db_connect->query($createTableSQL) === TRUE) {
        // Таблица 'users' создана успешно

        // Добавляем двух пользователей
        $adminEmail = 'admin@admin.com';
        $adminPassword = password_hash('12345', PASSWORD_DEFAULT);
        $adminGroupId = 1; // 'Администраторы'

        $userEmail = 'user@user.com';
        $userPassword = password_hash('user123', PASSWORD_DEFAULT);
        $userGroupId = 4; // 'Пользователи'

        $insertAdminSQL = "INSERT INTO `users` (`email`, `password`, `group_id`) VALUES ('$adminEmail', '$adminPassword', $adminGroupId)";
        $insertUserSQL = "INSERT INTO `users` (`email`, `password`, `group_id`) VALUES ('$userEmail', '$userPassword', $userGroupId)";

        if ($db_connect->query($insertAdminSQL) !== TRUE || $db_connect->query($insertUserSQL) !== TRUE) {
            // Ошибка при добавлении пользователей
            $error_message = "Ошибка при добавлении пользователей: " . $db_connect->error;
        }
    } else {
        // Ошибка при создании таблицы 'users'
        $error_message = "Ошибка при создании таблицы 'users': " . $db_connect->error;
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

                header('Location: panel.php');
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

$smarty->assign('user_authenticated', $user_authenticated);
$smarty->display('login.tpl');
?>