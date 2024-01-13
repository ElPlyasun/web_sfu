<?php
include 'connection.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_recipe'])) {
    $userId = $_SESSION['user_id'];
    $title = $_POST['title'];
    $instructions = $_POST['instructions'];

    $isAdmin = ($_SESSION['role_id'] === 1);

    if (!$isAdmin) {
        $stmtRequest = $pdo->prepare("INSERT INTO recipe_requests (user_id, title, instructions) VALUES (?, ?, ?)");
        $stmtRequest->execute([$userId, $title, $instructions]);
        $recipeRequestId = $pdo->lastInsertId();

        if (isset($_POST['ingredients']) && is_array($_POST['ingredients'])) {
            foreach ($_POST['ingredients'] as $productId => $ingredientData) {
                $quantity = floatval($ingredientData['quantity']);
                $stmtIngredients = $pdo->prepare("INSERT INTO recipe_request_ingredients (request_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmtIngredients->execute([$recipeRequestId, $productId, $quantity]);
            }
        }

        echo '<p>Заявка на добавление рецепта успешно отправлена.</p>';
    } else {

        $stmtRecipe = $pdo->prepare("INSERT INTO recipes (user_id, title, instructions) VALUES (?, ?, ?)");
        $stmtRecipe->execute([$userId, $title, $instructions]);
        $recipeId = $pdo->lastInsertId();

        if (isset($_POST['ingredients']) && is_array($_POST['ingredients'])) {
            foreach ($_POST['ingredients'] as $productId => $ingredientData) {
                $quantity = floatval($ingredientData['quantity']);
                $stmtProducts = $pdo->prepare("INSERT INTO recipe_products (recipe_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmtProducts->execute([$recipeId, $productId, $quantity]);
            }
        }


    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавление рецепта</title>
    <link rel="stylesheet" href="add_recipe.css">
</head>
<body>
<?php include 'layout.php'; ?>
<div class="container">
    <h2>Добавление рецепта</h2>
    <form method="post" action="add_recipe.php">
        <label for="title">Название:</label>
        <input type="text" id="title" name="title" required>

        <label for="instructions">Описание:</label>
        <textarea id="instructions" name="instructions" rows="4" required></textarea>

        <h3>Ингредиенты:</h3>
        
        <?php
        // Получение списка продуктов
        $stmtProducts = $pdo->query("SELECT id, name FROM products");
        $products = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $product) {
            echo '<label>';
            echo '<input type="checkbox" name="ingredients[' . $product['id'] . '][selected]">';
            echo $product['name'] . ' (грамм): ';
            echo '<input type="number" name="ingredients[' . $product['id'] . '][quantity]" min="0">';
            echo '</label>';
        }
        ?>
        </div>
        <button type="submit" name="submit_recipe">Отправить рецепт</button>
    </form>
</div>
<footer>
    <p>&copy; Рецепты по ингредиентам</p>
</footer>
</body>
</html>
