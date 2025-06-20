<?php
session_start();
require_once '../config/db.php';
require_once '../controllers/LoginController.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        $db = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS
        );
        
        $loginController = new LoginController($db);
        $result = $loginController->login($username, $password);

        if ($result['success']) {
            header('Location: dashboard.php');
            exit();
        } else {
            $error = $result['message'];
        }
    } catch (PDOException $e) {
        $error = "Error de conexión: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Formularios</title>
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

        .login-container {
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

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
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

        .form-control {
            background: transparent;
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

        .btn-login {
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

        .btn-login:hover {
            background: var(--neon-color);
            color: #000;
            box-shadow: 0 0 20px var(--neon-color);
        }

        .btn-login::before {
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

        .btn-login:hover::before {
            left: 100%;
        }

        .password-container {
            position: relative;
            width: 100%;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--neon-color);
            transition: all 0.3s ease;
            z-index: 10;
        }

        .toggle-password:hover {
            color: var(--neon-color-alt);
            text-shadow: 0 0 5px var(--neon-color);
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
            background-color: rgba(255, 0, 0, 0.9);
            border: 2px solid #ff3333;
            color: #ffffff;
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-family: 'Orbitron', sans-serif;
            text-align: center;
            font-size: 1rem;
            font-weight: 500;
            letter-spacing: 1px;
            box-shadow: 0 0 20px rgba(255, 0, 0, 0.4),
                        inset 0 0 10px rgba(255, 255, 255, 0.1);
            animation: alertPulse 2s infinite;
            position: relative;
            backdrop-filter: blur(5px);
        }

        @keyframes alertPulse {
            0% { box-shadow: 0 0 20px rgba(255, 0, 0, 0.4), inset 0 0 10px rgba(255, 255, 255, 0.1); }
            50% { box-shadow: 0 0 30px rgba(255, 0, 0, 0.6), inset 0 0 15px rgba(255, 255, 255, 0.2); }
            100% { box-shadow: 0 0 20px rgba(255, 0, 0, 0.4), inset 0 0 10px rgba(255, 255, 255, 0.1); }
        }

        @media (max-width: 576px) {
            .login-container {
                padding: 2rem;
                margin: 1rem auto;
            }

            .login-header h1 {
                font-size: 2rem;
            }

            body {
                padding: 1rem 0;
            }
        }

        .logo-img {
            filter: drop-shadow(0 0 10px var(--neon-color));
            transition: all 0.3s ease;
        }

        .logo-img:hover {
            filter: drop-shadow(0 0 15px var(--neon-color)) brightness(1.1);
            transform: scale(1.02);
        }

        .register-link {
            color: var(--neon-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .register-link:hover {
            color: var(--neon-color-alt);
            text-shadow: 0 0 10px var(--neon-color);
        }
    </style>
</head>
<body>
    <div id="particles-js"></div>
    <div class="login-container">
        <div class="text-center mb-4">
            <img src="../public/img/logo.png" alt="Logo Institucional" style="width: 180px;" class="logo-img">
        </div>
        <div class="login-header">
            <h1>Iniciar Sesión</h1>
        </div>
        <?php if ($error): ?>
            <div class="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['login_success'])): ?>
            <div class="alert alert-success" role="alert" style="color: <?php echo isset($_SESSION['login_success_color']) ? $_SESSION['login_success_color'] : 'green'; ?>">
                <?php 
                    echo htmlspecialchars($_SESSION['login_success']);
                    unset($_SESSION['login_success']);
                    unset($_SESSION['login_success_color']);
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <input type="text" name="username" class="form-control" placeholder="Nombre de usuario o correo electrónico" required>
            </div>
            <div class="form-group">
                <div class="password-container">
                    <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
                    <i class="toggle-password fas fa-eye"></i>
                </div>
            </div>
            <button type="submit" class="btn btn-login">Iniciar Sesión</button>
        </form>
        
        <div class="text-center mt-3">
            <a href="register.php" class="register-link">¿No tienes cuenta? Regístrate</a>
        </div>
    </div>

    <!-- Particles.js -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS('particles-js', {
            particles: {
                number: { value: 50, density: { enable: true, value_area: 800 } },
                color: { value: '#ccff00' },
                shape: { type: 'circle' },
                opacity: {
                    value: 0.5,
                    random: true,
                    animation: { enable: true, speed: 1, minimumValue: 0.1, sync: false }
                },
                size: {
                    value: 3,
                    random: true,
                    animation: { enable: true, speed: 2, minimumValue: 0.1, sync: false }
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: '#ccff00',
                    opacity: 0.4,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 2,
                    direction: 'none',
                    random: true,
                    straight: false,
                    out_mode: 'out',
                    bounce: false
                }
            },
            interactivity: {
                detect_on: 'canvas',
                events: {
                    onhover: { enable: true, mode: 'repulse' },
                    onclick: { enable: true, mode: 'push' },
                    resize: true
                }
            },
            retina_detect: true
        });
    </script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('.toggle-password');
            const passwordInput = document.querySelector('input[name="password"]');

            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        });
    </script>
</body>
</html>