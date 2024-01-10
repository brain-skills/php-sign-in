<?php
require_once('config.php');
require_once('libs/Smarty.class.php'); // Adjust the path based on your setup

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

if (!isset($_SESSION['user_id']) && !isset($_COOKIE['user_id'])) {
    header('Location: index.php');
    exit();
}

// Assign variables for Smarty template
$smarty->assign('user_email', htmlspecialchars($_SESSION['user_email']));

// Display Smarty template
$smarty->display('panel.tpl'); // Create panel.tpl with your HTML content
?>