<?php
require_once('../controllers/AuthController.php');
require_once('../config/db.php');

header('Content-Type: application/json');

$db = new PDO(
    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
    DB_USER,
    DB_PASS
);
$auth = new AuthController($db);

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        echo json_encode($auth->login($username, $password));
        break;

    case 'logout':
        $auth->logout();
        echo json_encode(['success' => true]);
        break;

    case 'check_auth':
        echo json_encode(['authenticated' => $auth->checkAuth()]);
        break;

    case 'check_permission':
        $role = $_POST['role'] ?? '';
        echo json_encode(['hasPermission' => $auth->checkPermission($role)]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
} 