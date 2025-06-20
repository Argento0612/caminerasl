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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Formulario de Operatividad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../public/neon-dark-theme.css">
    <style>
        :root {
            --neon-color: #c8ff00;
            --bg-dark: #1a1a1a;
            --sidebar-dark: rgba(0, 0, 0, 0.95);
        }

        /* Estilos base para asegurar el scroll */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #121212 !important;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            background-color: #121212 !important;
            flex: 1 0 auto;
            width: 100%;
            position: relative;
            z-index: 1;
        }

        .container {
            background-color: #121212 !important;
            max-width: 1200px;
            padding: 2rem;
            margin: 0 auto;
            position: relative;
        }

        .form-title {
            text-align: center;
            margin-bottom: 3rem;
            font-size: 2.5rem;
            font-weight: 600;
            color: var(--neon-color);
            text-shadow: 0 0 10px rgba(200, 255, 0, 0.5);
            animation: glow 2s ease-in-out infinite alternate;
        }

        /* Estilo para el contenedor principal del formulario */
        .form-container {
            background-color: #1a1a1a !important;
            border: 1px solid rgba(200, 255, 0, 0.2);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
            margin-bottom: 2rem;
            height: auto;
            overflow: visible;
        }

        /* Estilo para las secciones del formulario */
        .form-section {
            background-color: #1a1a1a !important;
            border: 1px solid rgba(200, 255, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .form-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(200, 255, 0, 0.2);
        }

        /* Estilo para los títulos de sección */
        .section-title {
            color: var(--neon-color);
            border-bottom: 1px solid rgba(200, 255, 0, 0.2);
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Estilo para las etiquetas */
        .form-label {
            color: #ffffff;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label i {
            color: var(--neon-color);
        }

        /* Estilo para el fondo general */
        body {
            background-color: #121212 !important;
        }

        .main-content {
            background-color: #121212 !important;
        }

        /* Contenedor principal */
        .container {
            background-color: #121212 !important;
        }

        /* Estilo para los grupos de campos */
        .form-group {
            background-color: #1a1a1a !important;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        /* Estilos base para todos los campos de entrada */
        input[type="text"],
        input[type="date"],
        input[type="time"],
        input[type="number"],
        input[type="email"],
        textarea,
        select,
        .form-control,
        .form-select,
        .select2-container .select2-selection,
        .select2-dropdown,
        .select2-search__field {
            background-color: #1a1a1a !important;
            color: #ffffff !important;
            border: 1px solid rgba(200, 255, 0, 0.2) !important;
        }

        /* Estilos para las opciones del select */
        select option {
            background-color: rgba(30, 30, 30, 0.95) !important;
            color: #fff !important;
        }

        /* Estilo para el select cuando está abierto */
        select:focus option:checked,
        select option:checked {
            background: rgba(200, 255, 0, 0.2) !important;
            color: #fff !important;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(30, 30, 30, 0.95) !important;
            border-color: var(--neon-color);
            box-shadow: 0 0 10px rgba(200, 255, 0, 0.3);
            color: #fff !important;
        }

        /* Estilo para el placeholder */
        .form-control::placeholder {
            color: #999 !important;
            opacity: 1;
        }

        .form-control::-webkit-input-placeholder {
            color: #999 !important;
                opacity: 1;
        }

        .form-control::-moz-placeholder {
            color: #999 !important;
                opacity: 1;
        }

        /* Estilos para el dropdown del select */
        select:focus,
        select:active {
            background-color: rgba(30, 30, 30, 0.95) !important;
            color: #fff !important;
        }

        /* Estilo para las opciones al pasar el mouse */
        select option:hover,
        select option:focus {
            background-color: rgba(200, 255, 0, 0.2) !important;
            color: #fff !important;
        }

        .btn-add {
            background: transparent;
            border: 1px solid var(--neon-color);
            color: var(--neon-color);
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-add:hover {
            background: var(--neon-color);
            color: var(--bg-dark);
            box-shadow: 0 0 15px rgba(200, 255, 0, 0.3);
        }

        .form-check-input {
            background-color: rgba(30, 30, 30, 0.7);
            border-color: rgba(200, 255, 0, 0.3);
        }

        .form-check-input:checked {
            background-color: var(--neon-color);
            border-color: var(--neon-color);
        }

        .form-check-label {
            color: #fff;
        }

        .radio-group {
            display: flex;
            gap: 1.5rem;
            margin: 1rem 0;
        }

        /* Estilos responsivos para radio buttons */
        @media (max-width: 768px) {
            .radio-group.flex-column {
                gap: 0.5rem;
            }

            .radio-group .form-check {
                width: 100%;
                padding: 0.5rem;
                border-radius: 0.25rem;
                transition: background-color 0.3s ease;
            }

            .radio-group .form-check:hover {
                background-color: rgba(200, 255, 0, 0.1);
            }

            .radio-group .form-check-label {
                display: flex;
                align-items: center;
                width: 100%;
                font-size: 0.95rem;
                line-height: 1.2;
            }

            .radio-group .form-check-input {
                margin-top: 0;
                margin-right: 0.5rem;
            }

            .radio-group .form-check-input:checked + .form-check-label {
                color: var(--neon-color);
            }
        }

        .conditional-section {
            margin-left: 2rem;
            padding-left: 1rem;
            border-left: 2px solid rgba(200, 255, 0, 0.2);
            transition: all 0.3s ease;
        }

        .btn-submit {
            background: var(--neon-color);
            color: var(--bg-dark);
            border: none;
            padding: 1rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 2rem;
            width: 100%;
        }

        .btn-submit:hover {
            box-shadow: 0 0 20px rgba(200, 255, 0, 0.5);
            transform: translateY(-2px);
        }

        /* Modal Styles */
        .modal-content {
            background: var(--bg-dark);
            border: 1px solid var(--neon-color);
        }

        .modal-header {
            border-bottom: 1px solid rgba(200, 255, 0, 0.2);
            background: var(--bg-dark);
            color: var(--neon-color);
        }

        .modal-footer {
            border-top: 1px solid rgba(200, 255, 0, 0.2);
            background: var(--bg-dark);
        }

        .modal-title {
            color: var(--neon-color);
        }

        .modal-body {
            background: var(--bg-dark);
            color: #fff;
        }

        /* Estilos para autocompletado */
        .autocomplete-container {
            position: relative;
            width: 100%;
        }

        .autocomplete-items {
            position: absolute;
            border: 1px solid var(--neon-color);
            border-top: none;
            z-index: 99;
            top: 100%;
            left: 0;
            right: 0;
            border-radius: 0 0 5px 5px;
            max-height: 200px;
            overflow-y: auto;
            background: rgba(30, 30, 30, 0.95);
            backdrop-filter: blur(10px);
            display: none;
        }

        .autocomplete-items div {
            padding: 10px;
            cursor: pointer;
            color: #fff;
            border-bottom: 1px solid rgba(200, 255, 0, 0.1);
            transition: all 0.3s ease;
        }

        .autocomplete-items div:hover {
            background-color: rgba(200, 255, 0, 0.1);
        }

        .autocomplete-items div.selected {
            background-color: rgba(200, 255, 0, 0.2);
        }

        .autocomplete-active {
            background-color: rgba(200, 255, 0, 0.2) !important;
        }

        .highlight {
            color: var(--neon-color);
            text-shadow: 0 0 5px var(--neon-color);
        }

        /* Scrollbar personalizado para autocompletado */
        .autocomplete-items::-webkit-scrollbar {
            width: 6px;
        }

        .autocomplete-items::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 3px;
        }

        .autocomplete-items::-webkit-scrollbar-thumb {
            background: var(--neon-color);
            border-radius: 3px;
        }

        .autocomplete-items::-webkit-scrollbar-thumb:hover {
            background: rgba(200, 255, 0, 0.8);
        }

        /* Animación de carga */
        .loading::after {
            content: '';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            border: 2px solid rgba(200, 255, 0, 0.3);
            border-top: 2px solid var(--neon-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: translateY(-50%) rotate(0deg); }
            100% { transform: translateY(-50%) rotate(360deg); }
        }

        /* Estilos para las alertas */
        .swal2-popup {
            border: 1px solid var(--neon-color) !important;
            box-shadow: 0 0 20px rgba(200, 255, 0, 0.2) !important;
        }

        .swal2-title {
            color: var(--neon-color) !important;
            text-shadow: 0 0 10px rgba(200, 255, 0, 0.5) !important;
        }

        .swal2-content {
            color: #fff !important;
        }

        .swal2-confirm {
            background-color: var(--neon-color) !important;
            color: var(--bg-dark) !important;
            border: none !important;
            box-shadow: 0 0 10px rgba(200, 255, 0, 0.3) !important;
            transition: all 0.3s ease !important;
        }

        .swal2-confirm:hover {
            box-shadow: 0 0 20px rgba(200, 255, 0, 0.5) !important;
            transform: translateY(-2px) !important;
        }

        .swal2-timer-progress-bar {
            background: var(--neon-color) !important;
        }

        /* Animaciones para las alertas */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .autocomplete-wrapper {
            position: relative;
            width: 100%;
        }

        .suggestions-container {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.95);
            border: 1px solid var(--neon-color);
            border-top: none;
            border-radius: 0 0 4px 4px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .suggestions-container div {
            padding: 8px 12px;
            cursor: pointer;
            color: #fff;
        }

        .suggestions-container div:hover {
            background: rgba(200, 255, 0, 0.2);
        }

        .suggestions-container::-webkit-scrollbar {
            width: 6px;
        }

        .suggestions-container::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.3);
        }

        .suggestions-container::-webkit-scrollbar-thumb {
            background: var(--neon-color);
            border-radius: 3px;
        }

        /* Estilos personalizados para jQuery UI Autocomplete */
        .ui-autocomplete {
            background: rgba(20, 20, 20, 0.95);
            border: 1px solid var(--neon-color);
            border-radius: 0 0 4px 4px;
            max-height: 200px;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1000;
        }

        .ui-menu-item {
            padding: 8px 12px;
            color: #fff;
            cursor: pointer;
        }

        .ui-menu-item:hover,
        .ui-menu-item.ui-state-focus,
        .ui-menu-item.ui-state-active {
            background: rgba(200, 255, 0, 0.2);
            border: none;
            margin: 0;
        }

        .ui-autocomplete-loading {
            background: rgba(30, 30, 30, 0.7) url('data:image/gif;base64,R0lGODlhEAAQAPIAAP///wAAAMLCwkJCQgAAAGJiYoKCgpKSkiH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAEAAQAAADMwi63P4wyklrE2MIOggZnAdOmGYJRbExwroUmcG2LmDEwnHQLVsYOd2mBzkYDAdKa+dIAAAh+QQJCgAAACwAAAAAEAAQAAADNAi63P5OjCEgG4QMu7DmikRxQlFUYDEZIGBMRVsaqHwctXXf7WEYB4Ag1xjihkMZsiUkKhIAIfkECQoAAAAsAAAAABAAEAAAAzYIujIjK8pByJDMlFYvBoVjHA70GU7xSUJhmKtwHPAKzLO9HMaoKwJZ7Rf8AYPDDzKpZBqfvwQAIfkECQoAAAAsAAAAABAAEAAAAzMIumIlK8oyhpHsnFZfhYumCYUhDAQxRIdhHBGqRoKw0R8DYlJd8z0fMDgsGo/IpHI5TAAAIfkECQoAAAAsAAAAABAAEAAAAzIIunInK0rnZBTwGPNMgQwmdsNgXGJUlIWEuR5oWUIpz8pAEAMe6TwfwyYsGo/IpFKSAAAh+QQJCgAAACwAAAAAEAAQAAADMwi6IMKQORfjdOe82p4wGccc4CEuQradylesojEMBgsUc2G7sDX3lQGBMLAJibufbSlKAAAh+QQJCgAAACwAAAAAEAAQAAADMgi63P7wCRHZnFVdmgHu2nFwlWCI3WGc3TSWhUFGxTAUkGCbtgENBMJAEJsxgMLWzpEAACH5BAkKAAAALAAAAAAQABAAAAMyCLrc/jDKSatlQtScKdceCAjDII7HcQ4EMTCpyrCuUBjCYRgHVtqlAiB1YhiCnlsRkAAAOwAAAAAAAAAAAA==') right center no-repeat;
        }

        .field-error {
            border-color: #dc3545 !important;
            background-color: rgba(220, 53, 69, 0.1) !important;
        }

        .field-error:focus {
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }

        /* Estilos para el nuevo autocompletado */
        .autocomplete-wrapper {
            position: relative;
            width: 100%;
        }

        .autocomplete-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(20, 20, 20, 0.95);
            border: 1px solid var(--neon-color);
            border-top: none;
            border-radius: 0 0 4px 4px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .autocomplete-item {
            padding: 10px;
            cursor: pointer;
            color: #fff;
            border-bottom: 1px solid rgba(200, 255, 0, 0.1);
        }

        .autocomplete-item:hover,
        .autocomplete-item.selected {
            background: rgba(200, 255, 0, 0.2);
        }

        .input-error {
            border-color: #dc3545;
            background-color: rgba(220, 53, 69, 0.1);
        }

        /* Estilos personalizados para Select2 */
        .select2-container--bootstrap-5 .select2-selection {
            background-color: rgba(30, 30, 30, 0.7);
            border: 1px solid rgba(200, 255, 0, 0.2);
            color: #fff;
        }

        .select2-container--bootstrap-5 .select2-selection--single {
            height: calc(3.5rem + 2px);
            padding: 1rem 0.75rem;
            font-size: 1rem;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            color: #fff;
            padding: 0;
        }

        .select2-container--bootstrap-5 .select2-dropdown {
            background-color: rgba(20, 20, 20, 0.95);
            border: 1px solid var(--neon-color);
        }

        .select2-container--bootstrap-5 .select2-results__option {
            color: #fff;
            padding: 0.5rem;
        }

        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: rgba(200, 255, 0, 0.2);
            color: #fff;
        }

        .select2-container--bootstrap-5 .select2-search__field {
            background-color: rgba(30, 30, 30, 0.7);
            border: 1px solid rgba(200, 255, 0, 0.2);
            color: #fff;
        }

        .select2-container--bootstrap-5.select2-container--focus .select2-selection {
            border-color: var(--neon-color);
            box-shadow: 0 0 10px rgba(200, 255, 0, 0.3);
        }

        /* Asegurar que los selects tengan el fondo oscuro incluso en diferentes navegadores */
        select:-internal-list-box {
            background-color: rgba(30, 30, 30, 0.95) !important;
            color: #fff !important;
        }

        select:-internal-list-box option {
            background-color: rgba(30, 30, 30, 0.95) !important;
            color: #fff !important;
        }

        /* Estilos para las opciones del select y autocompletado */
        .select2-results__option,
        .select2-search__field,
        select option,
        datalist option {
            background-color: #1a1a1a !important;
            color: #ffffff !important;
        }

        /* Estilos para las opciones al hacer hover */
        .select2-results__option--highlighted,
        .select2-results__option[aria-selected=true],
        .select2-results__option:hover {
            background-color: #2a2a2a !important;
            color: var(--neon-color) !important;
        }

        /* Estilos para el campo de búsqueda en selects */
        .select2-search--dropdown .select2-search__field {
            background-color: #1a1a1a !important;
            color: #ffffff !important;
            border: 1px solid rgba(200, 255, 0, 0.2) !important;
        }

        /* Estilos para el dropdown */
        .select2-dropdown {
            background-color: #1a1a1a !important;
            border: 1px solid rgba(200, 255, 0, 0.2) !important;
        }

        /* Estilos para el contenedor del select */
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            background-color: #1a1a1a !important;
            border: 1px solid rgba(200, 255, 0, 0.2) !important;
        }

        /* Estilos para el texto renderizado */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            background-color: #1a1a1a !important;
            color: #ffffff !important;
        }

        /* Estilos para el placeholder */
        .select2-container--default .select2-selection--single .select2-selection__placeholder,
        input::placeholder,
        textarea::placeholder,
        select::placeholder {
            color: #666666 !important;
        }

        /* Estilos para los inputs nativos */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #1a1a1a inset !important;
            -webkit-text-fill-color: #ffffff !important;
        }

        /* Estilos para el calendario y reloj */
        input[type="date"],
        input[type="time"] {
            background-color: rgba(30, 30, 30, 0.7) !important;
            border: 1px solid rgba(200, 255, 0, 0.2) !important;
            color: #fff !important;
            height: calc(3.5rem + 2px);
            padding: 1rem 0.75rem;
            font-size: 1rem;
            border-radius: 0.375rem;
        }

        input[type="date"]::-webkit-calendar-picker-indicator,
        input[type="time"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            opacity: 0.7;
        }

        input[type="date"]:focus,
        input[type="time"]:focus {
            border-color: var(--neon-color) !important;
            box-shadow: 0 0 10px rgba(200, 255, 0, 0.3) !important;
        }

        .text-neon {
            color: rgb(200, 255, 0);
        }

        .form-label {
            color: #fff;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .card.bg-dark {
            background-color: rgba(30, 30, 30, 0.7) !important;
            border: 1px solid rgba(200, 255, 0, 0.2) !important;
        }

        ::placeholder {
            color: rgba(255, 255, 255, 0.5) !important;
        }

        /* Estilo para el botón de cerrar principal */
        .btn-close {
            position: fixed !important;
            top: 20px !important;
            right: 20px !important;
            width: 30px !important;
            height: 30px !important;
            padding: 0 !important;
            background: none !important;
            border: none !important;
            cursor: pointer !important;
            z-index: 9999 !important;
            opacity: 1 !important;
            font-size: 24px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: rgb(200, 255, 0) !important;
            text-shadow: 0 0 10px rgba(200, 255, 0, 0.5) !important;
        }

        .btn-close::before {
            content: "×" !important;
            font-size: 40px !important;
            line-height: 1 !important;
        }

        .btn-close:hover {
            transform: scale(1.1) !important;
            color: rgb(220, 255, 50) !important;
            text-shadow: 0 0 15px rgba(200, 255, 0, 0.7) !important;
        }

        /* Ocultar TODOS los demás botones de cierre */
        .modal-header .btn-close,
        .offcanvas-header .btn-close,
        .modal-header .close,
        .offcanvas-header .close,
        .close {
            display: none !important;
        }

        /* Mantener visible solo el botón principal */
        .container-fluid > .btn-close {
            display: flex !important;
        }

        /* Asegurar que el contenedor padre tenga posición relativa */
        .container-fluid {
            position: relative !important;
        }

        /* Estilos para el footer */
        .footer {
            background-color: #1a1a1a !important;
            border-top: 1px solid rgba(200, 255, 0, 0.2);
            padding: 1rem 0;
            position: relative;
            z-index: 1;
        }

        .footer .text-muted {
            color: rgba(255, 255, 255, 0.7) !important;
        }

        .footer i {
            color: var(--neon-color);
            margin-right: 0.5rem;
        }

        .footer:hover .text-muted {
            color: rgba(255, 255, 255, 0.9) !important;
            transition: color 0.3s ease;
        }
    </style>
</head>

<body>
    <!-- Hamburger Menu Button -->
    <button class="menu-btn d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sideMenu" aria-controls="sideMenu">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Offcanvas Sidebar -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="sideMenu" aria-labelledby="sideMenuLabel">
        <div class="offcanvas-header">
            <div class="logo-container">
                <img src="../public/img/logo.png" alt="Logo Institucional" class="logo">
            </div>
            <button type="button" class="btn-close" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="user-info">
                <h4><?php echo htmlspecialchars($_SESSION['username']); ?></h4>
                <p class="department-text"><?php echo htmlspecialchars($_SESSION['departamento']); ?></p>
            </div>

            <nav class="nav-menu">
                <div class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </div>
                <div class="nav-item">
                    <a href="../views/dashboard.php" class="nav-link">
                        <i class="fas fa-file-alt"></i> Formularios
                    </a>
                </div>
                
            </nav>

            <a href="../controllers/logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
            </a>
        </div>
    </div>

    <!-- Botón de cerrar (siempre visible) -->
    <!-- <a href="../views/dashboard.php" class="nav-button close-button" title="Volver al Dashboard">
        <i class="fas fa-times"></i>
    </a> -->

    <main class="main-content" role="main">
        <div class="container-fluid py-4">
            <button type="button" class="btn-close" onclick="window.location.href='../views/dashboard.php'" aria-label="Close"></button>
            <h1 class="text-center mb-4">
                <i class="fas fa-clipboard-list text-neon"></i> Formulario de Operatividad
        </h1>

        <div class="form-container animate-slide-up">
                <form id="formOperativoB" action="../controllers/guardar_formulario_b.php" method="POST" class="pb-5">
                <!-- Fecha y Hora -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-calendar-alt"></i>
                        Fecha y Hora
                    </h3>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-calendar"></i>
                                Fecha
                            </label>
                            <input type="date" class="form-control" name="fecha" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-clock"></i>
                                Hora
                            </label>
                            <input type="time" class="form-control" name="hora" required>
                        </div>
                    </div>
                </div>

                <!-- Lugar -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-map-marker-alt"></i>
                        Lugar
                    </h3>
                    <div class="row">
                            <div class="col-12">
                                <div class="autocomplete-wrapper">
                                    <label class="form-label" for="lugar">
                                        <i class="fas fa-map-marker"></i>
                                        Ubicación
                            </label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="lugar_texto" 
                                           id="lugar" 
                                           placeholder="Ingrese el lugar" 
                                           autocomplete="off"
                                           required>
                                    <input type="hidden" name="lugar" id="lugar_id">
                                    <div class="suggestions-container" id="lugar_suggestions"></div>
                                </div>
                        </div>
                    </div>
                </div>

                <!-- Datos de Personas Identificadas -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-user-check"></i>
                        Datos de Personas Identificadas
                    </h3>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-user"></i>
                                Apellido y Nombre
                            </label>
                            <input type="text" class="form-control" name="nombre_identificado" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-id-card"></i>
                                D.N.I. N°
                            </label>
                            <input type="text" class="form-control" name="dni_identificado" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">
                                <i class="fas fa-home"></i>
                                Domicilio
                            </label>
                            <input type="text" class="form-control" name="domicilio_identificado" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Sexo</label>
                            <div class="radio-group">
                                <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sexo_identificado" id="masculino" value="M" required>
                                    <label class="form-check-label" for="masculino">Masculino</label>
                                </div>
                                <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sexo_identificado" id="femenino" value="F">
                                    <label class="form-check-label" for="femenino">Femenino</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menores -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-child"></i> ¿Hay menores?
                    </h3>
                    <div class="radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="hay_menores" id="hay_menores_si" value="si">
                            <label class="form-check-label" for="hay_menores_si">Sí</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="hay_menores" id="hay_menores_no" value="no">
                            <label class="form-check-label" for="hay_menores_no">No</label>
                        </div>
                    </div>
                </div>

                <!-- Si hay menores, mostrar este bloque -->
                <div id="bloque_menores" class="conditional-section" style="display: none;">
                    <div class="form-section">
                        <h5 class="section-title">
                            <i class="fas fa-question-circle"></i> ¿Tiene datos de los menores?
                        </h5>
                        <div class="radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tiene_datos_menor" id="tiene_datos_menor_si" value="si">
                                <label class="form-check-label" for="tiene_datos_menor_si">Sí</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tiene_datos_menor" id="tiene_datos_menor_no" value="no">
                                <label class="form-check-label" for="tiene_datos_menor_no">No</label>
                            </div>
                        </div>
                    </div>

                    <!-- Si tiene datos, mostrar estos campos -->
                    <div id="datos_menores" class="conditional-section" style="display: none;">
                        <div id="contenedorMenores">
                            <!-- Template para un nuevo menor -->
                            <template id="templateMenor">
                                <div class="menor-item mb-4 animate-fade-in">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="text-light mb-0">
                                            <i class="fas fa-child me-2"></i>
                                            <span class="numero-menor">Menor #1</span>
                                        </h4>
                                        <button type="button" class="btn btn-danger btn-sm quitar-menor">
                                            <i class="fas fa-trash-alt"></i>
                                            Quitar
                                        </button>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-user"></i>
                                                Nombre y Apellido
                                            </label>
                                            <input type="text" class="form-control" name="nombre_menor[]" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-id-card"></i>
                                                DNI
                                            </label>
                                            <input type="text" class="form-control" name="dni_menor[]" required>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-home"></i>
                                                Domicilio
                                            </label>
                                            <input type="text" class="form-control" name="domicilio_menor[]" required>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-comment-dots"></i>
                                                Observaciones (opcional)
                                            </label>
                                            <textarea class="form-control" name="observaciones_menor[]" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <hr class="border-secondary">
                                </div>
                            </template>
                        </div>

                        <button type="button" class="btn btn-add mt-3" id="agregarMenor">
                            <i class="fas fa-plus me-2"></i>
                            Agregar otro menor
                        </button>
                    </div>
                </div>

                <!-- Acompañantes -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-users"></i>
                        Acompañantes
                    </h3>
                    <div class="radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tiene_acompanantes" id="si_acompanantes" value="si">
                            <label class="form-check-label" for="si_acompanantes">Sí</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tiene_acompanantes" id="no_acompanantes" value="no">
                            <label class="form-check-label" for="no_acompanantes">No</label>
                        </div>
                    </div>

                    <div id="seccionAcompanantes" class="conditional-section" style="display: none;">
                        <div id="contenedorAcompanantes">
                            <!-- Acompañante #1 (se muestra automáticamente) -->
                            <div class="acompanante-item mb-4 animate-fade-in">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="text-light mb-0">
                                        <i class="fas fa-user me-2"></i>
                                        Acompañante #1
                                    </h4>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-user"></i>
                                            Apellido y Nombre
                                        </label>
                                        <input type="text" class="form-control" name="nombre_apellido_acompanante[]" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-id-card"></i>
                                            D.N.I.
                                        </label>
                                        <input type="text" class="form-control" name="dni_acompanante[]" required>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-home"></i>
                                            Domicilio
                                        </label>
                                        <input type="text" class="form-control" name="domicilio_acompanante[]" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Sexo</label>
                                        <div class="radio-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="sexo_acompanante_1" value="M" required>
                                                <label class="form-check-label">Masculino</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="sexo_acompanante_1" value="F">
                                                <label class="form-check-label">Femenino</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="border-secondary">
                            </div>

                            <!-- Template para acompañantes adicionales -->
                            <template id="templateAcompanante">
                                <div class="acompanante-item mb-4 animate-fade-in">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="text-light mb-0">
                                            <i class="fas fa-user me-2"></i>
                                            <span class="numero-acompanante">Acompañante #2</span>
                                        </h4>
                                        <button type="button" class="btn btn-danger btn-sm quitar-acompanante">
                                            <i class="fas fa-trash-alt"></i>
                                            Quitar
                                        </button>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-user"></i>
                                                Apellido y Nombre
                                            </label>
                                            <input type="text" class="form-control" name="nombre_apellido_acompanante[]" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-check-label">
                                                <i class="fas fa-id-card"></i>
                                                D.N.I.
                                            </label>
                                            <input type="text" class="form-control" name="dni_acompanante[]" required>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-home"></i>
                                                Domicilio
                                            </label>
                                            <input type="text" class="form-control" name="domicilio_acompanante[]" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Sexo</label>
                                            <div class="radio-group">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="sexo_acompanante_IDX" value="M" required>
                                                    <label class="form-check-label">Masculino</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="sexo_acompanante_IDX" value="F">
                                                    <label class="form-check-label">Femenino</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="border-secondary">
                                </div>
                            </template>

                            <!-- Aquí se agregarán dinámicamente los acompañantes adicionales -->
                        </div>

                        <button type="button" class="btn btn-add mt-3" id="agregarAcompanante">
                            <i class="fas fa-plus me-2"></i>
                            Agregar otro acompañante
                        </button>
                    </div>
                </div>

                <!-- Datos del Vehículo -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-car"></i>
                        Datos del Vehículo
                    </h3>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                                <div class="autocomplete-wrapper">
                                    <label class="form-label" for="tipo_vehiculo">
                                        <i class="fas fa-car-side"></i>
                                Tipo de Vehículo
                            </label>
                            <select class="form-select" id="tipo_vehiculo" name="tipo_vehiculo" required>
                                <option value="">Seleccione el tipo de vehículo</option>
                                <option value="AUTOMÓVIL">AUTOMÓVIL</option>
                                <option value="PICKUP">PICKUP</option>
                                <option value="CAMIÓN">CAMIÓN</option>
                                <option value="TRACTOR CAMIÓN">TRACTOR CAMIÓN</option>
                                <option value="SEMIRREMOLQUE">SEMIRREMOLQUE</option>
                                <option value="REMOLQUE">REMOLQUE</option>
                                <option value="UTILITARIO">UTILITARIO</option>
                                <option value="MOTOCICLETA">MOTOCICLETA</option>
                                <option value="CUATRICICLO">CUATRICICLO</option>
                                <option value="TRICICLO">TRICICLO</option>
                                <option value="ÓMNIBUS">ÓMNIBUS</option>
                                <option value="COLECTIVO">COLECTIVO</option>
                                <option value="MAQUINARIA AGRÍCOLA">MAQUINARIA AGRÍCOLA</option>
                                <option value="MAQUINARIA VIAL">MAQUINARIA VIAL</option>
                            </select>
                        </div>
                            </div>

                        <div class="col-md-6 mb-3">
                                <div class="autocomplete-wrapper">
                                    <label class="form-label" for="marca_vehiculo">
                                        <i class="fas fa-trademark"></i>
                                Marca
                            </label>
                            <select class="form-select" id="marca_vehiculo" name="marca_vehiculo" required>
                                <option value="">Seleccione la marca</option>
                                <option value="CITROEN">CITROEN</option>
                                <option value="CORVEN">CORVEN</option>
                                <option value="CHERY">CHERY</option>
                                <option value="CHEVROLET">CHEVROLET</option>
                                <option value="DODGE">DODGE</option>
                                <option value="FIAT">FIAT</option>
                                <option value="FORD">FORD</option>
                                <option value="GILERA">GILERA</option>
                                <option value="HONDA">HONDA</option>
                                <option value="HYUNDAI">HYUNDAI</option>
                                <option value="IVECO">IVECO</option>
                                <option value="JEEP">JEEP</option>
                                <option value="KAWASAKI">KAWASAKI</option>
                                <option value="MERCEDES BENZ">MERCEDES BENZ</option>
                                <option value="MITSUBISHI">MITSUBISHI</option>
                                <option value="MOTOMEL">MOTOMEL</option>
                                <option value="VOLKSWAGEN">VOLKSWAGEN</option>
                                <option value="NISSAN">NISSAN</option>
                                <option value="PEUGEOT">PEUGEOT</option>
                                <option value="RENAULT">RENAULT</option>
                                <option value="TOYOTA">TOYOTA</option>
                                <option value="SCANIA">SCANIA</option>
                                <option value="SUZUKI">SUZUKI</option>
                                <option value="VOLVO">VOLVO</option>
                                <option value="YAMAHA">YAMAHA</option>
                                <option value="ZANELLA">ZANELLA</option>
                                <option value="OTRA">OTRA</option>
                            </select>
                            <div id="marca_otra_container" style="display: none; margin-top: 10px;">
                                <input type="text" class="form-control" id="marca_otra" name="marca_otra" placeholder="Especifique la marca">
                            </div>
                                    <div class="suggestions-container" id="marca_vehiculo_suggestions"></div>
                        </div>
                            </div>

                        <div class="col-md-6 mb-3">
                                <div class="autocomplete-wrapper">
                                    <label class="form-label" for="modelo_vehiculo">
                                <i class="fas fa-car"></i>
                                        Modelo del Vehículo
                            </label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="modelo_vehiculo_texto" 
                                           id="modelo_vehiculo" 
                                           placeholder="Escriba o seleccione el modelo" 
                                           autocomplete="off"
                                           required>
                                    <div class="suggestions-container" id="modelo_vehiculo_suggestions"></div>
                        </div>
                            </div>

                        <div class="col-md-6 mb-3">
                                <label class="form-label" for="dominio">
                                <i class="fas fa-hashtag"></i>
                                Dominio
                            </label>
                                <input type="text" class="form-control" id="dominio" name="dominio" required>
                            </div>
                        </div>
                    </div>

                    <!-- Procedencia -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-map-signs"></i>
                            Procedencia
                        </h3>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="desde">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Desde
                                </label>
                                <select class="form-select" id="desde" name="desde" required>
                                    <option value="">Seleccione la provincia</option>
                                    <option value="Buenos Aires">Buenos Aires</option>
                                    <option value="Catamarca">Catamarca</option>
                                    <option value="Chaco">Chaco</option>
                                    <option value="Chubut">Chubut</option>
                                    <option value="Córdoba">Córdoba</option>
                                    <option value="Corrientes">Corrientes</option>
                                    <option value="Entre Ríos">Entre Ríos</option>
                                    <option value="Formosa">Formosa</option>
                                    <option value="Jujuy">Jujuy</option>
                                    <option value="La Pampa">La Pampa</option>
                                    <option value="La Rioja">La Rioja</option>
                                    <option value="Mendoza">Mendoza</option>
                                    <option value="Misiones">Misiones</option>
                                    <option value="Neuquén">Neuquén</option>
                                    <option value="Río Negro">Río Negro</option>
                                    <option value="Salta">Salta</option>
                                    <option value="San Juan">San Juan</option>
                                    <option value="San Luis">San Luis</option>
                                    <option value="Santa Cruz">Santa Cruz</option>
                                    <option value="Santa Fe">Santa Fe</option>
                                    <option value="Santiago del Estero">Santiago del Estero</option>
                                    <option value="Tierra del Fuego">Tierra del Fuego</option>
                                    <option value="Tucumán">Tucumán</option>
                                    <option value="Otros">Otros</option>
                                </select>
                                <div id="desde_otros_container" style="display: none; margin-top: 10px;">
                                    <input type="text" class="form-control" id="desde_otros" name="desde_otros" placeholder="Especifique el lugar">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="hasta">
                                    <i class="fas fa-map-marker"></i>
                                    Hasta
                                </label>
                                <select class="form-select" id="hasta" name="hasta" required>
                                    <option value="">Seleccione la provincia</option>
                                    <option value="Buenos Aires">Buenos Aires</option>
                                    <option value="Catamarca">Catamarca</option>
                                    <option value="Chaco">Chaco</option>
                                    <option value="Chubut">Chubut</option>
                                    <option value="Córdoba">Córdoba</option>
                                    <option value="Corrientes">Corrientes</option>
                                    <option value="Entre Ríos">Entre Ríos</option>
                                    <option value="Formosa">Formosa</option>
                                    <option value="Jujuy">Jujuy</option>
                                    <option value="La Pampa">La Pampa</option>
                                    <option value="La Rioja">La Rioja</option>
                                    <option value="Mendoza">Mendoza</option>
                                    <option value="Misiones">Misiones</option>
                                    <option value="Neuquén">Neuquén</option>
                                    <option value="Río Negro">Río Negro</option>
                                    <option value="Salta">Salta</option>
                                    <option value="San Juan">San Juan</option>
                                    <option value="San Luis">San Luis</option>
                                    <option value="Santa Cruz">Santa Cruz</option>
                                    <option value="Santa Fe">Santa Fe</option>
                                    <option value="Santiago del Estero">Santiago del Estero</option>
                                    <option value="Tierra del Fuego">Tierra del Fuego</option>
                                    <option value="Tucumán">Tucumán</option>
                                    <option value="Otros">Otros</option>
                                </select>
                                <div id="hasta_otros_container" style="display: none; margin-top: 10px;">
                                    <input type="text" class="form-control" id="hasta_otros" name="hasta_otros" placeholder="Especifique el lugar">
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Infracción y Lugar de Retención -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        Infracción y Lugar de Retención
                    </h3>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-exclamation-circle"></i>
                            ¿Hubo infracción?
                        </label>
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="hubo_infraccion" id="infraccion_si" value="si" required>
                                    <label class="form-check-label" for="infraccion_si">Sí</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="hubo_infraccion" id="infraccion_no" value="no" required>
                                    <label class="form-check-label" for="infraccion_no">No</label>
                                </div>
                            </div>
                    </div>

                    <div id="seccionInfraccion" class="conditional-section" style="display: none;">
                        <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-exclamation-circle"></i>
                                        Motivo de Infracción
                                    </label>
                                    <div class="autocomplete-wrapper">
                                        <input type="text" 
                                               class="form-control" 
                                               name="motivo_infraccion_texto" 
                                               id="motivo_infraccion" 
                                               placeholder="Ingrese el motivo de la infracción">
                                        <input type="hidden" name="motivo_infraccion" id="motivo_infraccion_id">
                                        <div class="suggestions-container" id="motivo_infraccion_suggestions"></div>
                                    </div>
                                </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-file-alt"></i>
                                    N° Acta de Infracción
                                </label>
                                <input type="text" class="form-control" name="numero_acta">
                            </div>
                            <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-warehouse"></i>
                                        ¿Hubo retención?
                                    </label>
                                    <div class="radio-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="hubo_retencion" id="retencion_si" value="si">
                                            <label class="form-check-label" for="retencion_si">Sí</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="hubo_retencion" id="retencion_no" value="no">
                                            <label class="form-check-label" for="retencion_no">No</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3" id="seccion_lugar_retencion" style="display: none;">
                                <label class="form-label">
                                    <i class="fas fa-warehouse"></i>
                                    Lugar de Retención
                                </label>
                                    <div class="autocomplete-container">
                                        <input type="text" 
                                               class="form-control" 
                                               name="lugar_retencion_texto" 
                                               id="lugar_retencion" 
                                               placeholder="Ingrese el lugar de retención">
                                        <input type="hidden" name="lugar_retencion" id="lugar_retencion_id">
                                        <div class="autocomplete-items" id="lugar_retencion_suggestions"></div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alcoholemia -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-wine-bottle"></i>
                        Alcoholemia
                    </h3>
                    <div class="col-md-6">
                        <label class="form-label">Alcoholemia</label>
                        <div class="radio-group flex-column">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="alcoholemia" id="alcoholemia_si" value="1" required>
                                <label class="form-check-label" for="alcoholemia_si">
                                    Sí, se realizó prueba de alcoholemia
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="alcoholemia" id="alcoholemia_na" value="2">
                                <label class="form-check-label" for="alcoholemia_na">
                                    No aplica
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="seccionAlcoholemia" class="conditional-section mt-3" style="display: none;">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                <i class="fas fa-tools"></i>
                                Equipo
                            </label>
                                <div class="autocomplete-wrapper">
                                    <input type="text" 
                                           class="form-control" 
                                           name="equipo_texto" 
                                           id="equipo" 
                                           placeholder="Ingrese el número de equipo">
                                    <input type="hidden" name="equipo" id="equipo_id">
                                    <div class="suggestions-container" id="equipo_suggestions"></div>
                                </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                <i class="fas fa-vial"></i>
                                Número de Prueba
                            </label>
                                <input type="text" class="form-control" id="prueba_id" name="prueba_id" placeholder="Ingrese número de prueba">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                <i class="fas fa-percentage"></i>
                                Resultado
                            </label>
                                <input type="text" class="form-control" id="resultado_alcoholemia" name="resultado_alcoholemia">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-submit">
                    <i class="fas fa-paper-plane me-2"></i>
                    Enviar Formulario
                </button>
            </form>
        </div>
    </div>
    </main>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container text-center py-3">
            <span class="footer-text">
                <i class="fas fa-code"></i> Desarrollado por FoxDigital © 2025
            </span>
        </div>
    </footer>

    <style>
        .footer {
            background-color: #1a1a1a;
            color: #c8ff00;
            position: relative;
            width: 100%;
            margin-top: auto;
        }
        
        .footer-text {
            font-size: 1rem;
            transition: color 0.3s ease;
        }
        
        .footer-text:hover {
            color: #fff;
        }
        
        .footer i {
            margin-right: 5px;
        }
    </style>

    <!-- Modales -->
    <div class="modal fade" id="modalLugar" tabindex="-1" aria-labelledby="modalLugarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLugarLabel">Agregar Lugar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Contenido del modal de lugar -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalModeloVehiculo" tabindex="-1" aria-labelledby="modalModeloVehiculoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalModeloVehiculoLabel">Agregar Modelo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Contenido del modal de modelo -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAgregarLugarRetencion" tabindex="-1" aria-labelledby="modalAgregarLugarRetencionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAgregarLugarRetencionLabel">Agregar Lugar de Retención</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Contenido del modal de lugar de retención -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalMotivoInfraccion" tabindex="-1" aria-labelledby="modalMotivoInfraccionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMotivoInfraccionLabel">Agregar Motivo de Infracción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Contenido del modal de motivo de infracción -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAgregarEquipo" tabindex="-1" aria-labelledby="modalAgregarEquipoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAgregarEquipoLabel">Agregar Equipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Contenido del modal de equipo -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Configuración global de todos los campos con autocompletado
        const fields = [
            { 
                id: 'desde',
                url: '../controllers/get_lugares.php',
                placeholder: 'Ingrese el lugar'
            },
            { 
                id: 'marca_vehiculo',
                url: '../controllers/get_marcas.php',
                saveUrl: '../controllers/guardar_marca.php',
                fieldName: 'nombre_marca',
                modalId: 'modalMarcaVehiculo',
                placeholder: 'Escriba o seleccione la marca'
            },
            { 
                id: 'modelo_vehiculo',
                url: '../controllers/get_modelos.php',
                saveUrl: '../controllers/guardar_modelo.php',
                fieldName: 'modelo_nombre',
                modalId: 'modalModeloVehiculo',
                placeholder: 'Escriba o seleccione el modelo',
                dependsOn: 'marca_vehiculo'
            },
            {
                id: 'lugar_retencion',
                url: '../controllers/get_lugares.php', // Cambiado para usar el mismo controlador de lugares
                saveUrl: '../controllers/guardar_lugar.php', // Cambiado para usar el mismo controlador de lugares
                fieldName: 'nombre_lugar',
                modalId: 'modalAgregarLugarRetencion',
                placeholder: 'Ingrese el lugar de retención'
            },
            {
                id: 'motivo_infraccion',
                url: '../controllers/get_motivos_infraccion.php',
                saveUrl: '../controllers/guardar_motivo_infraccion.php',
                fieldName: 'nombre_motivo',
                modalId: 'modalMotivoInfraccion',
                placeholder: 'Ingrese el motivo de la infracción'
            },
            {
                id: 'equipo',
                url: '../controllers/get_equipos.php',
                saveUrl: '../controllers/guardar_equipo.php',
                fieldName: 'numero_equipo',
                modalId: 'modalAgregarEquipo',
                placeholder: 'Ingrese el número de equipo'
            }
        ];

        // Función para manejar errores de fetch
        async function handleFetchResponse(response) {
            if (!response.ok) {
                const text = await response.text();
                console.error('Response text:', text);
                throw new Error(`Error HTTP: ${response.status}. URL: ${response.url}`);
            }
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                const text = await response.text();
                console.error('Invalid response:', text);
                throw new TypeError("La respuesta no es JSON válido");
            }
            return await response.json();
        }

        // Función para mostrar errores
        function showError(error) {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: error.message || 'Error en la operación',
                icon: 'error',
                background: '#1a1a1a',
                color: '#fff',
                confirmButtonColor: '#c8ff00'
            });
        }

        // Función para inicializar autocompletado
        function initializeAutocomplete(field) {
            const input = document.getElementById(field.id);
            const hiddenInput = document.getElementById(`${field.id}_id`);
            const suggestionsContainer = document.getElementById(`${field.id}_suggestions`);
            
            if (!input || !hiddenInput || !suggestionsContainer) {
                console.error(`No se encontraron los elementos necesarios para ${field.id}`);
                return;
            }

            let currentSuggestions = [];

            // Función para crear nuevo item
            async function createNewItem(value) {
                try {
                    const formData = new FormData();

                    if (field.id === 'modelo_vehiculo') {
                        const marcaId = document.getElementById('marca_vehiculo_id').value;
                        if (!marcaId) {
                            throw new Error('Primero debe seleccionar una marca');
                        }
                        formData.append('marca_id', marcaId);
                    }

                    formData.append(field.fieldName, value);
                    
                    const response = await fetch(field.saveUrl, {
                        method: 'POST',
                        body: formData
                    });

                    const data = await handleFetchResponse(response);
                    if (data.success) {
                        input.value = data.text || value;
                        hiddenInput.value = data.id;
                        suggestionsContainer.style.display = 'none';
                        input.classList.remove('is-invalid');
                        
                        Swal.fire({
                            title: '¡Éxito!',
                            text: data.message || 'Item creado correctamente',
                            icon: 'success',
                            background: '#1a1a1a',
                            color: '#fff',
                            confirmButtonColor: '#c8ff00',
                            timer: 1500
                        });
                    } else {
                        throw new Error(data.message || 'Error al crear el item');
                    }
                } catch (error) {
                    console.error('Error creating item:', error);
                    showError(error);
                }
            }

            // Función para buscar sugerencias
            async function fetchSuggestions(query) {
                try {
                    let url = field.url;
                    if (field.dependsOn) {
                        const dependentId = document.getElementById(`${field.dependsOn}_id`).value;
                        if (!dependentId && field.id === 'modelo_vehiculo') {
                            input.value = '';
                            hiddenInput.value = '';
                            return [];
                        }
                        url += `?parent_id=${dependentId}&query=${encodeURIComponent(query)}`;
                    } else {
                        url += `?query=${encodeURIComponent(query)}`;
                    }

                    console.log(`Buscando sugerencias para ${field.id}:`, url);
                    const response = await fetch(url);
                    const data = await handleFetchResponse(response);
                    console.log(`Respuesta para ${field.id}:`, data);

                    if (data.success) {
                        return data.results || [];
                    }
                    return [];
                } catch (error) {
                    console.error(`Error fetching suggestions for ${field.id}:`, error);
                    showError(error);
                    return [];
                }
            }

            // Función para mostrar sugerencias
            function showSuggestions(suggestions) {
                currentSuggestions = suggestions;
                suggestionsContainer.innerHTML = '';
                
                if (suggestions.length === 0 && input.value.trim()) {
                    const div = document.createElement('div');
                    div.className = 'suggestion-item suggestion-create';
                    div.innerHTML = `<i class="fas fa-plus"></i> Crear nuevo: "${input.value.trim()}"`;
                    div.addEventListener('click', () => createNewItem(input.value.trim()));
                    suggestionsContainer.appendChild(div);
                }

                suggestions.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'suggestion-item';
                    div.textContent = item.text;
                    div.addEventListener('click', () => {
                        input.value = item.text;
                        hiddenInput.value = item.id;
                        suggestionsContainer.style.display = 'none';
                        input.classList.remove('is-invalid');
                        
                        // Debug: Mostrar los valores seleccionados
                        console.log(`${field.id} seleccionado:`, {
                            texto: item.text,
                            id: item.id
                        });
                        
                        // Si es un tipo de vehículo, limpiar los campos dependientes
                        if (field.id === 'tipo_vehiculo') {
                            const marcaInput = document.getElementById('marca_vehiculo');
                            const marcaIdInput = document.getElementById('marca_vehiculo_id');
                            const modeloInput = document.getElementById('modelo_vehiculo');
                            const modeloIdInput = document.getElementById('modelo_vehiculo_id');
                            
                            if (marcaInput) marcaInput.value = '';
                            if (marcaIdInput) marcaIdInput.value = '';
                            if (modeloInput) modeloInput.value = '';
                            if (modeloIdInput) modeloIdInput.value = '';
                        }
                    });
                    suggestionsContainer.appendChild(div);
                });

                suggestionsContainer.style.display = 'block';
            }

            // Event Listeners
            let debounceTimer;
            input.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(async () => {
                    const query = input.value.trim();
                    if (query.length >= 1) {
                        const suggestions = await fetchSuggestions(query);
                        showSuggestions(suggestions);
                    } else {
                        suggestionsContainer.style.display = 'none';
                    }
                }, 300);
            });

            input.addEventListener('focus', async () => {
                const suggestions = await fetchSuggestions(input.value.trim());
                showSuggestions(suggestions);
            });

            // Cerrar sugerencias al hacer clic fuera
            document.addEventListener('click', (e) => {
                if (!input.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                    suggestionsContainer.style.display = 'none';
                }
            });

            // Si el campo depende de otro, limpiar cuando cambie el padre
            if (field.dependsOn) {
                const parentInput = document.getElementById(field.dependsOn);
                if (parentInput) {
                    parentInput.addEventListener('change', () => {
                        input.value = '';
                        hiddenInput.value = '';
                    });
                }
            }

            // Establecer placeholder
            if (field.placeholder) {
                input.placeholder = field.placeholder;
            }

            // Mostrar todas las sugerencias al hacer clic en el campo
            input.addEventListener('click', async () => {
                const suggestions = await fetchSuggestions('');
                showSuggestions(suggestions);
            });
        }

        // Función para manejar la visibilidad de secciones condicionales
        function toggleConditionalSection(radioName, sectionId, showOnValue = 'si') {
            const radios = document.querySelectorAll(`input[name="${radioName}"]`);
            const section = document.getElementById(sectionId);
            
            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (section) {
                        section.style.display = this.value === showOnValue ? 'block' : 'none';
                        
                        // Si la sección se oculta, limpiar sus campos
                        if (this.value !== showOnValue) {
                            const inputs = section.querySelectorAll('input, select, textarea');
                            inputs.forEach(input => {
                                if (input.type === 'radio' || input.type === 'checkbox') {
                                    input.checked = false;
                                } else {
                                    input.value = '';
                                }
                            });
                        }
                    }
                });
            });
        }

        // Inicializar cuando el documento esté listo
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar secciones condicionales
            toggleConditionalSection('hay_menores', 'bloque_menores');
            toggleConditionalSection('tiene_datos_menor', 'datos_menores');
            toggleConditionalSection('tiene_acompanantes', 'seccionAcompanantes');
            toggleConditionalSection('hubo_infraccion', 'seccionInfraccion');
            toggleConditionalSection('hubo_retencion', 'seccion_lugar_retencion');
            toggleConditionalSection('alcoholemia', 'seccionAlcoholemia', '1');

            // Contador para menores
            let contadorMenores = 0;
            const btnAgregarMenor = document.getElementById('agregarMenor');
            const contenedorMenores = document.getElementById('contenedorMenores');
            const templateMenor = document.getElementById('templateMenor');

            if (btnAgregarMenor && contenedorMenores && templateMenor) {
                btnAgregarMenor.addEventListener('click', function() {
                    contadorMenores++;
                    const clone = document.importNode(templateMenor.content, true);
                    clone.querySelector('.numero-menor').textContent = `Menor #${contadorMenores}`;
                    
                    // Agregar evento para quitar menor
                    const btnQuitar = clone.querySelector('.quitar-menor');
                    btnQuitar.addEventListener('click', function() {
                        this.closest('.menor-item').remove();
                        actualizarNumerosMenores();
                    });

                    contenedorMenores.appendChild(clone);
                });
            }

            // Contador para acompañantes
            let contadorAcompanantes = 1; // Comienza en 1 porque ya existe el primer acompañante
            const btnAgregarAcompanante = document.getElementById('agregarAcompanante');
            const contenedorAcompanantes = document.getElementById('contenedorAcompanantes');
            const templateAcompanante = document.getElementById('templateAcompanante');

            if (btnAgregarAcompanante && contenedorAcompanantes && templateAcompanante) {
                btnAgregarAcompanante.addEventListener('click', function() {
                    contadorAcompanantes++;
                    const clone = document.importNode(templateAcompanante.content, true);
                    clone.querySelector('.numero-acompanante').textContent = `Acompañante #${contadorAcompanantes}`;
                    
                    // Actualizar los nombres de los radio buttons
                    const radios = clone.querySelectorAll('input[type="radio"]');
                    radios.forEach(radio => {
                        radio.name = `sexo_acompanante_${contadorAcompanantes}`;
                    });

                    // Agregar evento para quitar acompañante
                    const btnQuitar = clone.querySelector('.quitar-acompanante');
                    btnQuitar.addEventListener('click', function() {
                        this.closest('.acompanante-item').remove();
                        actualizarNumerosAcompanantes();
                    });

                    contenedorAcompanantes.appendChild(clone);
                });
            }

            // Función para actualizar números de menores
            function actualizarNumerosMenores() {
                const menores = contenedorMenores.querySelectorAll('.menor-item');
                menores.forEach((menor, index) => {
                    menor.querySelector('.numero-menor').textContent = `Menor #${index + 1}`;
                });
                contadorMenores = menores.length;
            }

            // Función para actualizar números de acompañantes
            function actualizarNumerosAcompanantes() {
                const acompanantes = contenedorAcompanantes.querySelectorAll('.acompanante-item');
                let contador = 1; // El primer acompañante siempre es #1
                
                acompanantes.forEach((acompanante, index) => {
                    const numeroSpan = acompanante.querySelector('.numero-acompanante');
                    if (numeroSpan) { // Solo actualizar los que tienen span (acompañantes adicionales)
                        numeroSpan.textContent = `Acompañante #${contador + 1}`;
                        contador++;
                    }
                });
                contadorAcompanantes = acompanantes.length;
            }

            // Inicializar autocompletado
            fields.forEach(field => {
                console.log(`Setting up field: ${field.id}`);
                if (field.id !== 'tipo_vehiculo') { // Excluir el tipo de vehículo del autocompletado
                    initializeAutocomplete(field);
                }
            });

            // Manejar el cambio de tipo de vehículo
            const tipoVehiculoSelect = document.getElementById('tipo_vehiculo');
            if (tipoVehiculoSelect) {
                tipoVehiculoSelect.addEventListener('change', function() {
                    const marcaInput = document.getElementById('marca_vehiculo');
                    const modeloInput = document.getElementById('modelo_vehiculo');
                    
                    if (marcaInput) marcaInput.value = '';
                    if (modeloInput) modeloInput.value = '';
                });
            }

            // Manejar envío de formularios modales
            document.querySelectorAll('.modal-form').forEach(form => {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    console.log('Form submitted:', this.action);
                    const formData = new FormData(this);
                    
                    try {
                        const response = await fetch(this.action, {
                            method: 'POST',
                            body: formData
                        });
                        
                        const data = await handleFetchResponse(response);
                        console.log('Form response:', data);
                        
                        if (data.success) {
                            // Actualizar campo
                            const selectId = this.dataset.selectId;
                            const input = document.getElementById(selectId);
                            const hiddenInput = document.getElementById(`${selectId}_id`);
                            if (input && hiddenInput) {
                                input.value = data.text;
                                hiddenInput.value = data.id;
                            }
                            
                            // Cerrar modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById(this.dataset.modalId));
                            modal.hide();
                            
                            // Mostrar éxito
                            Swal.fire({
                                title: '¡Éxito!',
                                text: data.message,
                                icon: 'success',
                                background: '#1a1a1a',
                                color: '#fff',
                                confirmButtonColor: '#c8ff00'
                            });
                            
                            this.reset();
                        } else {
                            throw new Error(data.message || 'Error al guardar');
                        }
                    } catch (error) {
                        showError(error);
                    }
                });
            });

            // Manejar campos de "Otros" en procedencia
            document.getElementById('desde').addEventListener('change', function() {
                const otrosContainer = document.getElementById('desde_otros_container');
                const otrosInput = document.getElementById('desde_otros');
                if (this.value === 'Otros') {
                    otrosContainer.style.display = 'block';
                    otrosInput.required = true;
                } else {
                    otrosContainer.style.display = 'none';
                    otrosInput.required = false;
                    otrosInput.value = '';
                }
            });

            document.getElementById('hasta').addEventListener('change', function() {
                const otrosContainer = document.getElementById('hasta_otros_container');
                const otrosInput = document.getElementById('hasta_otros');
                if (this.value === 'Otros') {
                    otrosContainer.style.display = 'block';
                    otrosInput.required = true;
                } else {
                    otrosContainer.style.display = 'none';
                    otrosInput.required = false;
                    otrosInput.value = '';
                }
            });

            // Manejar campo "OTRA" en marca del vehículo
            document.getElementById('marca_vehiculo').addEventListener('change', function() {
                const otraContainer = document.getElementById('marca_otra_container');
                const otraInput = document.getElementById('marca_otra');
                if (this.value === 'OTRA') {
                    otraContainer.style.display = 'block';
                    otraInput.required = true;
                } else {
                    otraContainer.style.display = 'none';
                    otraInput.required = false;
                    otraInput.value = '';
                }
            });
        });

        // Función para limpiar el formulario
        function limpiarFormulario() {
            document.getElementById('formOperativoB').reset();
            
            // Limpiar campos ocultos
            document.querySelectorAll('input[type="hidden"]').forEach(input => {
                input.value = '';
            });

            // Ocultar secciones condicionales
            document.getElementById('bloque_menores').style.display = 'none';
            document.getElementById('datos_menores').style.display = 'none';
            document.getElementById('seccionAcompanantes').style.display = 'none';
            document.getElementById('seccionInfraccion').style.display = 'none';
            document.getElementById('seccion_lugar_retencion').style.display = 'none';
            document.getElementById('seccionAlcoholemia').style.display = 'none';
            document.getElementById('marca_otra_container').style.display = 'none';

            // Limpiar contenedores dinámicos
            document.getElementById('contenedorMenores').innerHTML = '';
            document.getElementById('contenedorAcompanantes').innerHTML = '';
        }

        // Agregar el manejador de envío del formulario
        document.getElementById('formOperativoB').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            try {
                const formData = new FormData(this);
                
                // Debug: Mostrar los valores de los campos
                console.log('Valor de hubo_infraccion:', formData.get('hubo_infraccion'));
                console.log('Valor de hubo_retencion:', formData.get('hubo_retencion'));
                console.log('Valor de tipo_vehiculo:', formData.get('tipo_vehiculo'));
                console.log('Valor de tipo_vehiculo_texto:', formData.get('tipo_vehiculo_texto'));
                
                // Mostrar indicador de carga
                Swal.fire({
                    title: 'Guardando...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData
                });

                const data = await handleFetchResponse(response);
                
                if (data.success) {
                    // Limpiar el formulario
                    limpiarFormulario();
                    
                    // Mostrar mensaje de éxito
                    await Swal.fire({
                        title: '¡Éxito!',
                        text: 'El formulario se ha guardado correctamente',
                        icon: 'success',
                        background: '#1a1a1a',
                        color: '#fff',
                        confirmButtonColor: '#c8ff00'
                    });
                } else {
                    throw new Error(data.message || 'Error al guardar el formulario');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: error.message || 'Error al procesar el formulario',
                    icon: 'error',
                    background: '#1a1a1a',
                    color: '#fff',
                    confirmButtonColor: '#c8ff00'
                });
            }
        });

        // Resto del código JavaScript existente...
    </script>
</body>

</html>