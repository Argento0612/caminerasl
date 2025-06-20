<?php
require_once '../config/db.php';
session_start();

header('Content-Type: application/json');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'save_form':
        if (!isset($_POST['form_data'])) {
            echo json_encode(['success' => false, 'message' => 'Datos del formulario no proporcionados']);
            exit;
        }

        $formData = json_decode($_POST['form_data'], true);
        $userId = $_SESSION['user_id'];
        $currentDate = date('Y-m-d H:i:s');

        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($conn->connect_error) {
                throw new Exception("Error de conexión: " . $conn->connect_error);
            }

            // Iniciar transacción
            $conn->begin_transaction();

            // Insertar en la tabla forms
            $stmt = $conn->prepare("INSERT INTO forms (user_id, created_at, status) VALUES (?, ?, 'pending')");
            $stmt->bind_param("is", $userId, $currentDate);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al guardar el formulario");
            }

            $formId = $conn->insert_id;

            // Insertar los detalles del formulario
            $stmt = $conn->prepare("INSERT INTO form_details (form_id, field_name, field_value) VALUES (?, ?, ?)");
            
            foreach ($formData as $field => $value) {
                $stmt->bind_param("iss", $formId, $field, $value);
                if (!$stmt->execute()) {
                    throw new Exception("Error al guardar los detalles del formulario");
                }
            }

            // Confirmar transacción
            $conn->commit();
            
            echo json_encode(['success' => true, 'message' => 'Formulario guardado exitosamente', 'form_id' => $formId]);

        } catch (Exception $e) {
            if (isset($conn)) {
                $conn->rollback();
            }
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
            if (isset($conn)) {
                $conn->close();
            }
        }
        break;

    case 'get_form':
        if (!isset($_POST['form_id'])) {
            echo json_encode(['success' => false, 'message' => 'ID del formulario no proporcionado']);
            exit;
        }

        $formId = $_POST['form_id'];

        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($conn->connect_error) {
                throw new Exception("Error de conexión: " . $conn->connect_error);
            }

            // Obtener detalles del formulario
            $stmt = $conn->prepare("
                SELECT f.id, f.user_id, f.created_at, f.status, fd.field_name, fd.field_value 
                FROM forms f 
                LEFT JOIN form_details fd ON f.id = fd.form_id 
                WHERE f.id = ?
            ");
            $stmt->bind_param("i", $formId);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al obtener el formulario");
            }

            $result = $stmt->get_result();
            $formData = [];
            
            while ($row = $result->fetch_assoc()) {
                if (empty($formData)) {
                    $formData = [
                        'id' => $row['id'],
                        'user_id' => $row['user_id'],
                        'created_at' => $row['created_at'],
                        'status' => $row['status'],
                        'fields' => []
                    ];
                }
                $formData['fields'][$row['field_name']] = $row['field_value'];
            }

            if (empty($formData)) {
                echo json_encode(['success' => false, 'message' => 'Formulario no encontrado']);
                exit;
            }

            echo json_encode(['success' => true, 'data' => $formData]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
            if (isset($conn)) {
                $conn->close();
            }
        }
        break;

    case 'update_status':
        if (!isset($_POST['form_id']) || !isset($_POST['status'])) {
            echo json_encode(['success' => false, 'message' => 'ID del formulario o estado no proporcionado']);
            exit;
        }

        $formId = $_POST['form_id'];
        $status = $_POST['status'];
        $validStatus = ['pending', 'approved', 'rejected'];

        if (!in_array($status, $validStatus)) {
            echo json_encode(['success' => false, 'message' => 'Estado no válido']);
            exit;
        }

        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($conn->connect_error) {
                throw new Exception("Error de conexión: " . $conn->connect_error);
            }

            $stmt = $conn->prepare("UPDATE forms SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $formId);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar el estado del formulario");
            }

            if ($stmt->affected_rows === 0) {
                echo json_encode(['success' => false, 'message' => 'Formulario no encontrado']);
                exit;
            }

            echo json_encode(['success' => true, 'message' => 'Estado actualizado exitosamente']);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
            if (isset($conn)) {
                $conn->close();
            }
        }
        break;

    case 'list_forms':
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($conn->connect_error) {
                throw new Exception("Error de conexión: " . $conn->connect_error);
            }

            // Construir la consulta base
            $query = "SELECT f.*, u.username FROM forms f LEFT JOIN users u ON f.user_id = u.id WHERE 1=1";
            $params = [];
            $types = "";

            // Filtrar por usuario si no es administrador
            if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
                $query .= " AND f.user_id = ?";
                $params[] = $_SESSION['user_id'];
                $types .= "i";
            }

            // Filtrar por estado si se proporciona
            if (isset($_POST['status']) && !empty($_POST['status'])) {
                $query .= " AND f.status = ?";
                $params[] = $_POST['status'];
                $types .= "s";
            }

            // Ordenar por fecha de creación descendente
            $query .= " ORDER BY f.created_at DESC";

            $stmt = $conn->prepare($query);
            
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            if (!$stmt->execute()) {
                throw new Exception("Error al obtener la lista de formularios");
            }

            $result = $stmt->get_result();
            $forms = [];
            
            while ($row = $result->fetch_assoc()) {
                $forms[] = $row;
            }

            echo json_encode(['success' => true, 'data' => $forms]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
            if (isset($conn)) {
                $conn->close();
            }
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
} 