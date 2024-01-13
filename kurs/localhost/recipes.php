<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Рецепты</title>
    <link rel="stylesheet" href="recipes.css">
</head>
<body>
<?php include 'layout.php'; ?>
<?php include 'connection.php'; ?>

<main>
    <h3>Существующие рецепты</h3>
    <div class="recipe-grid">
        <?php
        $stmt = $pdo->query("SELECT recipes.*, users.username AS author_name, GROUP_CONCAT(products.name) AS product_names
                             FROM recipes
                             LEFT JOIN users ON recipes.user_id = users.id
                             LEFT JOIN recipe_products ON recipes.id = recipe_products.recipe_id
                             LEFT JOIN products ON recipe_products.product_id = products.id
                             GROUP BY recipes.id");
        $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($recipes as $recipe) {
            echo '<a href="recipe.php?id=' . $recipe['id'] . '" class="recipe-card">';
            echo '<h4>' . $recipe['title'] . '</h4>';
            echo '<p>Автор: ' . $recipe['author_name'] . '</p>';
            echo '</a>';
        }
        ?>
    </div>
</main>

<footer>
    <p>&copy; Рецепты по ингредиентам</p>
</footer>
</body>
</html>

