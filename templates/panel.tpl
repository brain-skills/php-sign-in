<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authenticated Panel</title>
</head>
<body>
    Успешно! Добро пожаловать, {$user_email}
    <p>Вы вошли как {$user_group}.</p>
    {if $user_group eq 'Администраторы'}
        <p>Дополнительный контент для администраторов.</p>
    {elseif $user_group eq 'Модераторы'}
        <p>Дополнительный контент для модераторов.</p>
    {elseif $user_group eq 'Копирайтеры'}
        <p>Дополнительный контент для копирайтеров.</p>
    {elseif $user_group eq 'Пользователи'}
        <p>Дополнительный контент для пользователей.</p>
    {elseif $user_group eq 'Гости'}
        <p>Дополнительный контент для гостей.</p>
    {/if}
    
    <a href="logout.php">Выйти</a>
</body>
</html>