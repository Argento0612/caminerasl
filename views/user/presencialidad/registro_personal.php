<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Personal - Seguridad Vial</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Choices.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <style>
        :root {
            --neon-color: #ccff00;
            --bg-dark: #1a1a1a;
            --sidebar-dark: rgba(0, 0, 0, 0.95);
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-dark);
            color: white;
        }

        .form-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 3rem;
            background-color: var(--sidebar-dark);
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(204, 255, 0, 0.1);
            border: 2px solid var(--neon-color);
        }

        .form-title {
            color: var(--neon-color);
            margin-bottom: 3rem;
            text-align: center;
            font-weight: 700;
            text-shadow: 0 0 10px var(--neon-color);
            width: 100%;
            display: block;
            animation: neon-glow 2.5s ease-in-out infinite alternate;
        }

        @keyframes neon-glow {
            0% {
                text-shadow: 0 0 10px #ccff00, 0 0 20px #ccff00, 0 0 30px #ccff00;
            }
            100% {
                text-shadow: 0 0 20px #ccff00, 0 0 40px #ccff00, 0 0 60px #ccff00;
            }
        }

        .section-title {
            color: var(--neon-color);
            margin-bottom: 2rem;
            padding-bottom: 0.8rem;
            border-bottom: 2px solid var(--neon-color);
            text-shadow: 0 0 5px var(--neon-color);
        }

        .form-section {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 2rem;
            margin-bottom: 3rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(204, 255, 0, 0.1);
            border: 1px solid var(--neon-color);
        }

        .required-field::after {
            content: " *";
            color: var(--neon-color);
        }

        .form-label {
            color: white;
            margin-bottom: 0.8rem;
            display: block;
        }

        .form-control {
            background-color: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--neon-color);
            color: white;
            padding: 0.8rem;
            margin-bottom: 1rem;
        }

        .form-control:focus {
            background-color: rgba(0, 0, 0, 0.5);
            border-color: var(--neon-color);
            box-shadow: 0 0 0 0.2rem rgba(204, 255, 0, 0.25);
            color: white;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        /* Estilos específicos para el select */
        select.form-select {
            background-color: #111 !important;
            border: 2px solid var(--neon-color) !important;
            color: var(--neon-color) !important;
            padding: 0.8rem;
            margin-bottom: 1rem;
            width: 100%;
            font-weight: bold;
            box-shadow: 0 0 8px 0 var(--neon-color, #ccff00);
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' fill='none' stroke='%23ccff00' stroke-width='3' stroke-linecap='round' stroke-linejoin='round' viewBox='0 0 24 24'%3E%3Cpolyline points='6 9 12 15 18 9' style='stroke:%23ccff00'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1.5rem;
        }

        select.form-select:focus {
            border-color: var(--neon-color);
            box-shadow: 0 0 0 0.2rem rgba(204,255,0,0.5);
            outline: none;
            color: var(--neon-color);
        }

        /* Opciones (solo visible en Firefox y algunos navegadores) */
        select.form-select option {
            background: #111 !important;
            color: #fff !important;
            font-weight: bold;
        }

        /* Placeholder */
        select.form-select option[value=""] {
            color: #fff !important;
            background: #111 !important;
        }

        .btn-primary {
            background-color: transparent;
            border: 2px solid var(--neon-color);
            color: var(--neon-color);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--neon-color);
            color: var(--bg-dark);
            box-shadow: 0 0 15px var(--neon-color);
            text-shadow: 0 0 5px var(--neon-color);
        }

        .btn-secondary {
            background-color: transparent;
            border: 2px solid var(--neon-color);
            color: var(--neon-color);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: var(--neon-color);
            color: var(--bg-dark);
            box-shadow: 0 0 15px var(--neon-color);
            text-shadow: 0 0 5px var(--neon-color);
        }

        .datetime-section {
            background-color: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--neon-color);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .current-time {
            color: var(--neon-color);
            text-shadow: 0 0 5px var(--neon-color);
        }

        .current-date {
            color: white;
        }

        .personal-list {
            background-color: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--neon-color);
            padding: 1rem;
            margin-top: 1rem;
        }

        .personal-item {
            border-bottom: 1px solid rgba(204, 255, 0, 0.2);
            color: white;
            padding: 1rem;
        }

        .remove-personal {
            color: var(--neon-color);
            transition: all 0.3s ease;
        }

        .remove-personal:hover {
            color: #fff;
            text-shadow: 0 0 5px var(--neon-color);
            transform: scale(1.1);
        }

        .form-check-input {
            background-color: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--neon-color);
        }

        .form-check-input:checked {
            background-color: var(--neon-color);
            border-color: var(--neon-color);
        }

        .form-check-label {
            color: white;
            padding-top: 0.3rem;
        }

        /* Estilos para el autocompletado */
        .autocomplete-suggestions {
            background-color: var(--sidebar-dark);
            border: 1px solid var(--neon-color);
            color: white;
        }

        .autocomplete-suggestion {
            color: white;
        }

        .autocomplete-selected {
            background: var(--neon-color);
            color: var(--bg-dark);
        }

        .autocomplete-suggestion:hover {
            background: rgba(204, 255, 0, 0.1);
        }

        /* Grid overlay */
        .grid-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: linear-gradient(var(--neon-color) 1px, transparent 1px),
                            linear-gradient(90deg, var(--neon-color) 1px, transparent 1px);
            background-size: 50px 50px;
            opacity: 0.05;
            z-index: 0;
            pointer-events: none;
        }

        /* Animaciones */
        @keyframes glow {
            from {
                text-shadow: 0 0 5px var(--neon-color),
                           0 0 10px var(--neon-color),
                           0 0 15px var(--neon-color);
            }
            to {
                text-shadow: 0 0 10px var(--neon-color),
                           0 0 20px var(--neon-color),
                           0 0 30px var(--neon-color);
            }
        }

        .form-title {
            animation: glow 2s ease-in-out infinite alternate;
        }

        .mb-3 {
            margin-bottom: 1.5rem !important;
        }

        /* Ajustes para los botones de acción */
        .d-grid.gap-2.d-md-flex.justify-content-md-end {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(204, 255, 0, 0.2);
        }

        /* Ajuste para el input group */
        .input-group {
            margin-bottom: 1.5rem;
        }

        .input-group .form-control {
            margin-bottom: 0;
        }

        /* Ajuste para la sección de ausentes */
        #seccionAusentes {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(204, 255, 0, 0.2);
        }

        /* Ajuste para el textarea */
        textarea.form-control {
            min-height: 100px;
        }

        .choices__inner {
            background: #111 !important;
            border: 2px solid #ccff00 !important;
            color: #ccff00 !important;
            font-weight: bold;
            box-shadow: 0 0 8px 0 #ccff00;
        }
        .choices__list--dropdown, .choices__list[aria-expanded] {
            background: #111 !important;
            border: 2px solid #ccff00 !important;
        }
        .choices__item--selectable {
            color: #ccff00 !important;
            background: #111 !important;
        }
        .choices__item--selectable.is-highlighted {
            background: #ccff00 !important;
            color: #111 !important;
        }
        .choices__placeholder {
            color: #888 !important;
        }

        /* Choices.js: opciones del dropdown */
        .choices__list--dropdown .choices__item,
        .choices__list[aria-expanded] .choices__item {
            background: #111 !important;
            color: #fff !important;
            font-weight: bold;
        }
        .choices__item--selectable.is-highlighted {
            background: #222 !important;
            color: #ccff00 !important;
        }

        /* Scrollbar personalizada para select y Choices.js */
        select.form-select, .choices__list--dropdown, .choices__list[aria-expanded] {
            scrollbar-width: thin;
            scrollbar-color: #ccff00 #111;
        }
        select.form-select::-webkit-scrollbar, .choices__list--dropdown::-webkit-scrollbar, .choices__list[aria-expanded]::-webkit-scrollbar {
            width: 10px;
            background: #111;
        }
        select.form-select::-webkit-scrollbar-thumb, .choices__list--dropdown::-webkit-scrollbar-thumb, .choices__list[aria-expanded]::-webkit-scrollbar-thumb {
            background: #ccff00;
            border-radius: 8px;
        }
        select.form-select::-webkit-scrollbar-thumb:hover, .choices__list--dropdown::-webkit-scrollbar-thumb:hover, .choices__list[aria-expanded]::-webkit-scrollbar-thumb:hover {
            background: #bada00;
        }

        /* Flecha amarilla personalizada para el select */
        .select-wrapper {
            position: relative;
        }
        .select-wrapper select.form-select {
            padding-right: 2.5rem;
            background-image: none !important;
        }
        .select-wrapper::after {
            content: '';
            position: absolute;
            top: 50%;
            right: 1.2rem;
            width: 1.2rem;
            height: 1.2rem;
            pointer-events: none;
            transform: translateY(-50%);
            background: url("data:image/svg+xml,%3Csvg width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23ccff00' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") no-repeat center/contain;
        }
        /* Ocultar flecha nativa en navegadores modernos */
        .select-wrapper select.form-select::-ms-expand {
            display: none;
        }
        .select-wrapper select.form-select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        /* Choices.js: flecha amarilla personalizada */
        .choices--custom .choices__inner {
            position: relative;
        }
        .choices--custom .choices__inner::after {
            content: '';
            position: absolute;
            top: 50%;
            right: 1.2rem;
            width: 1.2rem;
            height: 1.2rem;
            pointer-events: none;
            transform: translateY(-50%);
            background: url("data:image/svg+xml,%3Csvg width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23ccff00' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") no-repeat center/contain;
        }
        .choices--custom .choices[data-type*=select-one]::after {
            display: none !important;
        }
    </style>
</head>
<body>
    <!-- Grid Overlay -->
    <div class="grid-overlay"></div>
    
    <div class="container">
        <div class="form-container">
            <h1 class="form-title">Registro de Personal</h1>
            
            <form id="registroPersonalForm" method="POST" action="/caminerasl/views/user/presencialidad/procesar_registro.php">
                <!-- Sección de Fecha y Hora -->
                <div class="form-section">
                    <h2 class="section-title">Fecha y Hora</h2>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_registro" class="form-label required-field">Fecha de Registro</label>
                                <input type="date" class="form-control" id="fecha_registro" name="fecha_registro" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hora_registro" class="form-label required-field">Hora de Registro</label>
                                <input type="time" class="form-control" id="hora_registro" name="hora_registro" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Departamento -->
                <div class="form-section">
                    <h2 class="section-title">Departamento</h2>
                    <div class="mb-3 select-wrapper">
                        <label for="departamento" class="form-label required-field">Seleccione el Departamento</label>
                        <select class="form-select" id="departamento" name="departamento" required>
                            <option value="" disabled selected>-- Seleccione un Departamento --</option>
                            <option value="DEPARTAMENTO DE SEGURIDAD VIAL N°1">DEPARTAMENTO DE SEGURIDAD VIAL N°1</option>
                            <option value="DEPARTAMENTO DE SEGURIDAD VIAL N°2">DEPARTAMENTO DE SEGURIDAD VIAL N°2</option>
                            <option value="DEPARTAMENTO DE SEGURIDAD VIAL N°3">DEPARTAMENTO DE SEGURIDAD VIAL N°3</option>
                            <option value="DEPARTAMENTO DE SEGURIDAD VIAL N°4">DEPARTAMENTO DE SEGURIDAD VIAL N°4</option>
                            <option value="DEPARTAMENTO DE SEGURIDAD VIAL N°5">DEPARTAMENTO DE SEGURIDAD VIAL N°5</option>
                            <option value="DEPARTAMENTO TRANSITO">DEPARTAMENTO TRANSITO</option>
                            <option value="DEPARTAMENTO BRIGADA ESPECIALES">DEPARTAMENTO BRIGADA ESPECIALES</option>
                            <option value="ACCIDENTOLOGIA VIAL SAN LUIS">ACCIDENTOLOGIA VIAL SAN LUIS</option>
                            <option value="ACCIDENTOLOGIA VIAL VILLA MERCEDES">ACCIDENTOLOGIA VIAL VILLA MERCEDES</option>
                            <option value="OFICINA DIRECCION GENERAL">OFICINA DIRECCION GENERAL</option>
                            <option value="OFICINA DEPARTAMENTO ACCIDENTOLOGIA VIAL">OFICINA DEPARTAMENTO ACCIDENTOLOGIA VIAL</option>
                            <option value="OFICINA DEPARTAMENTO VIAL 1">OFICINA DEPARTAMENTO VIAL 1</option>
                            <option value="OFICINA TRANSITO">OFICINA TRANSITO</option>
                            <option value="OFICINA ESTADISTICAS">OFICINA ESTADISTICAS</option>
                            <option value="OFICINA OPERACIONES">OFICINA OPERACIONES</option>
                            <option value="OFICINA LOGISTICA">OFICINA LOGISTICA</option>
                        </select>
                    </div>
                </div>

                <!-- Sección de Lugar de Servicio -->
                <div class="form-section">
                    <h2 class="section-title">Lugar de Servicio</h2>
                    <div class="mb-3">
                        <label for="lugar_servicio" class="form-label required-field">Lugar de Servicio</label>
                        <input type="text" class="form-control" id="lugar_servicio" name="lugar_servicio" required 
                               placeholder="Ej: PUENTE DERIVADOR" list="lugares">
                        <datalist id="lugares"></datalist>
                    </div>
                </div>

                <!-- Sección de Personal de Guardia (restaurada) -->
                <div class="form-section">
                    <h2 class="section-title">Personal de Guardia</h2>
                    <div class="mb-3">
                        <label for="personal_guardia" class="form-label required-field">Agregar Personal</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="personal_guardia" 
                                   placeholder="Ej: Oficial Pérez Juan" list="personal_guardia_list">
                            <button type="button" class="btn btn-primary" id="agregarPersonal">
                                <i class="fas fa-plus"></i> Agregar
                            </button>
                        </div>
                        <datalist id="personal_guardia_list"></datalist>
                    </div>
                    <div class="personal-list" id="listaPersonal">
                        <!-- Aquí se mostrará el personal agregado -->
                    </div>
                    <input type="hidden" name="personal_guardia" id="personal_guardia_hidden">
                </div>

                <!-- Sección de Personal Ausente -->
                <div class="form-section">
                    <h2 class="section-title">Personal Ausente</h2>
                    <div class="mb-3">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="hay_ausentes" name="hay_ausentes">
                            <label class="form-check-label" for="hay_ausentes">
                                Hay personal ausente
                            </label>
                        </div>
                        <div id="seccionAusentes" style="display: none;">
                            <div class="mb-3">
                                <label for="personal_ausente" class="form-label">Personal Ausente</label>
                                <input type="text" class="form-control" id="personal_ausente" name="personal_ausente" 
                                       placeholder="Ej: Oficial García María">
                            </div>
                            <div class="mb-3">
                                <label for="motivo_ausencia" class="form-label">Motivo de Ausencia</label>
                                <textarea class="form-control" id="motivo_ausencia" name="motivo_ausencia" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Modalidad de Trabajo -->
                <div class="form-section">
                    <h2 class="section-title">Modalidad de Trabajo</h2>
                    <div class="mb-3">
                        <label class="form-label required-field">Seleccione la Modalidad</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="modalidad" id="modalidad24" value="24 HS" required>
                            <label class="form-check-label" for="modalidad24">24 HS</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="modalidad" id="modalidad48" value="48 HS" required>
                            <label class="form-check-label" for="modalidad48">48 HS</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="modalidad" id="modalidadOficina" value="OFICINA" required>
                            <label class="form-check-label" for="modalidadOficina">OFICINA</label>
                        </div>
                    </div>
                </div>

                <!-- Sección de Personal de Horas I.P. (al final) -->
                <div class="form-section">
                    <h2 class="section-title">Personal de Horas I.P. <span style='font-weight:normal; font-size:0.8em;'>(Agregue el personal que al salir de guardia se quedara a realizar Hora I.P.)</span></h2>
                    <div class="mb-3">
                        <label for="personal_horas_ip" class="form-label required-field">Agregar Personal</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="input_personal_horas_ip" 
                                   placeholder="Ej: Oficial López Ana" list="personal_horas_ip_list">
                            <button type="button" class="btn btn-primary" id="agregarPersonalHorasIP">
                                <i class="fas fa-plus"></i> Agregar
                            </button>
                        </div>
                        <datalist id="personal_horas_ip_list"></datalist>
                    </div>
                    <div class="personal-list" id="listaPersonalHorasIP">
                        <!-- Aquí se mostrará el personal agregado -->
                    </div>
                    <input type="hidden" name="personal_horas_ip" id="personal_horas_ip_hidden">
                </div>

                <!-- Botones de Acción -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="button" class="btn btn-secondary me-md-2" onclick="limpiarFormulario()">
                        <i class="fas fa-eraser"></i> Limpiar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Registro
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Choices.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Auto-completado de fecha y hora
        document.addEventListener('DOMContentLoaded', () => {
            const fechaInput = document.getElementById('fecha_registro');
            const horaInput = document.getElementById('hora_registro');

            const ahora = new Date();
            
            // Obtener fecha en formato YYYY-MM-DD
            const fecha = ahora.toISOString().split('T')[0];
            fechaInput.value = fecha;

            // Obtener hora en formato HH:MM
            const horas = ahora.getHours().toString().padStart(2, '0');
            const minutos = ahora.getMinutes().toString().padStart(2, '0');
            horaInput.value = `${horas}:${minutos}`;
        });

        // Gestión de lugares de servicio
        const lugarServicioInput = document.getElementById('lugar_servicio');
        const dataListLugares = document.getElementById('lugares');

        // Cargar lugares desde la base de datos
        function cargarLugares() {
            fetch('/caminerasl/views/user/controlers/get_lugares.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        dataListLugares.innerHTML = '';
                        data.results.forEach(lugar => {
                            const option = document.createElement('option');
                            option.value = lugar.text;
                            dataListLugares.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Cargar lugares al iniciar
        cargarLugares();

        // Guardar nuevo lugar
        lugarServicioInput.addEventListener('blur', () => {
            const nuevoLugar = lugarServicioInput.value.trim().toUpperCase();
            if (nuevoLugar) {
                fetch('/caminerasl/views/user/controlers/guardar_lugar.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `nombre_lugar=${encodeURIComponent(nuevoLugar)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        cargarLugares();
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });

        // Autocompletado de Personal de Guardia desde la base de datos
        function cargarPersonalGuardia() {
            fetch('/caminerasl/views/user/controlers/get_personal_guardia.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        dataListPersonal.innerHTML = '';
                        data.results.forEach(nombre => {
                            const option = document.createElement('option');
                            option.value = nombre;
                            dataListPersonal.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
        }
        cargarPersonalGuardia();

        // Gestión de personal de guardia
        const personalGuardiaInput = document.getElementById('personal_guardia');
        const dataListPersonal = document.getElementById('personal_guardia_list');
        const listaPersonal = document.getElementById('listaPersonal');
        const personalAgregado = new Set();

        document.getElementById('agregarPersonal').addEventListener('click', () => {
            const nuevoPersonal = personalGuardiaInput.value.trim();
            if (nuevoPersonal && !personalAgregado.has(nuevoPersonal)) {
                // Agregar a la lista visual
                const div = document.createElement('div');
                div.className = 'personal-item';
                div.innerHTML = `
                    <span>${nuevoPersonal}</span>
                    <i class="fas fa-times remove-personal" data-personal="${nuevoPersonal}"></i>
                `;
                listaPersonal.appendChild(div);
                personalAgregado.add(nuevoPersonal);

                // Actualizar el input oculto con todos los nombres
                let personalList = Array.from(document.querySelectorAll('#listaPersonal .personal-item span')).map(e => e.textContent);
                document.getElementById('personal_guardia_hidden').value = personalList.join(', ');

                // Limpiar input y devolver el foco
                personalGuardiaInput.value = '';
                personalGuardiaInput.focus();
            }
        });

        // Eliminar personal de la lista
        listaPersonal.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-personal')) {
                const personal = e.target.dataset.personal;
                e.target.parentElement.remove();
                personalAgregado.delete(personal);
                // Actualizar el input oculto después de eliminar
                let personalList = Array.from(document.querySelectorAll('#listaPersonal .personal-item span')).map(e => e.textContent);
                document.getElementById('personal_guardia_hidden').value = personalList.join(', ');
            }
        });

        // Gestión de personal ausente
        document.getElementById('hay_ausentes').addEventListener('change', function() {
            const seccionAusentes = document.getElementById('seccionAusentes');
            seccionAusentes.style.display = this.checked ? 'block' : 'none';
        });

        // Función para limpiar el formulario
        function limpiarFormulario() {
            document.getElementById('registroPersonalForm').reset();
            document.getElementById('seccionAusentes').style.display = 'none';
            document.getElementById('listaPersonalHorasIP').innerHTML = '';
            personalAgregado.clear();
        }

        // Validación del formulario
        document.getElementById('registroPersonalForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var form = this;
            var formData = new FormData(form);

            // Log para verificar la modalidad
            const modalidadSeleccionada = document.querySelector('input[name="modalidad"]:checked');
            console.log('Modalidad seleccionada:', modalidadSeleccionada ? modalidadSeleccionada.value : 'No seleccionada');
            
            // Validar que haya al menos un personal de guardia
            let personalGuardiaList = Array.from(document.querySelectorAll('#listaPersonal .personal-item span')).map(e => e.textContent);
            if (personalGuardiaList.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe agregar al menos un personal de guardia',
                    background: '#1a1a1a',
                    color: '#fff',
                    confirmButtonColor: '#ccff00'
                });
                return;
            }
            document.getElementById('personal_guardia_hidden').value = personalGuardiaList.join(', ');

            // El campo de Horas I.P. es opcional
            let personalHorasIPList = Array.from(document.querySelectorAll('#listaPersonalHorasIP .personal-item span')).map(e => e.textContent);
            document.getElementById('personal_horas_ip_hidden').value = personalHorasIPList.join(', ');

            // Agregar validación de modalidad antes de enviar el formulario
            if (!modalidadSeleccionada) {
                e.preventDefault();
                alert('Por favor seleccione una modalidad de trabajo');
                return false;
            }
            console.log('Modalidad seleccionada:', modalidadSeleccionada.value);

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Registro Exitoso!',
                        text: data.message,
                        background: '#1a1a1a',
                        color: '#fff',
                        iconColor: '#ccff00',
                        confirmButtonColor: '#ccff00'
                    }).then(() => {
                        window.location.href = '/caminerasl/views/dashboard.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                        background: '#1a1a1a',
                        color: '#fff',
                        confirmButtonColor: '#ccff00'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error en la conexión: ' + error.message,
                    background: '#1a1a1a',
                    color: '#fff',
                    confirmButtonColor: '#ccff00'
                });
            });
        });

        var departamentoSelect = document.getElementById('departamento');
        if (departamentoSelect) {
            new Choices(departamentoSelect, {
                searchEnabled: false,
                itemSelectText: '',
                shouldSort: false,
                placeholder: true,
                placeholderValue: '-- Seleccione un Departamento --',
                classNames: {
                    containerOuter: 'choices--custom',
                }
            });
        }

        // Gestión de personal de Horas I.P. (independiente)
        const inputPersonalHorasIP = document.getElementById('input_personal_horas_ip');
        const dataListPersonalHorasIP = document.getElementById('personal_horas_ip_list');
        const listaPersonalHorasIP = document.getElementById('listaPersonalHorasIP');
        const personalHorasIPAgregado = new Set();

        document.getElementById('agregarPersonalHorasIP').addEventListener('click', () => {
            const nuevoPersonal = inputPersonalHorasIP.value.trim();
            if (nuevoPersonal && !personalHorasIPAgregado.has(nuevoPersonal)) {
                // Agregar a la lista visual
                const div = document.createElement('div');
                div.className = 'personal-item';
                div.innerHTML = `
                    <span>${nuevoPersonal}</span>
                    <i class='fas fa-times remove-personal' data-personal='${nuevoPersonal}'></i>
                `;
                listaPersonalHorasIP.appendChild(div);
                personalHorasIPAgregado.add(nuevoPersonal);

                // Actualizar el input oculto con todos los nombres
                let personalList = Array.from(document.querySelectorAll('#listaPersonalHorasIP .personal-item span')).map(e => e.textContent);
                document.getElementById('personal_horas_ip_hidden').value = personalList.join(', ');

                // Limpiar input y devolver el foco
                inputPersonalHorasIP.value = '';
                inputPersonalHorasIP.focus();
            }
        });

        // Eliminar personal de la lista de Horas I.P.
        listaPersonalHorasIP.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-personal')) {
                const personal = e.target.dataset.personal;
                e.target.parentElement.remove();
                personalHorasIPAgregado.delete(personal);
                // Actualizar el input oculto después de eliminar
                let personalList = Array.from(document.querySelectorAll('#listaPersonalHorasIP .personal-item span')).map(e => e.textContent);
                document.getElementById('personal_horas_ip_hidden').value = personalList.join(', ');
            }
        });
    </script>
</body>
</html>