<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
</head>
<body>
    {if $user_authenticated == false}
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
    {else}
        <p>Успешно! Добро пожаловать, {$user_email}</p>
        <p>Вы вошли как {$user_group}.</p>
        {if $user_group eq 'Администраторы'}
            <p><a href="admin.php">Панель управления</a></p>
            <p>Дополнительный контент для администраторов.</p>
        {elseif $user_group eq 'Модераторы'}
            <p>Дополнительный контент для модераторов.</p>
        {elseif $user_group eq 'Копирайтеры'}
            <p>Дополнительный контент для копирайтеров.</p>
        {elseif $user_group eq 'Пользователи'}
            <p>Дополнительный контент для пользователей.</p>
        {elseif $user_group eq 'Гости'}
            <p>Дополнительный контент для гостей.</p>
        {else}
            <p>Неизвестная группа пользователей.</p>
        {/if}
    
        <a href="logout.php">Выйти</a>
    {/if}
</body>
</html>