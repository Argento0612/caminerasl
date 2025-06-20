<?php
session_start();

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Incluir conexión a la base de datos
require_once('../config/db.php');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Procedimiento</title>
    
    <!-- jQuery primero -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- jQuery UI después -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    
    <!-- Bootstrap y otros después -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            background: #0a0a0a;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            padding: 1rem;
            margin: 0 auto;
            box-sizing: border-box;
        }

        .form-title {
            text-align: center;
            margin-bottom: 2rem;
            font-size: clamp(1.5rem, 4vw, 2.5rem);
            font-weight: 600;
            color: #c8ff00;
            text-shadow: 0 0 10px rgba(200, 255, 0, 0.5);
            animation: glow 2s ease-in-out infinite alternate;
            padding: 0 1rem;
        }

        .form-container {
            background: rgba(20, 20, 20, 0.95);
            border-radius: 20px;
            padding: clamp(1rem, 3vw, 2rem);
            box-shadow: 0 0 20px rgba(200, 255, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(200, 255, 0, 0.1);
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
        }

        .form-section {
            background: rgba(30, 30, 30, 0.5);
            border-radius: 15px;
            padding: clamp(1rem, 2vw, 1.5rem);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(200, 255, 0, 0.1);
        }

        .section-title {
            color: #c8ff00;
            font-size: clamp(1.2rem, 3vw, 1.5rem);
            margin-bottom: 1.5rem;
            text-shadow: 0 0 5px rgba(200, 255, 0, 0.3);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: clamp(0.9rem, 2vw, 1rem);
        }

        select, .form-control {
            width: 100%;
            background: rgba(15, 15, 15, 0.9) !important;
            border: 1px solid rgba(200, 255, 0, 0.2) !important;
            color: #fff !important;
            border-radius: 10px !important;
            padding: 0.75rem !important;
            transition: all 0.3s ease !important;
            font-size: clamp(0.9rem, 2vw, 1rem);
        }

        .btn-submit {
            background: #c8ff00;
            color: #000;
            padding: clamp(0.8rem, 2vw, 1rem) clamp(2rem, 4vw, 3rem);
            border-radius: 15px;
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.3s ease;
            margin: 2rem auto 0;
            width: 100%;
            max-width: 300px;
            display: block;
            font-size: clamp(0.9rem, 2vw, 1rem);
        }

        .user-info {
            text-align: center;
            margin-bottom: 2rem;
            padding: clamp(0.8rem, 2vw, 1rem);
            background: rgba(30, 30, 30, 0.5);
            border-radius: 15px;
            border: 1px solid rgba(200, 255, 0, 0.1);
        }

        .user-info h4 {
            color: #c8ff00;
            margin-bottom: 0.5rem;
            font-size: clamp(1.1rem, 2.5vw, 1.3rem);
        }

        .user-info p {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 0;
            font-size: clamp(0.9rem, 2vw, 1rem);
        }

        /* Estilos para el autocompletado */
        .ui-autocomplete {
            z-index: 99999;
            background-color: #1a1a1a !important;
            border: 2px solid #c8ff00 !important;
            border-radius: 5px;
            padding: 0;
            max-height: 200px;
            overflow-y: auto;
            overflow-x: hidden;
            list-style: none;
            width: 90% !important;
            max-width: 500px;
        }

        .ui-menu-item {
            padding: 10px 15px;
            cursor: pointer;
            color: white !important;
            border-bottom: 1px solid rgba(200, 255, 0, 0.1);
            font-size: clamp(0.9rem, 2vw, 1rem);
        }

        /* Media Queries */
        @media (max-width: 768px) {
            .container {
                padding: 0.5rem;
            }

            .form-container {
                padding: 1rem;
            }

            .form-section {
                padding: 1rem;
            }

            .btn-submit {
                width: 100%;
                max-width: none;
            }
        }

        @media (max-width: 480px) {
            .form-title {
                margin-bottom: 1.5rem;
            }

            .user-info {
                margin-bottom: 1.5rem;
                padding: 0.8rem;
            }

            .form-section {
                margin-bottom: 1rem;
            }

            select, .form-control {
                padding: 0.6rem !important;
            }
        }

        /* Ajustes para pantallas muy pequeñas */
        @media (max-width: 320px) {
            .container {
                padding: 0.3rem;
            }

            .form-container {
                padding: 0.8rem;
            }

            .btn-submit {
                padding: 0.7rem 1.5rem;
                font-size: 0.9rem;
            }
        }

        /* Ajustes para pantallas grandes */
        @media (min-width: 1200px) {
            .form-container {
                max-width: 1000px;
                margin: 0 auto;
            }
        }

        .help-text {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
            margin-top: 1rem;
            text-align: center;
        }

        @keyframes glow {
            from {
                text-shadow: 0 0 5px rgba(200, 255, 0, 0.5);
            }
            to {
                text-shadow: 0 0 20px rgba(200, 255, 0, 0.8);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.8s ease-out;
        }

        .animate-slide-up {
            animation: slideUp 0.8s ease-out;
        }

        /* Estilos actualizados para el autocompletado */
        .ui-autocomplete {
            z-index: 99999;
            background-color: #1a1a1a !important;
            border: 2px solid #c8ff00 !important;
            border-radius: 5px;
            padding: 0;
            max-height: 200px;
            overflow-y: auto;
            overflow-x: hidden;
            list-style: none;
        }

        .ui-menu-item:hover,
        .ui-menu-item.ui-state-focus,
        .ui-menu-item.ui-state-active {
            background: rgba(200, 255, 0, 0.2) !important;
            color: #c8ff00 !important;
            border: none;
            margin: 0;
        }

        .ui-helper-hidden-accessible {
            display: none;
        }

        /* Estilo para la opción de crear nuevo */
        .ui-menu-item.create-new {
            border-top: 2px solid #c8ff00;
            background: rgba(200, 255, 0, 0.1);
            font-style: italic;
        }

        .ui-menu-item.create-new:hover {
            background: rgba(200, 255, 0, 0.3) !important;
        }

        /* Tooltip de ayuda */
        .field-tooltip {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.8rem;
            margin-top: 5px;
            display: block;
        }

        /* Estilos para SweetAlert */
        .swal2-popup {
            background: #1a1a1a !important;
            border: 2px solid #c8ff00 !important;
            color: #fff !important;
        }

        .swal2-title {
            color: #c8ff00 !important;
        }

        .swal2-html-container {
            color: #fff !important;
        }

        .swal2-confirm {
            background: #c8ff00 !important;
            color: #000 !important;
        }
    </style>
</head>
<body>
    <div class="container animate-fade-in">
        <h1 class="form-title">PROCEDIMIENTO</h1>
        
        <div class="form-container animate-slide-up">
            <form id="procedimientoForm" method="POST" action="procesar_procedimiento.php">
                <!-- Sección de Fecha y Hora -->
                <div class="form-section">
                    <h3 class="section-title">Fecha y Hora</h3>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fecha" class="form-label">Fecha del Procedimiento</label>
                            <input 
                                type="date" 
                                class="form-control" 
                                id="fecha" 
                                name="fecha" 
                                required
                            >
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="hora" class="form-label">Hora del Procedimiento</label>
                            <input 
                                type="time" 
                                class="form-control" 
                                id="hora" 
                                name="hora" 
                                required
                            >
                        </div>
                    </div>
                    <div class="help-text">
                        <i class="fas fa-clock"></i>
                        Indique la fecha y hora en que se realizó el procedimiento
                    </div>
                </div>

                <!-- Sección de Lugar -->
                <div class="form-section">
                    <h3 class="section-title">Lugar</h3>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="lugar" class="form-label">Ubicación</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="lugar" 
                                name="lugar" 
                                placeholder="Ingrese la ubicación"
                                required
                            >
                            <div class="help-text">
                                <i class="fas fa-map-marker-alt"></i>
                                Especifique el lugar donde se realiza el procedimiento
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Causa -->
                <div class="form-section">
                    <h3 class="section-title">Causa</h3>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="causa" class="form-label">Motivo del Procedimiento</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="causa" 
                                name="causa" 
                                placeholder="Detalle el motivo"
                                required
                            >
                            <div class="help-text">
                                <i class="fas fa-file-alt"></i>
                                Describa la causa que originó el procedimiento
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Detalle -->
                <div class="form-section">
                    <h3 class="section-title">Detalle</h3>
                    <div class="mb-3">
                        <label for="detalle" class="form-label">Descripción del Procedimiento</label>
                        <textarea 
                            class="form-control" 
                            id="detalle" 
                            name="detalle" 
                            rows="4" 
                            placeholder="Describa detalladamente el procedimiento realizado"
                            required
                        ></textarea>
                        <div class="help-text">
                            <i class="fas fa-align-left"></i>
                            Proporcione una descripción completa del procedimiento
                        </div>
                    </div>
                </div>

                <!-- Sección de Dependencia a Cargo -->
                <div class="form-section">
                    <h3 class="section-title">Dependencia a Cargo</h3>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <input 
                                type="text" 
                                class="form-control" 
                                id="dependencia" 
                                name="dependencia" 
                                placeholder="Ingrese la dependencia"
                                required
                            >
                            <div class="help-text">
                                <i class="fas fa-building"></i>
                                Indique la dependencia que continua con el procedimiento
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Guardar Procedimiento
                </button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Verificar que jQuery está disponible
        if (typeof jQuery != 'undefined') {
            console.log('jQuery está cargado correctamente');
        } else {
            console.error('jQuery no está cargado');
        }

        // Verificar que jQuery UI está disponible
        if (typeof jQuery.ui != 'undefined') {
            console.log('jQuery UI está cargado correctamente');
        } else {
            console.error('jQuery UI no está cargado');
        }

        $(document).ready(function() {
            console.log('DOM está listo');

            function showSuccessAlert(fieldName, value) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Creado con éxito!',
                    text: `Se ha creado un nuevo registro "${value}" en ${fieldName}`,
                    background: '#1a1a1a',
                    confirmButtonColor: '#c8ff00',
                    confirmButtonText: 'Aceptar',
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    customClass: {
                        popup: 'animated fadeInRight'
                    }
                });
            }

            function showErrorAlert(fieldName, value) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: `El registro "${value}" ya existe en ${fieldName}. No se pueden crear duplicados.`,
                    background: '#1a1a1a',
                    confirmButtonColor: '#c8ff00',
                    confirmButtonText: 'Entendido',
                    customClass: {
                        popup: 'animated fadeInRight'
                    }
                });
            }

            function getFieldLabel(fieldId) {
                switch(fieldId) {
                    case 'lugar':
                        return 'Ubicación';
                    case 'causa':
                        return 'Causa';
                    case 'dependencia':
                        return 'Dependencia';
                    default:
                        return fieldId;
                }
            }

            async function checkIfExists(fieldName, value) {
                try {
                    const response = await $.ajax({
                        url: '../controllers/get_autocomplete.php',
                        method: 'GET',
                        dataType: 'json',
                        data: {
                            term: value,
                            field: fieldName,
                            check_exists: true
                        }
                    });
                    return response.exists;
                } catch (error) {
                    console.error('Error al verificar duplicados:', error);
                    return false;
                }
            }

            async function createNewLugar(value) {
                try {
                    const response = await $.ajax({
                        url: '../controllers/guardar_lugar.php',
                        method: 'POST',
                        data: {
                            nombre_lugar: value
                        }
                    });
                    
                    if (response.success) {
                        return true;
                    } else {
                        throw new Error(response.message || 'Error al guardar el lugar');
                    }
                } catch (error) {
                    console.error('Error al crear lugar:', error);
                    return false;
                }
            }

            async function createNewCausa(value) {
                try {
                    const response = await $.ajax({
                        url: '../controllers/guardar_causa.php',
                        method: 'POST',
                        data: {
                            nombre_causa: value
                        }
                    });
                    
                    if (response.success) {
                        return true;
                    } else {
                        throw new Error(response.message || 'Error al guardar la causa');
                    }
                } catch (error) {
                    console.error('Error al crear causa:', error);
                    return false;
                }
            }

            async function createNewDependencia(value) {
                try {
                    const response = await $.ajax({
                        url: '../controllers/guardar_dependencia.php',
                        method: 'POST',
                        data: {
                            nombre_dependencia: value
                        }
                    });
                    
                    if (response.success) {
                        return true;
                    } else {
                        throw new Error(response.message || 'Error al guardar la dependencia');
                    }
                } catch (error) {
                    console.error('Error al crear dependencia:', error);
                    return false;
                }
            }

            function setupAutocomplete(fieldId, fieldName) {
                var $field = $('#' + fieldId);
                
                if ($field.length === 0) {
                    console.error('Campo no encontrado:', fieldId);
                    return;
                }

                $field.after('<span class="field-tooltip">Escriba para buscar o crear un nuevo registro</span>');

                try {
                    $field.autocomplete({
                        source: function(request, response) {
                            $.ajax({
                                url: '../controllers/get_autocomplete.php',
                                method: 'GET',
                                dataType: 'json',
                                data: {
                                    term: request.term,
                                    field: fieldName
                                },
                                success: function(data) {
                                    if (data.error) {
                                        console.error('Error del servidor:', data.error);
                                        return;
                                    }

                                    var term = request.term;
                                    var exists = false;

                                    if (Array.isArray(data)) {
                                        exists = data.some(function(item) {
                                            return item.toLowerCase() === term.toLowerCase();
                                        });
                                    }

                                    if (!exists && term.trim() !== '') {
                                        if (!Array.isArray(data)) {
                                            data = [];
                                        }
                                        data.push({
                                            label: 'Crear nuevo: "' + term + '"',
                                            value: term,
                                            isNew: true
                                        });
                                    }

                                    response(data);
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error en la petición AJAX:', error);
                                    console.error('Estado:', status);
                                    console.error('Respuesta:', xhr.responseText);
                                }
                            });
                        },
                        minLength: 1,
                        delay: 300,
                        autoFocus: false,
                        classes: {
                            "ui-autocomplete": "highlight"
                        },
                        select: async function(event, ui) {
                            if (ui.item.isNew) {
                                // Verificar si ya existe
                                const exists = await checkIfExists(fieldName, ui.item.value);
                                if (exists) {
                                    showErrorAlert(getFieldLabel(fieldId), ui.item.value);
                                    event.preventDefault();
                                    $field.val('');
                                    return false;
                                }
                                
                                let saved = false;
                                // Manejar la creación según el tipo de campo
                                if (fieldName === 'lugar') {
                                    saved = await createNewLugar(ui.item.value);
                                } else if (fieldName === 'causa') {
                                    saved = await createNewCausa(ui.item.value);
                                } else if (fieldName === 'dependencia') {
                                    saved = await createNewDependencia(ui.item.value);
                                }

                                if (!saved) {
                                    showErrorAlert(getFieldLabel(fieldId), `Error al guardar ${fieldName}`);
                                    event.preventDefault();
                                    $field.val('');
                                    return false;
                                }
                                
                                showSuccessAlert(getFieldLabel(fieldId), ui.item.value);
                                $field.val(ui.item.value);
                                return false;
                            }
                        }
                    });
                } catch (error) {
                    console.error('Error al inicializar autocompletado:', error);
                }
            }

            // Inicializar autocompletado para todos los campos
            setupAutocomplete('lugar', 'lugar');
            setupAutocomplete('causa', 'causa');
            setupAutocomplete('dependencia', 'dependencia');
        });
    </script>
</body>
</html> 