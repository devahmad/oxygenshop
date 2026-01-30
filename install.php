<?php
require_once 'db.php';

try {
    // جدول کاربران
    $sqlUsers = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlUsers);

    // جدول محصولات
    $sqlProducts = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        description TEXT,
        price DECIMAL(10, 0) NOT NULL,
        image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlProducts);

    // جدول سفارشات
    $sqlOrders = "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tracking_code VARCHAR(50) NOT NULL UNIQUE,
        user_id INT NOT NULL,
        total_price DECIMAL(10, 0) NOT NULL,
        address TEXT NOT NULL,
        phone VARCHAR(20) NOT NULL,
        status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sqlOrders);

    // جدول آیتم‌های سفارش
    $sqlOrderItems = "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10, 0) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )";
    $pdo->exec($sqlOrderItems);


    // ادمین پیش‌فرض
    // user: admin, pass: 123456
    $adminPass = password_hash('123456', PASSWORD_DEFAULT);
    $checkAdmin = $pdo->query("SELECT * FROM users WHERE username = 'admin'");
    if ($checkAdmin->rowCount() == 0) {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['admin', 'admin@example.com', $adminPass, 'admin']);
        echo "✅ ادمین پیش‌فرض ساخته شد: admin / 123456 <br>";
    }

    echo "✅ دیتابیس و جداول با موفقیت نصب شدند.";

} catch (PDOException $e) {
    die("خطا در نصب: " . $e->getMessage());
}
