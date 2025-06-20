<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Formularios</title>
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
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .container {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 2rem;
        }

        .logo {
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
            position: relative;
            animation: rotate 20s linear infinite;
        }

        .logo::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border: 3px solid var(--neon-color);
            border-radius: 50%;
            box-shadow: 0 0 20px var(--neon-color);
            animation: pulse 2s ease-in-out infinite;
        }

        .logo::after {
            content: '';
            position: absolute;
            width: 80%;
            height: 80%;
            top: 10%;
            left: 10%;
            border: 2px solid var(--neon-color-alt);
            border-radius: 50%;
            box-shadow: 0 0 15px var(--neon-color-alt);
        }

        h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 4rem;
            margin-bottom: 3rem;
            text-transform: uppercase;
            letter-spacing: 4px;
            position: relative;
            animation: textGlow 2s ease-in-out infinite alternate;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 200px;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--neon-color), transparent);
        }

        .btn-container {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            max-width: 400px;
            margin: 0 auto;
        }

        .btn-futuristic {
            background: transparent;
            color: var(--text-color);
            border: 2px solid var(--neon-color);
            padding: 1.2rem 2.5rem;
            font-size: 1.2rem;
            font-family: 'Orbitron', sans-serif;
            text-transform: uppercase;
            letter-spacing: 3px;
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
            box-shadow: 0 0 15px rgba(204, 255, 0, 0.3);
            clip-path: polygon(10% 0, 100% 0, 90% 100%, 0 100%);
        }

        .btn-futuristic:hover {
            background: var(--neon-color);
            color: #000;
            box-shadow: 0 0 30px var(--neon-color);
            transform: translateY(-5px) scale(1.05);
        }

        .btn-futuristic::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.3),
                transparent
            );
            transition: 0.5s;
        }

        .btn-futuristic:hover::before {
            left: 100%;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes textGlow {
            from {
                text-shadow: 0 0 10px var(--neon-color),
                           0 0 20px var(--neon-color),
                           0 0 30px var(--neon-color);
            }
            to {
                text-shadow: 0 0 20px var(--neon-color),
                           0 0 30px var(--neon-color),
                           0 0 40px var(--neon-color),
                           0 0 50px var(--neon-color);
            }
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 2.5rem;
            }
            
            .btn-futuristic {
                padding: 1rem 2rem;
                font-size: 1rem;
            }

            .logo {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>
<body>
    <div id="particles-js"></div>
    <div class="container">
        <div class="logo"></div>
        <h1>Bienvenido al Sistema</h1>
        <div class="btn-container">
            <a href="login.php" class="btn btn-futuristic">Iniciar Sesión</a>
            <a href="register.php" class="btn btn-futuristic">Registrarse</a>
        </div>
    </div>

    <!-- Particles.js -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS('particles-js', {
            particles: {
                number: { value: 80, density: { enable: true, value_area: 800 } },
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
</body>
</html> 