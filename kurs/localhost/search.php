<?php

session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ingredients'])) {

    $selectedIngredients = explode(',', $_POST['ingredients']);
    $selectedIngredients = array_map('trim', $selectedIngredients);

    $conditions = [];
    foreach ($selectedIngredients as $ingredient) {
        $conditions[] = "products.name LIKE '%$ingredient%'";
    }

    $sql = "SELECT DISTINCT recipes.*, users.username AS author_name
            FROM recipes
            LEFT JOIN users ON recipes.user_id = users.id
            LEFT JOIN recipe_products ON recipes.id = recipe_products.recipe_id
            LEFT JOIN products ON recipe_products.product_id = products.id
            WHERE " . implode(" AND ", $conditions);

    $stmt = $pdo->query($sql);
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог рецептов - Результаты поиска</title>
    <link rel="stylesheet" href="auth.css">
</head>
<body>
<?php require_once 'layout.php' ?>

<main>
    <form action="search.php" method="post">
        <label for="ingredients">Выберите продукты:</label>
        <input type="text" id="ingredients" name="ingredients" placeholder="Например, помидор, огурец...">
        <button type="submit">Искать рецепты</button>
    </form>

    <?php
    if (isset($recipes) && count($recipes) > 0) {
        echo '<h2>Рецепты, подходящие под выбранные продукты:</h2>';
        echo '<ul>';
        foreach ($recipes as $recipe) {
            echo '<li><a class="link" href="recipe.php?id=' . $recipe['id'] . '">' . $recipe['title'] . '</a> - Автор: ' . $recipe['author_name'] . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>По вашему запросу ничего не найдено.</p>';
    }
    ?>
</main>

<footer>
    <p>&copy; Рецепты по ингредиентам</p>
</footer>
<style>
    .link{
        color: #333333;
    }
</style>
</body>
</html>
