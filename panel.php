<?php
require_once('config.php');
require_once('libs/Smarty.class.php');

$smarty = new Smarty();
session_start();
// if (session_status() == PHP_SESSION_ACTIVE) {
//     echo 'Текущая сессия активна.<br>';
//     echo 'ID сессии: ' . session_id() . '<br>';
//     echo 'Данные сессии:<br>';
//     print_r($_SESSION);
// } else {
//     echo 'Сессия не активна.';
// }

// Проверяем аутентификацию пользователя
if (!isset($_SESSION['user_id']) && !isset($_COOKIE['user_id'])) {
    header('Location: index.php');
    exit();
}

// Запрос к базе данных для получения пользователя по email
$query = "SELECT users.*, user_groups.name AS group_name FROM users LEFT JOIN user_groups ON users.group_id = user_groups.id WHERE email = ? LIMIT 1";
$stmt = mysqli_prepare($db_connect, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['user_email']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $smarty->assign('user_email', htmlspecialchars($_SESSION['user_email']));
        $smarty->assign('user_group', $user['group_name']);

        if ($user['group_name'] === 'Администраторы') {
            // Контент доступный только администратору
            $smarty->assign('admin_content', 'Этот контент доступен только администратору.');
        }
    } else {
        // Ошибка при получении данных пользователя
        $smarty->assign('error_message', "Ошибка при получении данных пользователя");
    }
} else {
    // Ошибка подготовки запроса
    $smarty->assign('error_message', "Ошибка подготовки запроса: " . mysqli_error($db_connect));
}

$smarty->display('panel.tpl');
?>