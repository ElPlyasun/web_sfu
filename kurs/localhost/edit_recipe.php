<?php
include 'connection.php';

session_start();

if (isset($_GET['id'])) {
    $recipeId = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = ?");
    $stmt->execute([$recipeId]);
    $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($recipe) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Редактировать рецепт</title>
            <link rel="stylesheet" href="edit_recipe.css"> 
        </head>
        <body>
        <?php include 'layout.php'; ?>

        <div class="container">
            <h2>Редактировать рецепт</h2>

            <form method="post" action="update_recipe.php">
                <input type="hidden" name="recipe_id" value="<?php echo $recipe['id']; ?>">

                <label for="edited_title">Название:</label>
                <input type="text" id="edited_title" name="edited_title" value="<?php echo $recipe['title']; ?>" required>

                <label for="edited_instructions">Описание:</label>
                <textarea id="edited_instructions" name="edited_instructions" rows="4" required><?php echo $recipe['instructions']; ?></textarea>

                <h3>Редактировать ингредиенты</h3>
                <?php
                $stmtProducts = $pdo->query("SELECT id, name FROM products");
                $products = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);

                $stmtIngredients = $pdo->prepare("SELECT product_id, quantity FROM recipe_products WHERE recipe_id = ?");
                $stmtIngredients->execute([$recipeId]);
                $ingredients = $stmtIngredients->fetchAll(PDO::FETCH_ASSOC);

                foreach ($products as $product) {
                    $isChecked = in_array($product['id'], array_column($ingredients, 'product_id'));
                    $quantity = $isChecked ? $ingredients[array_search($product['id'], array_column($ingredients, 'product_id'))]['quantity'] : 0;

                    echo '<label>';
                    echo '<input type="checkbox" name="ingredients[' . $product['id'] . '][selected]" ' . ($isChecked ? 'checked' : '') . '>';
                    echo $product['name'] . ' (Quantity in grams): ';
                    echo '<input type="number" name="ingredients[' . $product['id'] . '][quantity]" value="' . $quantity . '" min="0">';
                    echo '</label>';
                }
                ?>
                <button type="submit" name="update_recipe">Обновить рецепт</button>
            </form>


        </div>

        <footer>
            <p>&copy; Рецепты по ингредиентам</p>
        </footer>
        </body>
        </html>
        <?php
    } else {
        echo '<p>Рецепт не найден.</p>';
    }
} else {
    echo '<p>Идентификатор рецепта не передан.</p>';
}
?>
