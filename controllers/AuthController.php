<?php
session_start();
require_once('../config/db.php');

class AuthController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Login de usuario
    public function login($username, $password) {
        try {
            $stmt = $this->conn->prepare("SELECT id, username, password, departamento, role FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['departamento'] = $user['departamento'];
                $_SESSION['role'] = $user['role'];
                return ['success' => true];
            }
            return ['success' => false, 'message' => 'Credenciales inválidas'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error en el servidor'];
        }
    }

    // Verificar si el usuario está autenticado
    public function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../views/login.php');
            exit();
        }
        return true;
    }

    // Verificar permisos del usuario
    public function checkPermission($requiredRole) {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== $requiredRole) {
            return false;
        }
        return true;
    }

    // Cerrar sesión
    public function logout() {
        session_destroy();
        header('Location: ../views/login.php');
        exit();
    }
} 