<?php
class LoginController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function login($username, $password) {
        if (empty($username) || empty($password)) {
            return ['success' => false, 'message' => 'Usuario y contraseña son requeridos'];
        }

        try {
            $stmt = $this->db->prepare("SELECT id, username, email, password, rol, departamento FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                error_log("Login fallido: Usuario/Email no encontrado: " . $username);
                return ['success' => false, 'message' => 'Usuario o contraseña incorrectos'];
            }

            if (!password_verify($password, $user['password'])) {
                error_log("Login fallido: Contraseña incorrecta para el usuario/email: " . $username);
                return ['success' => false, 'message' => 'Usuario o contraseña incorrectos'];
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['is_admin'] = ($user['rol'] === 'admin');
            $_SESSION['departamento'] = $user['departamento'];
            
            error_log("Login exitoso para el usuario: " . $user['username']);
            return [
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'is_admin' => ($user['rol'] === 'admin'),
                    'departamento' => $user['departamento']
                ]
            ];

        } catch (PDOException $e) {
            error_log("Error de login: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al intentar iniciar sesión'];
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        return ['success' => true];
    }

    public function checkAuth() {
        return isset($_SESSION['user_id']);
    }

    public function checkPermission($role) {
        if (!$this->checkAuth()) {
            return false;
        }

        if ($role === 'admin') {
            return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
        }

        return true;
    }
} 