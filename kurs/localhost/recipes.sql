-- Создание базы данных
CREATE DATABASE IF NOT EXISTS recipes_db;

-- Использование созданной базы данных
USE recipes_db;

-- Таблица продуктов
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT
);

-- Таблица ролей
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

-- Добавление ролей
INSERT INTO roles (name) VALUES ('admin'), ('user');

-- Таблица пользователей
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) unique NOT NULL,
    role_id INT,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Таблица рецептов
CREATE TABLE IF NOT EXISTS recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(255) NOT NULL,
    instructions TEXT
);

-- Добавление внешнего ключа
ALTER TABLE recipes ADD FOREIGN KEY (user_id) REFERENCES users(id);

-- Таблица ингредиентов
CREATE TABLE IF NOT EXISTS ingredients (
    recipe_id INT,
    product_id INT,
    quantity DECIMAL(10, 2),
    PRIMARY KEY (recipe_id, product_id),
    FOREIGN KEY (recipe_id) REFERENCES recipes(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Таблица отзывов
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    recipe_id INT,
    text TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (recipe_id) REFERENCES recipes(id)
);
-- Таблица для связи рецептов с продуктами
CREATE TABLE IF NOT EXISTS recipe_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT,
    product_id INT,
    quantity INT,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
-- Таблица заявок на добавление рецептов
CREATE TABLE IF NOT EXISTS recipe_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(255) NOT NULL,
    instructions TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Таблица ингредиентов для заявок на добавление рецептов
CREATE TABLE IF NOT EXISTS recipe_request_ingredients (
    request_id INT,
    product_id INT,
    quantity DECIMAL(10, 2),
    PRIMARY KEY (request_id, product_id),
    FOREIGN KEY (request_id) REFERENCES recipe_requests(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

