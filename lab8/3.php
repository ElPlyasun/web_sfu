<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="style3.css">
    
</head>
<body>

<?php if (isset($_POST["submit-form"]))
    {
        echo $_POST("input-name");
    }
?>
<form action="/Labs/lab8/3.php" method="post">
    <label for="name">
    <h2>Регистрация пользователя</h2> <br>
    ФИО:</label>
    <input type="text" name="name" required><br>

    <label for="dob">Дата рождения:</label>
    <input type="date" name="dob" required><br>

    <label for="username">Логин:</label>
    <input type="text" name="username" required><br>

    <label for="password">Пароль:</label>
    <input type="password" name="password" required><br>

    <input type="submit" value="Зарегистрироваться">
</form>

</body>
</html>