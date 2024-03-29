<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
</head>
<body>
    {if isset($user_group_id) && $user_group_id <= 3}
        Добро пожаловать, {$user_email}
        <p>Вы вошли как {$user_group}.</p>
        <a href="index.php">На главную</a> - <a href="logout.php">Выйти</a>
    {else}
        <h3>Вход в Панель управления</h3>
        <!-- Форма для незарегистрированных пользователей -->
        <form method="POST" action="index.php">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <br>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <br>
            <button type="submit">Войти</button>
            {if isset($error_message)}
                <p>{$error_message}</p>
            {/if}
        </form>
        <br>
        <a href="index.php">На главную</a>
    {/if}
</body>
</html>