<?php
session_start();
require_once('../config/db.php');

header('Content-Type: application/json');

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

// Obtener parámetros
$term = isset($_GET['term']) ? trim($_GET['term']) : '';
$field = isset($_GET['field']) ? trim($_GET['field']) : '';
$check_exists = isset($_GET['check_exists']) ? true : false;

// Validar campo
$allowed_fields = ['lugar', 'causa', 'dependencia'];
if (!in_array($field, $allowed_fields)) {
    echo json_encode(['error' => 'Campo no válido']);
    exit();
}

try {
    $conn = getDB();
    
    // Si el campo es 'lugar', usar la tabla lugares
    if ($field === 'lugar') {
        if ($check_exists) {
            $sql = "SELECT COUNT(*) as count FROM lugares WHERE LOWER(nombre) = LOWER(?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $term);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            echo json_encode(['exists' => $row['count'] > 0]);
            exit();
        }

        $sql = "SELECT nombre as value FROM lugares WHERE 1=1 ";
        if (!empty($term)) {
            $sql .= "AND LOWER(nombre) LIKE LOWER(?) ";
        }
        $sql .= "ORDER BY nombre ASC";
        
        $stmt = $conn->prepare($sql);
        if (!empty($term)) {
            $searchTerm = "%$term%";
            $stmt->bind_param("s", $searchTerm);
        }
    }
    // Si el campo es 'causa', usar la tabla causas
    else if ($field === 'causa') {
        if ($check_exists) {
            $sql = "SELECT COUNT(*) as count FROM causas WHERE LOWER(nombre) = LOWER(?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $term);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            echo json_encode(['exists' => $row['count'] > 0]);
            exit();
        }

        $sql = "SELECT nombre as value FROM causas WHERE 1=1 ";
        if (!empty($term)) {
            $sql .= "AND LOWER(nombre) LIKE LOWER(?) ";
        }
        $sql .= "ORDER BY nombre ASC";
        
        $stmt = $conn->prepare($sql);
        if (!empty($term)) {
            $searchTerm = "%$term%";
            $stmt->bind_param("s", $searchTerm);
        }
    }
    // Si el campo es 'dependencia', usar la tabla dependencias
    else if ($field === 'dependencia') {
        if ($check_exists) {
            $sql = "SELECT COUNT(*) as count FROM dependencias WHERE LOWER(nombre) = LOWER(?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $term);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            echo json_encode(['exists' => $row['count'] > 0]);
            exit();
        }

        $sql = "SELECT nombre as value FROM dependencias WHERE 1=1 ";
        if (!empty($term)) {
            $sql .= "AND LOWER(nombre) LIKE LOWER(?) ";
        }
        $sql .= "ORDER BY nombre ASC";
        
        $stmt = $conn->prepare($sql);
        if (!empty($term)) {
            $searchTerm = "%$term%";
            $stmt->bind_param("s", $searchTerm);
        }
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $suggestions = [];
    
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['value'])) {
            $suggestions[] = $row['value'];
        }
    }

    echo json_encode($suggestions);

} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?> 