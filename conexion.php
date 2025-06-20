<?php
$host = "localhost";
$user = "root";
$password = ""; // Por defecto en XAMPP, la contraseña está vacía
$database = "daniel prueba";

// Crear conexión
$conn = new mysqli($host, $user, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
echo "¡Conexión exitosa!";
?> 