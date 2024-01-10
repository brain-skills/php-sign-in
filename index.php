<?php
require_once('config.php');
require_once('libs/Smarty.class.php');

$smarty = new Smarty();

session_start();

$user_authenticated = isset($_SESSION['user_id']) || isset($_COOKIE['user_id']);
$error_message = '';

// Check if the 'users' table exists, create if not
$usersql = "SHOW TABLES LIKE 'users'";
$usertable = $db_connect->query($usersql);

if ($usertable->num_rows === 0) {
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS `users` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `email` VARCHAR(50) DEFAULT NULL,
            `password` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=INNODB DEFAULT CHARSET=utf8;
    ";

    if ($db_connect->query($createTableSQL) === TRUE) {
        // Table created successfully

        // Add a default user with a hashed password
        $email = 'user@user.com';
        $password = password_hash('12345', PASSWORD_DEFAULT);

        $insertUserSQL = "INSERT INTO `users` (`email`, `password`) VALUES ('$email', '$password')";
        if ($db_connect->query($insertUserSQL) !== TRUE) {
            // Error adding the default user
            $error_message = "Error adding default user";
        }
    } else {
        // Error creating the 'users' table
        $error_message = "Error creating 'users' table";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = mysqli_prepare($db_connect, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            setcookie('user_id', $user['id'], time() + 3600 * 24 * 7);

            header('Location: panel.php');
            exit();
        } else {
            $error_message = "Неверный email или пароль";
        }
    } else {
        $error_message = "Неверный email или пароль";
    }

    if (isset($error_message)) {
        $smarty->assign('error_message', $error_message);
    }
}

$smarty->assign('user_authenticated', $user_authenticated);
$smarty->display('login.tpl');
?>