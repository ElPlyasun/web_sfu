<?php
include 'connection.php';

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Обработка формы регистрации
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных из формы
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Валидация данных
    if (empty($username) || empty($password) || empty($email)) {
        $error_message = "Заполните все поля!";
    } else {
        // Проверка уникальности email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error_message = "Пользователь с таким адресом электронной почты уже зарегистрирован.";
        } else {
            // Хеширование пароля перед сохранением в базу данных
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Вставка данных в базу данных
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role_id) VALUES (?, ?, ?, 2)");
            $stmt->execute([$username, $hashed_password, $email]);

            // Перенаправление на страницу входа
            header("Location: login.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="auth.css">
</head>
<body>
<?php include 'layout.php'; ?>

<div class="container">
    <h2>Регистрация</h2>
    <?php
    if (isset($error_message)) {
        echo '<p class="error">' . $error_message . '</p>';
    }
    ?>
    <form method="post">
        <label for="username">Имя пользователя:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <button type="submit">Зарегистрироваться</button>
    </form>
</div>


</body>
</html>
