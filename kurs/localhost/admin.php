<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];

    $stmt = $pdo->prepare("INSERT INTO products (name, description) VALUES (?, ?)");
    $stmt->execute([$product_name, $product_description]);

    $_SESSION['success_message'] = 'Продукт успешно добавлен.';

    header("Location: admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_recipe'])) {
    $recipe_title = $_POST['recipe_title'];
    $recipe_instructions = $_POST['recipe_instructions'];

    $stmt = $pdo->prepare("INSERT INTO recipes (user_id, title, instructions) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $recipe_title, $recipe_instructions]);
    $recipe_id = $pdo->lastInsertId(); 

    foreach ($_POST['products'] as $product_id => $productData) {

        if (!empty($productData['selected'])) {
            $quantity = (float)$productData['quantity']; 

            $stmt = $pdo->prepare("INSERT INTO recipe_products (recipe_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$recipe_id, $product_id, $quantity]);
        }
    }

    $_SESSION['success_message'] = 'Рецепт успешно добавлен.';

    header("Location: admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_request'])) {
    $request_id = $_POST['request_id'];

    $stmtRequest = $pdo->prepare("SELECT * FROM recipe_requests WHERE id = ?");
    $stmtRequest->execute([$request_id]);
    $request = $stmtRequest->fetch(PDO::FETCH_ASSOC);

    if ($request) {
        $stmtInsertRecipe = $pdo->prepare("INSERT INTO recipes (user_id, title, instructions) VALUES (?, ?, ?)");
        $stmtInsertRecipe->execute([$request['user_id'], $request['title'], $request['instructions']]);
        $recipe_id = $pdo->lastInsertId(); 

        $stmtIngredients = $pdo->prepare("SELECT * FROM recipe_request_ingredients WHERE request_id = ?");
        $stmtIngredients->execute([$request_id]);
        $ingredients = $stmtIngredients->fetchAll(PDO::FETCH_ASSOC);

        foreach ($ingredients as $ingredient) {
            $quantity = (float)$ingredient['quantity']; 

            $stmt = $pdo->prepare("INSERT INTO recipe_products (recipe_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$recipe_id, $ingredient['product_id'], $quantity]);
        }

        $stmtDeleteIngredients = $pdo->prepare("DELETE FROM recipe_request_ingredients WHERE request_id = ?");
        $stmtDeleteIngredients->execute([$request_id]);

        $stmtDeleteRequest = $pdo->prepare("DELETE FROM recipe_requests WHERE id = ?");
        $stmtDeleteRequest->execute([$request_id]);

        $_SESSION['success_message'] = 'Заявка успешно принята. Рецепт добавлен.';
    } else {
        $_SESSION['error_message'] = 'Ошибка при обработке заявки.';
    }

    header("Location: admin.php");
    exit();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_request'])) {
    $request_id = $_POST['request_id'];

    // Отклонение заявки - удаление заявки и связанных ингредиентов
    $stmtDeleteIngredients = $pdo->prepare("DELETE FROM recipe_request_ingredients WHERE request_id = ?");
    $stmtDeleteIngredients->execute([$request_id]);

    $stmtDeleteRequest = $pdo->prepare("DELETE FROM recipe_requests WHERE id = ?");
    $stmtDeleteRequest->execute([$request_id]);

    $_SESSION['success_message'] = 'Заявка успешно отклонена.';

    header("Location: admin.php");
    exit();
}

// Получение списка продуктов
$stmt = $pdo->query("SELECT id, name FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$selectedProducts = isset($_POST['products']) ? $_POST['products'] : [];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include 'layout.php'; ?>

<div class="container">
    <h2>Админ</h2>

    <?php
    // Выводим сообщение об успехе, если оно есть
    if (isset($_SESSION['success_message'])) {
        echo '<p class="success">' . $_SESSION['success_message'] . '</p>';
        unset($_SESSION['success_message']); // Очищаем сообщение из сессии
    }
    ?>
    <button id="showProductForm">Добавить новый продукт</button>
    <div id="productForm" style="display: none;">
        <h3>Добавить новый продукт</h3>

        <form method="post">
            <label for="product_name">Название продукта:</label>
            <input type="text" id="product_name" name="product_name" required>

            <label for="product_description">Описание продукта:</label>
            <textarea id="product_description" name="product_description" rows="4" required></textarea>

            <button type="submit" name="add_product">Добавить продукт</button>
        </form>
    </div>
    <button id="showRecipeForm">Добавить новый рецепт</button>
    <div id="recipeForm" style="display: none;">
        <h3>Добавить новый рецепт</h3>
        <form method="post">
            <label for="recipe_title">Название рецепта:</label>
            <input type="text" id="recipe_title" name="recipe_title" required>

            <label for="recipe_instructions">Инструкции:</label>
            <textarea id="recipe_instructions" name="recipe_instructions" rows="4" required></textarea>

            <h4>Выберите продукты:</h4>
            <?php
            $stmt = $pdo->query("SELECT id, name FROM products");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($products as $product) {
                echo '<div class="product-checkbox">';
                echo '<input type="checkbox" id="product_' . $product['id'] . '" name="products[' . $product['id'] . '][selected]">';
                echo '<label for="product_' . $product['id'] . '">' . $product['name'] . '</label>';
                echo '<input type="number" id="quantity_' . $product['id'] . '" name="products[' . $product['id'] . '][quantity]" min="0" placeholder="Граммовка" style="display: none;">';
                echo '</div>';
            }
            ?>

            <button type="submit" name="add_recipe">Добавить рецепт</button>
        </form>
    </div>
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

    <h3>Заявки на добавление рецептов</h3>
    <div class="recipe-requests">
        <?php
        $stmtRequests = $pdo->query("SELECT recipe_requests.*, users.username AS user_name
                                     FROM recipe_requests
                                     LEFT JOIN users ON recipe_requests.user_id = users.id");
        $recipeRequests = $stmtRequests->fetchAll(PDO::FETCH_ASSOC);

        foreach ($recipeRequests as $request) {
            echo '<div class="recipe-request-card">';
            echo '<h4>' . $request['title'] . '</h4>';
            echo '<p>Пользователь: ' . $request['user_name'] . '</p>';
            echo '<p>Инструкции: ' . $request['instructions'] . '</p>';
            echo '<form method="post">';
            echo '<input type="hidden" name="request_id" value="' . $request['id'] . '">';
            echo '<button type="submit" name="accept_request">Принять</button>';
            echo '<button type="submit" name="reject_request">Отклонить</button>';
            echo '</form>';
            echo '</div>';
        }
        ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var productForm = document.getElementById('productForm');
        var recipeForm = document.getElementById('recipeForm');

        document.getElementById('showProductForm').addEventListener('click', function() {
            productForm.style.display = 'block';
            recipeForm.style.display = 'none';
        });

        document.getElementById('showRecipeForm').addEventListener('click', function() {
            productForm.style.display = 'none';
            recipeForm.style.display = 'block';
        });

        var checkboxes = document.querySelectorAll('.product-checkbox input[type="checkbox"]');
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                var input = this.parentElement.querySelector('input[type="number"]');
                input.style.display = this.checked ? 'block' : 'none';
            });
        });
    });
</script>
</body>
</html>
