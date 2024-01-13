<?php
include 'connection.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_recipe'])) {
    if (isset($_POST['recipe_id'], $_POST['edited_title'], $_POST['edited_instructions'], $_POST['ingredients'])) {
        $recipeId = $_POST['recipe_id'];
        $editedTitle = $_POST['edited_title'];
        $editedInstructions = $_POST['edited_instructions'];
        $ingredients = $_POST['ingredients'];

        $stmtCheckOwnership = $pdo->prepare("SELECT user_id FROM recipes WHERE id = ?");
        $stmtCheckOwnership->execute([$recipeId]);
        $recipeOwner = $stmtCheckOwnership->fetch(PDO::FETCH_ASSOC);

        if ($_SESSION['role_id'] === 1 || $recipeOwner['user_id'] === $_SESSION['user_id']) {
            $stmtUpdateRecipe = $pdo->prepare("UPDATE recipes SET title = ?, instructions = ? WHERE id = ?");
            $stmtUpdateRecipe->execute([$editedTitle, $editedInstructions, $recipeId]);

            $stmtDeleteIngredients = $pdo->prepare("DELETE FROM recipe_products WHERE recipe_id = ?");
            $stmtDeleteIngredients->execute([$recipeId]);

            foreach ($ingredients as $productId => $ingredientData) {
                if (isset($ingredientData['selected']) && $ingredientData['selected'] == 'on' && isset($ingredientData['quantity'])) {
                    $stmtInsertIngredient = $pdo->prepare("INSERT INTO recipe_products (recipe_id, product_id, quantity) VALUES (?, ?, ?)");
                    $stmtInsertIngredient->execute([$recipeId, $productId, $ingredientData['quantity']]);
                }
            }

            echo '<p>Рецепт успешно обновлен.</p>';
        } else {
            echo '<p>У вас нет прав для редактирования этого рецепта.</p>';
        }
    } else {
        echo '<p>Недостаточно данных для обновления рецепта.</p>';
    }
} else {
    echo '<p>Недопустимый метод запроса.</p>';
}
echo '<p>Перейти на <a href="index.php">главную страницу</a></p>'
?>
