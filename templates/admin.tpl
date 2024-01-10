<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
</head>
<body>
    {if isset($user_group_id) && $user_group_id == 1}
        Добро пожаловать, {$user_email}
        <p>Вы вошли как {$user_group}.</p>
        <a href="logout.php">Выйти</a>
    {else}
        <p>Уважаемый {$user_email}, у вас нет доступа к административному разделу!</p>
        <p>Вы вошли как {$user_group}.</p>
        <form method="POST" action="index.php">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Войти</button>
            
            {if isset($error_message)}
                <p>{$error_message}</p>
            {/if}
        </form>
        <a href="index.php">Вернуться на главную страницу</a>
    {/if}
</body>
</html>