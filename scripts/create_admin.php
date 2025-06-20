<?php
require_once __DIR__ . '/../config/db.php';

try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS
    );
    
    // Datos del administrador
    $username = 'admin';
    $email = 'admin@example.com';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $departamento = 'Administración';
    $rol = 'admin';

    // Verificar si el usuario ya existe
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->rowCount() === 0) {
        // Insertar el usuario administrador
        $stmt = $db->prepare("
            INSERT INTO users (username, email, password, departamento, rol) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([$username, $email, $password, $departamento, $rol]);
        echo "Usuario administrador creado exitosamente\n";
        echo "Usuario: admin\n";
        echo "Contraseña: admin123\n";
    } else {
        echo "El usuario administrador ya existe\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 