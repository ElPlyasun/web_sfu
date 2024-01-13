<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Рецепт</title>
    <link rel="stylesheet" href="recipe.css">
</head>
<body>

<?php
include 'layout.php';
include 'connection.php';

session_start();

if (isset($_GET['id'])) {
    $recipeId = $_GET['id'];

    $stmt = $pdo->prepare("SELECT recipes.*, users.username AS author_name
                           FROM recipes
                           LEFT JOIN users ON recipes.user_id = users.id
                           WHERE recipes.id = ?");
    $stmt->execute([$recipeId]);
    $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($recipe) {
        ?>
        <div>
            <h2><?php echo $recipe['title']; ?></h2>
            <p>Автор: <?php echo $recipe['author_name']; ?></p>
            <p>Инструкции: <?php echo $recipe['instructions']; ?></p>

            <?php
            $stmtIngredients = $pdo->prepare("SELECT products.name AS product_name, recipe_products.quantity
                                              FROM recipe_products
                                              LEFT JOIN products ON recipe_products.product_id = products.id
                                              WHERE recipe_products.recipe_id = ?");
            $stmtIngredients->execute([$recipeId]);
            $ingredients = $stmtIngredients->fetchAll(PDO::FETCH_ASSOC);

            if ($ingredients) {
                echo '<h3>Ингредиенты:</h3>';
                echo '<ul>';
                foreach ($ingredients as $ingredient) {
                    echo '<li>' . $ingredient['product_name'] . ' - ' . $ingredient['quantity'] . ' грамм</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>Отсутствуют информация об ингредиентах.</p>';
            }
            ?>

            <?php
            $stmtReviews = $pdo->prepare("SELECT users.username AS reviewer_name, reviews.text
                                          FROM reviews
                                          LEFT JOIN users ON reviews.user_id = users.id
                                          WHERE reviews.recipe_id = ?");
            $stmtReviews->execute([$recipeId]);
            $reviews = $stmtReviews->fetchAll(PDO::FETCH_ASSOC);

            if ($reviews) {
                echo '<h3>Отзывы:</h3>';
                echo '<ul>';
                foreach ($reviews as $review) {
                    echo '<li><strong>' . $review['reviewer_name'] . ':</strong> ' . $review['text'] . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>Отсутствуют отзывы для данного рецепта.</p>';
            }

            if (isset($_SESSION['role_id']) && ($_SESSION['role_id'] === 1 || $recipe['user_id'] === $_SESSION['user_id'])) {
                echo '<a class="link" href="edit_recipe.php?id=' . $recipe['id'] . '">Редактировать рецепт  </a>';
                echo '<a class="link" href="delete_recipe.php?id=' . $recipe['id'] . '">Удалить рецепт</a>';
            }
            ?>

            <?php
            if (isset($_SESSION['user_id'])) {
                ?>
                <h3>Оставить отзыв:</h3>
                <form method="post" action="submit_review.php">
                    <input type="hidden" name="recipe_id" value="<?php echo $recipe['id']; ?>">

                    <label for="review_text">Текст отзыва:</label>
                    <textarea id="review_text" name="review_text" rows="4" required></textarea>

                    <button type="submit" name="submit_review">Отправить отзыв</button>
                </form>
                <?php
            } else {
                echo '<p>Для оставления отзыва войдите в свой аккаунт.</p>';
            }
            ?>
        </div>
        <?php
    } else {
        echo '<p>Рецепт не найден.</p>';
    }
} else {
    echo '<p>Идентификатор рецепта не передан.</p>';
}
?>

<footer>
    <p>&copy; Рецепты по ингредиентам</p>
</footer>

</body>
</html>
