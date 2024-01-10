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
        <p>Вы уже аутентифицированы.</p>
    {/if}
</body>
</html>