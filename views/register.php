<?php
session_start();
require_once '../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $dni = trim($_POST['dni']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $departamento = trim($_POST['departamento']);

    if (empty($username) || empty($dni) || empty($email) || empty($password) || empty($confirm_password) || empty($departamento)) {
        $error = 'Por favor, complete todos los campos';
    } elseif (!preg_match('/^[0-9]{8}$/', $dni)) {
        $error = 'El DNI debe contener 8 dígitos numéricos';
    } elseif ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden';
    } else {
        try {
            $db = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS
            );
            
            // Verificar si el usuario o DNI ya existe
            $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ? OR dni = ?");
            $stmt->execute([$username, $email, $dni]);
            
            if ($stmt->rowCount() > 0) {
                $error = 'El usuario, correo electrónico o DNI ya está registrado';
            } else {
                // Insertar nuevo usuario
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("
                    INSERT INTO users (username, dni, email, password, departamento, rol) 
                    VALUES (?, ?, ?, ?, ?, 'efectivo')
                ");
                
                if ($stmt->execute([$username, $dni, $email, $hashed_password, $departamento])) {
                    $success = 'Registro exitoso. Ahora puedes iniciar sesión.';
                    header("refresh:2;url=login.php");
                } else {
                    $error = 'Error al registrar el usuario';
                }
            }
        } catch (PDOException $e) {
            $error = "Error de conexión: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema de Formularios</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --neon-color: #ccff00;
            --neon-color-alt: #d4f300;
            --bg-color: #1a1a1a;
            --text-color: #ffffff;
            --accent-color: rgba(255, 255, 255, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Rajdhani', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            overflow: auto;
            position: relative;
            padding: 2rem 0;
        }

        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .register-container {
            position: relative;
            z-index: 2;
            background: rgba(26, 26, 26, 0.8);
            padding: 3rem;
            border-radius: 10px;
            box-shadow: 0 0 30px rgba(204, 255, 0, 0.2);
            border: 1px solid var(--neon-color);
            max-width: 500px;
            width: 90%;
            backdrop-filter: blur(10px);
            margin: 2rem auto;
        }

        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .register-header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: var(--neon-color);
            text-shadow: 0 0 10px var(--neon-color);
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-color);
            opacity: 0.7;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .form-group .toggle-password:hover {
            opacity: 1;
            color: var(--neon-color);
        }

        .form-control {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid var(--neon-color);
            color: var(--text-color);
            padding: 0.8rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(204, 255, 0, 0.1);
            border-color: var(--neon-color-alt);
            box-shadow: 0 0 15px rgba(204, 255, 0, 0.3);
            color: var(--text-color);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .btn-register {
            background: transparent;
            color: var(--text-color);
            border: 2px solid var(--neon-color);
            padding: 0.8rem 2rem;
            font-size: 1.1rem;
            font-family: 'Orbitron', sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-register:hover {
            background: var(--neon-color);
            color: #000;
            box-shadow: 0 0 20px var(--neon-color);
        }

        .btn-register::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            transition: 0.5s;
        }

        .btn-register:hover::before {
            left: 100%;
        }

        .back-link {
            color: var(--neon-color);
            text-decoration: none;
            display: inline-block;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: var(--neon-color-alt);
            text-shadow: 0 0 10px var(--neon-color);
        }

        .alert {
            background: rgba(255, 59, 48, 0.15);
            border: 2px solid #ff3b30;
            color: #ffffff;
            padding: 1.2rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 1px;
            position: relative;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 20px rgba(255, 59, 48, 0.3),
                        inset 0 0 10px rgba(255, 59, 48, 0.1);
            animation: alertGlow 2s infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }

        .alert::before {
            content: '\f071';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 1.2rem;
            color: #ff3b30;
            text-shadow: 0 0 10px rgba(255, 59, 48, 0.5);
        }

        .success {
            background: rgba(52, 199, 89, 0.15);
            border: 2px solid #34c759;
            color: #ffffff;
            padding: 1.2rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 1px;
            position: relative;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 20px rgba(52, 199, 89, 0.3),
                        inset 0 0 10px rgba(52, 199, 89, 0.1);
            animation: successGlow 2s infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }

        .success::before {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 1.2rem;
            color: #34c759;
            text-shadow: 0 0 10px rgba(52, 199, 89, 0.5);
        }

        @keyframes alertGlow {
            0% { box-shadow: 0 0 20px rgba(255, 59, 48, 0.3), inset 0 0 10px rgba(255, 59, 48, 0.1); }
            50% { box-shadow: 0 0 30px rgba(255, 59, 48, 0.5), inset 0 0 15px rgba(255, 59, 48, 0.2); }
            100% { box-shadow: 0 0 20px rgba(255, 59, 48, 0.3), inset 0 0 10px rgba(255, 59, 48, 0.1); }
        }

        @keyframes successGlow {
            0% { box-shadow: 0 0 20px rgba(52, 199, 89, 0.3), inset 0 0 10px rgba(52, 199, 89, 0.1); }
            50% { box-shadow: 0 0 30px rgba(52, 199, 89, 0.5), inset 0 0 15px rgba(52, 199, 89, 0.2); }
            100% { box-shadow: 0 0 20px rgba(52, 199, 89, 0.3), inset 0 0 10px rgba(52, 199, 89, 0.1); }
        }

        .error-message {
            background: rgba(255, 59, 48, 0.15) !important;
            border: 2px solid #ff3b30 !important;
            color: #ffffff !important;
            padding: 1.2rem !important;
            margin-bottom: 1.5rem !important;
            border-radius: 12px !important;
            font-family: 'Orbitron', sans-serif !important;
            text-align: center !important;
            letter-spacing: 1px !important;
            position: relative !important;
            backdrop-filter: blur(10px) !important;
            box-shadow: 0 0 20px rgba(255, 59, 48, 0.3),
                        inset 0 0 10px rgba(255, 59, 48, 0.1) !important;
            animation: alertGlow 2s infinite !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 1rem !important;
        }

        .error-message::before {
            content: '\f071';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 1.2rem;
            color: #ff3b30;
            text-shadow: 0 0 10px rgba(255, 59, 48, 0.5);
        }

        .alert-password {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(255, 59, 48, 0.15);
            border: 2px solid #ff3b30;
            color: #ffffff;
            padding: 1.2rem 2rem;
            border-radius: 12px;
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 1rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 20px rgba(255, 59, 48, 0.3),
                        inset 0 0 10px rgba(255, 59, 48, 0.1);
            transform: translateX(150%);
            transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            z-index: 1000;
            animation: alertGlow 2s infinite;
        }

        .alert-password i {
            font-size: 1.5rem;
            color: #ff3b30;
            text-shadow: 0 0 10px rgba(255, 59, 48, 0.5);
        }

        .alert-password.show {
            transform: translateX(0);
        }

        .logo-img {
            filter: drop-shadow(0 0 10px var(--neon-color));
            transition: all 0.3s ease;
        }

        .logo-img:hover {
            filter: drop-shadow(0 0 15px var(--neon-color)) brightness(1.1);
            transform: scale(1.02);
        }

        .btn-departamento {
            background: var(--bg-color);
            color: var(--text-color);
            border: 2px solid var(--neon-color);
            padding: 1rem;
            width: 100%;
            font-family: 'Orbitron', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-bottom: 0.5rem;
            text-align: center;
            cursor: pointer;
        }

        .btn-departamento:hover {
            background: rgba(204, 255, 0, 0.1);
            box-shadow: 0 0 15px rgba(204, 255, 0, 0.3);
        }

        .btn-departamento.active {
            background: var(--neon-color);
            color: #000;
            box-shadow: 0 0 20px var(--neon-color);
        }

        .btn-departamento::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            transition: 0.5s;
        }

        .btn-departamento:hover::before {
            left: 100%;
        }

        .btn-departamento-selector {
            background: rgba(26, 26, 26, 0.9);
            color: var(--neon-color);
            border: 2px solid var(--neon-color);
            padding: 1.2rem;
            width: 100%;
            font-family: 'Orbitron', sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-bottom: 0.5rem;
            text-align: left;
            cursor: pointer;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(204, 255, 0, 0.1);
        }

        .selector-content {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            position: relative;
        }

        .btn-departamento-selector i {
            font-size: 1.4rem;
            color: var(--neon-color);
            transition: all 0.3s ease;
        }

        .selector-arrow {
            margin-left: auto;
            font-size: 1rem !important;
            opacity: 0.8;
            transform: translateX(0);
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .btn-departamento-selector:hover {
            background: rgba(204, 255, 0, 0.1);
            box-shadow: 0 0 20px rgba(204, 255, 0, 0.3);
            transform: translateY(-2px);
        }

        .btn-departamento-selector:hover .selector-arrow {
            transform: translateX(5px);
            opacity: 1;
        }

        .btn-departamento-selector:hover i:first-child {
            transform: scale(1.1) rotate(5deg);
        }

        .btn-departamento-selector::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(204, 255, 0, 0.1),
                transparent
            );
            transition: 0.5s;
        }

        .btn-departamento-selector:hover::before {
            left: 100%;
        }

        #departamentoSeleccionado {
            flex-grow: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 0.85rem;
        }

        .departamentos-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 1rem;
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .departamentos-grid::-webkit-scrollbar {
            width: 8px;
        }

        .departamentos-grid::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
        }

        .departamentos-grid::-webkit-scrollbar-thumb {
            background: var(--neon-color);
            border-radius: 10px;
            border: 2px solid rgba(0, 0, 0, 0.8);
        }

        .departamentos-grid::-webkit-scrollbar-thumb:hover {
            background: #d4ff33;
            box-shadow: 0 0 15px rgba(204, 255, 0, 0.5);
        }

        .departamento-item {
            background: rgba(0, 0, 0, 0.8);
            border: 1px solid var(--neon-color);
            border-radius: 12px;
            padding: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 1.2rem;
            position: relative;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }

        .departamento-item:hover {
            background: rgba(204, 255, 0, 0.1);
            box-shadow: 0 0 20px rgba(204, 255, 0, 0.3);
            transform: translateY(-2px);
        }

        .departamento-item.selected {
            background: var(--neon-color);
            color: #000;
        }

        .departamento-item.selected .departamento-icon i,
        .departamento-item.selected .departamento-text h6 {
            color: #000;
        }

        .departamento-icon {
            font-size: 1.8rem;
            color: var(--neon-color);
            transition: all 0.3s ease;
            min-width: 40px;
            text-align: center;
        }

        .departamento-text h6 {
            margin: 0;
            font-family: 'Orbitron', sans-serif;
            font-size: 0.85rem;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            color: var(--text-color);
        }

        @media (max-width: 576px) {
            .register-container {
                padding: 2rem;
            }

            .register-header h1 {
                font-size: 2rem;
            }

            .btn-departamento {
                font-size: 0.8rem;
                padding: 0.8rem;
            }

            .departamentos-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-body {
                padding: 1rem;
            }
            
            .departamento-item {
                padding: 1rem;
            }
        }

        /* Estilos para la validación de contraseña */
        .password-requirements {
            margin-top: 1rem;
            padding: 1rem;
            background: rgba(26, 26, 26, 0.8);
            border: 1px solid var(--neon-color);
            border-radius: 8px;
            display: none;
        }

        .requirement {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 0.5rem;
            color: #ff4444;
            font-family: 'Orbitron', sans-serif;
            font-size: 0.85rem;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .requirement.valid {
            color: var(--neon-color);
        }

        .requirement i {
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .requirement.valid i {
            transform: scale(1.1);
        }

        .password-match {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-top: 0.5rem;
            color: #ff4444;
            font-family: 'Orbitron', sans-serif;
            font-size: 0.85rem;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            opacity: 0;
        }

        .password-match.valid {
            color: var(--neon-color);
            opacity: 1;
        }

        .password-match.invalid {
            color: #ff4444;
            opacity: 1;
        }

        .password-match i {
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .password-match.valid i {
            transform: scale(1.1);
        }

        .modal-content {
            background: rgba(0, 0, 0, 0.95) !important;
            border: 2px solid var(--neon-color);
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(204, 255, 0, 0.2);
            backdrop-filter: blur(10px);
        }

        .modal-header {
            border-bottom: 1px solid var(--neon-color);
            padding: 1.5rem;
            background: rgba(0, 0, 0, 0.8);
        }

        .modal-title {
            color: var(--neon-color);
            font-family: 'Orbitron', sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 1.2rem;
            text-shadow: 0 0 10px rgba(204, 255, 0, 0.5);
        }

        .modal-body {
            padding: 2rem;
            background: rgba(0, 0, 0, 0.8);
        }

        .btn-close {
            background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ccff00'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e") center/1em auto no-repeat !important;
            opacity: 0.7;
            filter: drop-shadow(0 0 2px rgba(204, 255, 0, 0.5));
            transition: all 0.3s ease;
        }

        .btn-close:hover {
            opacity: 1;
            transform: rotate(90deg);
            filter: drop-shadow(0 0 5px rgba(204, 255, 0, 0.8));
        }

        .modal.fade .modal-dialog {
            transform: scale(0.95);
            transition: transform 0.3s ease-out;
        }

        .modal.show .modal-dialog {
            transform: scale(1);
        }
    </style>
</head>
<body>
    <div id="particles-js"></div>
    <div class="register-container">
        <div class="text-center mb-4">
            <img src="../public/img/logo.png" alt="Logo Institucional" style="width: 180px;" class="logo-img">
        </div>
        <div class="register-header">
            <h1>Registro</h1>
        </div>
        
        <?php if (isset($_SESSION['register_errors'])): ?>
            <div class="error-message">
                <?php 
                    foreach ($_SESSION['register_errors'] as $error) {
                        echo htmlspecialchars($error) . "<br>";
                    }
                    unset($_SESSION['register_errors']);
                    unset($_SESSION['register_error_color']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <input type="text" name="username" class="form-control" placeholder="Nombre de usuario" required>
            </div>
            <div class="form-group">
                <input type="text" name="dni" class="form-control" placeholder="DNI (8 dígitos)" required pattern="[0-9]{8}" maxlength="8">
            </div>
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
            </div>
            <div class="form-group">
                <button type="button" class="btn-departamento-selector" data-bs-toggle="modal" data-bs-target="#departamentoModal">
                    <div class="selector-content">
                        <i class="fas fa-building"></i>
                        <span id="departamentoSeleccionado">SELECCIONAR DEPARTAMENTO</span>
                        <i class="fas fa-chevron-right selector-arrow"></i>
                    </div>
                </button>
                <input type="hidden" id="departamento_id" name="departamento_id">
                <input type="hidden" name="departamento" id="departamento_nombre">
            </div>
            <div class="form-group">
                <div class="password-container">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" required>
                    <i class="toggle-password fas fa-eye" onclick="togglePassword('password')"></i>
                </div>
                <div class="password-requirements">
                    <div class="requirement" data-requirement="length">
                        <i class="fas fa-times-circle"></i> Mínimo 8 caracteres
                    </div>
                    <div class="requirement" data-requirement="uppercase">
                        <i class="fas fa-times-circle"></i> Al menos una mayúscula
                    </div>
                    <div class="requirement" data-requirement="lowercase">
                        <i class="fas fa-times-circle"></i> Al menos una minúscula
                    </div>
                    <div class="requirement" data-requirement="symbol">
                        <i class="fas fa-times-circle"></i> Al menos un símbolo (!@#$%)
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="password-container">
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirmar contraseña" required>
                    <i class="toggle-password fas fa-eye" onclick="togglePassword('confirm_password')"></i>
                </div>
                <div class="password-match">
                    <i class="fas fa-times-circle"></i> Las contraseñas coinciden
                </div>
            </div>
            <button type="submit" class="btn btn-register">Registrarse</button>
        </form>
        
        <div class="text-center mt-3">
            <a href="login.php" class="back-link">← Volver al login</a>
        </div>
    </div>

    <!-- Modal de Departamentos -->
    <div class="modal fade" id="departamentoModal" tabindex="-1" aria-labelledby="departamentoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="departamentoModalLabel">Selecciona tu Departamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="departamentos-grid">
                        <div class="departamento-item" data-id="1">
                            <div class="departamento-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="departamento-text">
                                <h6>Departamento de Seguridad Vial N° 1</h6>
                            </div>
                        </div>
                        <div class="departamento-item" data-id="2">
                            <div class="departamento-icon">
                                <i class="fas fa-traffic-light"></i>
                            </div>
                            <div class="departamento-text">
                                <h6>Departamento de Seguridad Vial N° 2</h6>
                            </div>
                        </div>
                        <div class="departamento-item" data-id="3">
                            <div class="departamento-icon">
                                <i class="fas fa-car-side"></i>
                            </div>
                            <div class="departamento-text">
                                <h6>Departamento de Seguridad Vial N° 3</h6>
                            </div>
                        </div>
                        <div class="departamento-item" data-id="4">
                            <div class="departamento-icon">
                                <i class="fas fa-road"></i>
                            </div>
                            <div class="departamento-text">
                                <h6>Departamento de Seguridad Vial N° 4</h6>
                            </div>
                        </div>
                        <div class="departamento-item" data-id="5">
                            <div class="departamento-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="departamento-text">
                                <h6>Departamento de Seguridad Vial N° 5</h6>
                            </div>
                        </div>
                        <div class="departamento-item" data-id="6">
                            <div class="departamento-icon">
                                <i class="fas fa-traffic-light"></i>
                            </div>
                            <div class="departamento-text">
                                <h6>Departamento de Tránsito</h6>
                            </div>
                        </div>
                        <div class="departamento-item" data-id="7">
                            <div class="departamento-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="departamento-text">
                                <h6>Brigada Especiales</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Declaración de variables globales
        const form = document.querySelector('form');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const departamentoModal = document.getElementById('departamentoModal');
        const passwordMatch = document.querySelector('.password-match');
        const requirements = document.querySelectorAll('.requirement');
        const passwordRequirements = document.querySelector('.password-requirements');
        
        // Solo inicializar la funcionalidad de departamentos si el modal existe
        if (departamentoModal) {
            const items = document.querySelectorAll('.departamento-item');
            const hiddenInput = document.getElementById('departamento_id');
            const departamentoInput = document.getElementById('departamento_nombre');
            const departamentoSeleccionado = document.getElementById('departamentoSeleccionado');

            items.forEach(item => {
                item.addEventListener('click', function() {
                    items.forEach(i => i.classList.remove('selected'));
                    this.classList.add('selected');
                    
                    const id = this.getAttribute('data-id');
                    const text = this.querySelector('.departamento-text h6').textContent;
                    if (hiddenInput) hiddenInput.value = id;
                    if (departamentoInput) departamentoInput.value = text;
                    if (departamentoSeleccionado) departamentoSeleccionado.textContent = text;
                    
                    const modal = bootstrap.Modal.getInstance(departamentoModal);
                    if (modal) modal.hide();
                });
            });

            // Validación de departamento en el formulario
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (hiddenInput && !hiddenInput.value) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Departamento requerido',
                            text: 'Por favor, selecciona un departamento',
                            background: '#1a1a1a',
                            color: '#fff',
                            confirmButtonColor: '#ccff00',
                            iconColor: '#ccff00'
                        });
                    }
                });
            }
        }

        // Inicializar validación de contraseña solo si los elementos existen
        if (passwordInput && confirmPasswordInput) {
            passwordInput.addEventListener('focus', function() {
                passwordRequirements.style.display = 'block';
            });

            passwordInput.addEventListener('blur', function() {
                if (!this.value) {
                    passwordRequirements.style.display = 'none';
                }
            });

            passwordInput.addEventListener('input', function() {
                const password = this.value;
                
                if (requirements.length > 0) {
                    const lengthValid = password.length >= 8;
                    const uppercaseValid = /[A-Z]/.test(password);
                    const lowercaseValid = /[a-z]/.test(password);
                    const symbolValid = /[!@#$%]/.test(password);

                    updateRequirement('length', lengthValid);
                    updateRequirement('uppercase', uppercaseValid);
                    updateRequirement('lowercase', lowercaseValid);
                    updateRequirement('symbol', symbolValid);
                }

                validatePasswordMatch();
            });

            if (confirmPasswordInput) {
                confirmPasswordInput.addEventListener('input', validatePasswordMatch);
            }
        }

        function updateRequirement(type, isValid) {
            const requirement = document.querySelector(`[data-requirement="${type}"]`);
            if (requirement) {
                requirement.classList.toggle('valid', isValid);
                const icon = requirement.querySelector('i');
                if (icon) {
                    icon.className = isValid ? 'fas fa-check-circle' : 'fas fa-times-circle';
                }
            }
        }

        function validatePasswordMatch() {
            if (!passwordInput || !confirmPasswordInput || !passwordMatch) return;
            
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            if (confirmPassword) {
                const isValid = password === confirmPassword;
                if (passwordMatch) {
                    passwordMatch.classList.toggle('valid', isValid);
                    passwordMatch.classList.toggle('invalid', !isValid);
                    
                    const icon = passwordMatch.querySelector('i');
                    if (icon) {
                        icon.className = isValid ? 'fas fa-check-circle' : 'fas fa-times-circle';
                    }
                }
            } else if (passwordMatch) {
                passwordMatch.classList.remove('valid', 'invalid');
            }
        }

        // Validación del formulario
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!passwordInput || !confirmPasswordInput) return;

                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                
                if (!password || !confirmPassword) {
                    e.preventDefault();
                    showAlert('Por favor, complete ambos campos de contraseña');
                    return;
                }
                
                const lengthValid = password.length >= 8;
                const uppercaseValid = /[A-Z]/.test(password);
                const lowercaseValid = /[a-z]/.test(password);
                const symbolValid = /[!@#$%]/.test(password);
                const matchValid = password === confirmPassword;
                
                if (!lengthValid || !uppercaseValid || !lowercaseValid || !symbolValid || !matchValid) {
                    e.preventDefault();
                    showAlert('Por favor, cumple con todos los requisitos de la contraseña');
                }
            });
        }

        function showAlert(message) {
            const alert = document.createElement('div');
            alert.className = 'alert-password';
            alert.innerHTML = `
                <i class="fas fa-exclamation-circle"></i>
                <span>${message}</span>
            `;
            document.body.appendChild(alert);
            
            setTimeout(() => alert.classList.add('show'), 100);
            
            setTimeout(() => {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 500);
            }, 3000);
        }

        // Función para alternar la visibilidad de la contraseña
        window.togglePassword = function(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling;
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    });
    </script>
</body>
</html>