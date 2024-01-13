<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог рецептов</title>
    <link rel="stylesheet" href="/css/layout.css">
</head>
<body>
<nav>
    <ul>
        <li><a href="index.php">Главная</a></li>
        <li><a href="recipes.php">Рецепты</a></li>
        <li><a href="add_recipe.php">Добавить рецепт</a></li>
        <?php
        session_start();

        if (isset($_SESSION['user_id'])) {
            if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
                echo '<li><a href="admin.php">Админ</a></li>';
            }
            echo '<li><a href="profile.php">' . $_SESSION['username'] . '</a></li>';
            echo '<li><a href="logout.php">Выйти</a></li>';
        } else {
            echo '<li><a href="login.php">Войти</a></li>';
            echo '<li><a href="register.php">Регистрация</a></li>';
        }
        ?>
    </ul>
</nav>
</body>
</html>

