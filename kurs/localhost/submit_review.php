<?php
include 'connection.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (isset($_POST['recipe_id'], $_POST['review_text'])) {
        $recipeId = $_POST['recipe_id'];
        $reviewText = $_POST['review_text'];

        $stmtInsertReview = $pdo->prepare("INSERT INTO reviews (user_id, recipe_id, text) VALUES (?, ?, ?)");
        $stmtInsertReview->execute([$_SESSION['user_id'], $recipeId, $reviewText]);

        echo '<p>Отзыв успешно отправлен.</p>';
    } else {
        echo '<p>Недостаточно данных для отправки отзыва.</p>';
    }
} else {
    echo '<p>Недопустимый метод запроса.</p>';
}
echo '<p>Перейти на <a href="index.php">главную страницу</a></p>'
?>

