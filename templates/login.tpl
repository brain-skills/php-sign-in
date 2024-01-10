<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
</head>
<body>
    {if $user_authenticated == false}
        <h3>Форма авторизации</h3>
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
    {else}
        <p>Успешно! Добро пожаловать, <b>{$user_email}</b></p>
        <p>Вы вошли как <b>{$user_group}</b>.</p>
        {if $user_group eq 'Администраторы'}
            <p>Контент для администраторов.</p>
        {elseif $user_group eq 'Модераторы'}
            <p>Контент для модераторов.</p>
        {elseif $user_group eq 'Копирайтеры'}
            <p>Контент для копирайтеров.</p>
        {elseif $user_group eq 'Пользователи'}
            <p>Контент для пользователей.</p>
        {elseif $user_group eq 'Гости'}
            <p>Контент для гостей.</p>
        {else}
            <p>Неизвестная группа пользователей.</p>
        {/if}
    
        <a href="logout.php">Выйти</a> - <a href="admin.php">Панель управления</a>
    {/if}
</body>
</html>