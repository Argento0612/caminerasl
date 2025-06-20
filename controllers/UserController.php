<?php
session_start();
require_once('../config/db.php');

class UserController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Crear nuevo usuario
    public function createUser($data) {
        try {
            // Verificar si el usuario ya existe
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$data['username']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'El nombre de usuario ya existe'];
            }

            // Crear el usuario
            $stmt = $this->conn->prepare("
                INSERT INTO users (
                    username, password, nombre_completo, 
                    departamento, role, email
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");

            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            $stmt->execute([
                $data['username'],
                $hashedPassword,
                $data['nombre_completo'],
                $data['departamento'],
                $data['role'],
                $data['email']
            ]);

            return ['success' => true, 'message' => 'Usuario creado correctamente'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al crear el usuario: ' . $e->getMessage()];
        }
    }

    // Actualizar usuario
    public function updateUser($id, $data) {
        try {
            $updates = [];
            $params = [];

            // Construir la consulta dinámicamente
            if (isset($data['nombre_completo'])) {
                $updates[] = "nombre_completo = ?";
                $params[] = $data['nombre_completo'];
            }
            if (isset($data['email'])) {
                $updates[] = "email = ?";
                $params[] = $data['email'];
            }
            if (isset($data['departamento'])) {
                $updates[] = "departamento = ?";
                $params[] = $data['departamento'];
            }
            if (isset($data['role'])) {
                $updates[] = "role = ?";
                $params[] = $data['role'];
            }
            if (isset($data['password']) && !empty($data['password'])) {
                $updates[] = "password = ?";
                $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            if (empty($updates)) {
                return ['success' => false, 'message' => 'No hay datos para actualizar'];
            }

            $params[] = $id;
            $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            return ['success' => true, 'message' => 'Usuario actualizado correctamente'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al actualizar el usuario: ' . $e->getMessage()];
        }
    }

    // Obtener usuario por ID
    public function getUserById($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT id, username, nombre_completo, departamento, 
                       role, email, created_at, updated_at 
                FROM users 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return ['success' => false, 'message' => 'Usuario no encontrado'];
            }

            return ['success' => true, 'user' => $user];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al obtener el usuario'];
        }
    }

    // Listar usuarios
    public function listUsers($page = 1, $perPage = 10, $filters = []) {
        try {
            $offset = ($page - 1) * $perPage;
            $where = [];
            $params = [];

            // Aplicar filtros
            if (!empty($filters['departamento'])) {
                $where[] = "departamento = ?";
                $params[] = $filters['departamento'];
            }
            if (!empty($filters['role'])) {
                $where[] = "role = ?";
                $params[] = $filters['role'];
            }
            if (!empty($filters['search'])) {
                $where[] = "(username LIKE ? OR nombre_completo LIKE ? OR email LIKE ?)";
                $searchTerm = "%{$filters['search']}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            // Obtener total de registros
            $countSql = "SELECT COUNT(*) FROM users $whereClause";
            $stmt = $this->conn->prepare($countSql);
            $stmt->execute($params);
            $total = $stmt->fetchColumn();

            // Obtener usuarios
            $sql = "
                SELECT id, username, nombre_completo, departamento, 
                       role, email, created_at, updated_at 
                FROM users 
                $whereClause 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?
            ";
            
            $params[] = $perPage;
            $params[] = $offset;

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'success' => true,
                'users' => $users,
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'totalPages' => ceil($total / $perPage)
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al listar usuarios'];
        }
    }

    // Eliminar usuario
    public function deleteUser($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Usuario no encontrado'];
            }

            return ['success' => true, 'message' => 'Usuario eliminado correctamente'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al eliminar el usuario'];
        }
    }

    // Cambiar contraseña
    public function changePassword($userId, $currentPassword, $newPassword) {
        try {
            // Verificar contraseña actual
            $stmt = $this->conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($currentPassword, $user['password'])) {
                return ['success' => false, 'message' => 'Contraseña actual incorrecta'];
            }

            // Actualizar contraseña
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $userId]);

            return ['success' => true, 'message' => 'Contraseña actualizada correctamente'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al cambiar la contraseña'];
        }
    }
} 