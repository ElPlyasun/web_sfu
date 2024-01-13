<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог рецептов</title>
    <link rel="stylesheet" href="auth.css">
</head>
<body>
<?php require_once 'layout.php' ?>

<main>
    <form action="search.php" method="post">
        <label for="ingredients">Выберите продукты:</label>
        <input type="text" id="ingredients" name="ingredients" placeholder="Например, масло, сахар, мука">
        <button type="submit">Искать рецепты</button>
    </form>
</main>

<footer>
    <p>&copy; Рецепты по ингредиентам</p>
</footer>
</body>
</html>
