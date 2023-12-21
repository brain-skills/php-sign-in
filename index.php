<?php
session_start();
require_once('config.php');

// Формируем запрос на проверку существования таблицы
$usersql = "SHOW TABLES LIKE 'users'";
$usertable = $db_connect->query($usersql);

// Проверяем результат запроса
if ($usertable->num_rows > 0) {
    // Если таблица в БД существует выполняем код:
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];
    
        $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
        $result = mysqli_query($db_connect, $query);
    
        if ($result && mysqli_num_rows($result) > 0) {
            // Пользователь найден
            $user = mysqli_fetch_assoc($result);
    
            // Сохраняем информацию о пользователе в сессии
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
    
            // Создаем cookie для автоматического входа
            setcookie('user_id', $user['id'], time() + 3600 * 24 * 7); // Например, на неделю
    
            // Перенаправляем на panel.php
            header('Location: panel.php');
            exit();
        } else {
            // Неверные учетные данные
            $error_message = "Неверный email или пароль";
        }
    }
} else {
    // Если таблицы в БД не существует выполняем код:
    $sql = "
        CREATE TABLE if NOT EXISTS `users` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `email` VARCHAR(50) DEFAULT NULL,
            `password` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=INNODB DEFAULT CHARSET=utf8;

        INSERT INTO `users` (`id`,`email`,`password`) VALUES (1, 'geor.ka@mail.ru', '12345');
        INSERT INTO `users` (`id`,`email`,`password`) VALUES (2, 'marixurcidze93@mail.ru', '12345');
        INSERT INTO `users` (`id`,`email`,`password`) VALUES (3, 'user@user.com', '12345');
    ";
    $addTable = mysqli_multi_query($db_connect, $sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>php-sign-in</title>
</head>
<body>
    <form method="POST">
        <div class="row">
            <div class="col-4">
                <input type="text" class="form-control mt-2 mb-2" name="email" id="email" placeholder="E-mail" value="user@user.com">
            </div>
            <div class="col-4">
                <input type="password" class="form-control mt-2 mb-2" name="password" id="password" placeholder="пароль" value="12345">
            </div>
            <div class="col-4">
                <button type="submit" class="btn btn-success mt-2 mb-2">Войти</button>
            </div>
        </div>
    </form>

    <?php
    if (isset($error_message)) {
        echo "<p>$error_message</p>";
    }

    // Устанавливаем cookie с именем 'PHPSESSID' на удаление
    setcookie('PHPSESSID', '', time() - 3600, '/');

    // Смотрим массив всех записей в cookie
    var_dump($_COOKIE);
    ?>
</body>
</html>