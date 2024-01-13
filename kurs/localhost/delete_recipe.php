<?php
include 'connection.php';

session_start();

if (isset($_GET['id'])) {
    $recipeId = $_GET['id'];

    $stmtDeleteProducts = $pdo->prepare("DELETE FROM recipe_products WHERE recipe_id = ?");
    $stmtDeleteProducts->execute([$recipeId]);

    if ($_SESSION['role_id'] === 1 || $recipe['user_id'] === $_SESSION['user_id']) {
        $stmtDeleteRecipe = $pdo->prepare("DELETE FROM recipes WHERE id = ?");
        $stmtDeleteRecipe->execute([$recipeId]);

        echo '<p>Рецепт успешно удален.</p>';
        header('Location: admin.php');
    } else {
        echo '<p>У вас нет прав для удаления этого рецепта.</p>';
    }
} else {
    echo '<p>Идентификатор рецепта не передан.</p>';
}
?>
