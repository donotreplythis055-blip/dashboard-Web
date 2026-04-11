<?php

try {
    $pdo = new PDO(
        "pgsql:host=dpg-d7cqqn7lk1mc73ebedug-a.oregon-postgres.render.com;port=5432;dbname=dashboard_db_x12n",
        "dashboard_db_x12n_user",
        "A_SAJAT_JELSZAVAD"
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("
        CREATE TABLE users (
            id SERIAL PRIMARY KEY,
            username TEXT,
            email TEXT,
            password TEXT,
            role TEXT DEFAULT 'user',
            verification_code INT,
            is_verified BOOLEAN DEFAULT FALSE
        );
    ");

    echo "Kész!";

} catch (PDOException $e) {
    die($e->getMessage());
}
