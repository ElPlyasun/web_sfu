<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="auth.css">
</head>
<body>

<?php
include 'layout.php';
include 'connection.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    $stmtUser = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmtUser->execute([$userId]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_username'])) {
            $newUsername = trim($_POST['new_username']);
            if (!empty($newUsername)) {
                $stmtUpdateUsername = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
                $stmtUpdateUsername->execute([$newUsername, $userId]);

                $_SESSION['username'] = $newUsername;


            } else {
                echo '<p>Введите новое имя пользователя.</p>';
            }
        }

        echo '<div class="user-info">';
        echo '<h2>Профиль пользователя: ' . $_SESSION['username'] . '</h2>';
        echo '<p>Name: ' . $_SESSION['username'] . '</p>';
        echo '<p>Email: ' . $user['email'] . '</p>';
        echo '<p>Роль: ' . ($user['role_id'] == 1 ? 'Администратор' : 'Пользователь') . '</p>';
        echo '</div>';

        echo '<form method="post" action="profile.php">';
        echo '<label for="new_username">Новое имя пользователя:</label>';
        echo '<input type="text" id="new_username" name="new_username" required>';
        echo '<button type="submit" name="update_username">Обновить имя пользователя</button>';
        echo '</form>';

        $stmtRecipes = $pdo->prepare("SELECT recipes.*, users.username AS author_name
                                      FROM recipes
                                      LEFT JOIN users ON recipes.user_id = users.id
                                      WHERE recipes.user_id = ?");
        $stmtRecipes->execute([$userId]);
        $userRecipes = $stmtRecipes->fetchAll(PDO::FETCH_ASSOC);

        if ($userRecipes) {
            echo '<h3>Рецепты пользователя:</h3>';
            echo '<div class="recipe-cards">';
            foreach ($userRecipes as $recipe) {
                echo '<div class="recipe-card">';
                echo '<h4>' . $recipe['title'] . '</h4>';

                echo '<a class="link" href="recipe.php?id=' . $recipe['id'] . '">Подробнее</a>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<p>Пользователь еще не добавил ни одного рецепта.</p>';
        }
    } else {
        echo '<p>Пользователь не найден.</p>';
    }
} else {
    echo '<p>Пользователь не авторизован.</p>';
}
?>

<footer>
    <p>&copy; Рецепты по ингредиентам</p>
</footer>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f2f2f2;
    }

    .layout-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        margin-top: 20px;
    }

    .user-info {

        color: #333333;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .recipe-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
    }

    .recipe-card {
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        padding: 10px;
    }

    .link {
        color: #3498db;
        text-decoration: none;
        display: inline-block;
        margin-top: 10px;
    }
    form {
        margin-top: 20px;
        display: flex;
        flex-direction: column;
    }

    label {
        margin-bottom: 5px;
    }

    input {
        padding: 5px;
        margin-bottom: 10px;
    }

    button {
        padding: 8px;
        background-color: #3498db;
        color: #fff;
        border: none;
        cursor: pointer;
    }

    button:hover {
        background-color: #2980b9;
    }
</style>
</body>
</html>
