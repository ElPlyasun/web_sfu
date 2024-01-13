<?php
try{
    $pdo =new PDO('mysql:host=localhost;port=3306;dbname=recipes_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(Exception $e){
        die("Ошибка подключения к базе данных: " . $e->getMessage());
    }
?>

