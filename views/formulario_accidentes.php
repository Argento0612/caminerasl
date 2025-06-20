<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Accidentes - Personal a Cargo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --main-bg: #232c3d;
            --container-bg: #2d3a53;
            --primary: #3baaf7;
            --primary-hover: #5bc6ff;
            --text-main: #fff;
            --text-secondary: #bfc9da;
            --border: #3baaf7;
            --shadow: 0 4px 24px 0 rgba(59,170,247,0.10);
        }
        body {
            background: #000;
            color: var(--text-main);
            font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
            min-height: 100vh;
        }
        .form-section {
            background: #232c3d;
            border: 2.5px solid #00f0ff;
            box-shadow: 0 0 24px 4px #00f0ff80, 0 2px 24px 0 #00f0ff40;
            padding: 2.5rem 2rem 2rem 2rem;
            margin-bottom: 2rem;
            border-radius: 18px;
            transition: border-color 0.3s, box-shadow 0.3s;
            animation: neonGlow 2s infinite alternate;
        }
        @keyframes neonGlow {
            from {
                box-shadow: 0 0 24px 4px #00f0ff80, 0 2px 24px 0 #00f0ff40;
            }
            to {
                box-shadow: 0 0 36px 8px #00f0ffcc, 0 4px 32px 0 #00f0ff80;
            }
        }
        .form-section:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(59,170,247,0.15);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .section-title {
            color: var(--primary);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            letter-spacing: 1px;
            position: relative;
        }
        .section-title::after {
            content: '';
            display: block;
            position: absolute;
            left: 0;
            bottom: -8px;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--primary-hover));
            border-radius: 2px;
            animation: underlineGrow 1s cubic-bezier(.39,.575,.56,1.000);
        }
        @keyframes underlineGrow {
            from { width: 0; }
            to { width: 60px; }
        }
        .form-label {
            color: var(--text-secondary);
            font-weight: 600;
            margin-bottom: 0.3rem;
        }
        .form-control {
            background: #22304a !important;
            border: 1.5px solid #34476a !important;
            color: var(--text-main) !important;
            border-radius: 10px !important;
            padding: 0.8rem !important;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-control:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 2px var(--primary-hover) !important;
            background: #263759 !important;
        }
        .btn-submit {
            background: #22304a;
            color: #ffffff;
            padding: 1rem 3rem;
            border-radius: 15px;
            border: 1.5px solid #34476a;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: background 0.3s, transform 0.2s, box-shadow 0.3s;
            margin-top: 2rem;
            width: auto;
            display: block;
            margin-left: auto;
            margin-right: auto;
            box-shadow: 0 2px 8px 0 rgba(59,170,247,0.10);
            position: relative;
            overflow: hidden;
        }
        .btn-submit i {
            transition: transform 0.4s cubic-bezier(.39,.575,.56,1.000);
        }
        .btn-submit:hover {
            background: #34476a;
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 4px 16px 0 rgba(59,170,247,0.18);
        }
        .btn-submit:hover i {
            transform: rotate(-20deg) scale(1.2);
        }
        .mb-4 {
            margin-bottom: 1.5rem !important;
        }
        ::placeholder {
            color: #7a8bbd !important;
            opacity: 1;
        }
        .form-control[type="date"] {
            color: #fff !important;
        }
        .form-control[type="date"]::-webkit-input-placeholder {
            color: #fff !important;
        }
        .form-control[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }
        select.form-control {
            background-color: #22304a !important;
            color: #ffffff !important;
            border: 1.5px solid #34476a !important;
            padding: 0.8rem !important;
            cursor: pointer;
        }
        select.form-control option {
            background-color: #22304a !important;
            color: #ffffff !important;
            padding: 10px;
        }
        select.form-control optgroup {
            background-color: #22304a !important;
            color: #3baaf7 !important;
            font-weight: bold;
            padding: 10px;
        }
        select.form-control option:checked {
            background-color: #34476a !important;
        }
        select.form-control option:hover {
            background-color: #34476a !important;
        }
        select.form-control option[value=""][disabled],
        select.form-control option[value=""][selected],
        select.form-control option[value=""]:checked {
            background-color: #22304a !important;
            color: #ffffff !important;
        }
        input[type="time"] {
            background: #22304a !important;
            color: var(--text-main) !important;
            border: 1.5px solid #34476a !important;
        }
        input[type="time"]:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 2px var(--primary-hover) !important;
            background: #263759 !important;
        }
        input[type="time"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }
        .dropdown-menu {
            background: #22304a !important;
            border: 1.5px solid #34476a !important;
            color: var(--text-main) !important;
        }
        .dropdown-item {
            color: var(--text-main) !important;
            padding: 0.8rem 1rem;
        }
        .dropdown-item:hover {
            background: #34476a !important;
            color: var(--text-main) !important;
        }
        .dropdown-toggle {
            background: #22304a !important;
            color: #3baaf7 !important;
            font-weight: bold;
            text-align: left;
            padding: 0.8rem 1rem;
            border: 1.5px solid #34476a !important;
        }
        .dropdown-toggle::after {
            float: right;
            margin-top: 8px;
        }
        .dropdown-header {
            color: #3baaf7 !important;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .collapse {
            display: none;
        }
        .collapse.show {
            display: block;
        }
        /* Submenú personalizado */
        .submenu {
            display: none;
            position: static;
            background: #22304a !important;
            border-left: 2px solid #3baaf7;
            margin-left: 1.5rem;
            padding-left: 0.5rem;
        }
        .submenu.show {
            display: block;
        }
        .submenu .dropdown-item {
            padding-left: 2rem;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form id="formularioAccidentes" class="form-section">
                    <h2 class="section-title">
                        Planilla de Accidente de Tránsito
                    </h2>

                    <div class="mb-4">
                        <label for="fecha" class="form-label">
                            Fecha
                        </label>
                        <input type="date" class="form-control" id="fecha" name="fecha" required>
                    </div>

                    <div class="mb-4">
                        <label for="hora" class="form-label">
                            Hora
                        </label>
                        <input type="time" class="form-control" id="hora" name="hora" required>
                    </div>

                    <div class="mb-4">
                        <label for="urop" class="form-label">UROP</label>
                        <select class="form-control" id="urop" name="urop" required>
                            <option value="" disabled selected>Seleccione UROP</option>
                            <option value="urop1">UROP 1</option>
                            <option value="urop2">UROP 2</option>
                            <option value="urop3">UROP 3</option>
                            <option value="urop4">UROP 4</option>
                            <option value="urop5">UROP 5</option>
                            <option value="urop6">UROP 6</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="lugar" class="form-label">Comisaría</label>
                        <select class="form-control" id="lugar" name="lugar" required disabled>
                            <option value="" disabled selected>Seleccione Comisaría</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="sumario_interno" class="form-label">
                            Sumario Interno N°
                        </label>
                        <input type="text" class="form-control" id="sumario_interno" name="sumario_interno" placeholder="Ingrese el número de sumario" required>
                    </div>

                    <div class="mb-4">
                        <label for="iph" class="form-label">
                            IPH N°
                        </label>
                        <input type="text" class="form-control" id="iph" name="iph" placeholder="Ingrese el número de IPH" required>
                    </div>

                    <div class="mb-4">
                        <label for="ubicacion" class="form-label">
                            Ubicación del Accidente
                        </label>
                        <input type="text" class="form-control" id="ubicacion" name="ubicacion" placeholder="Ingrese la ubicación exacta" required>
                    </div>

                    <div id="partesContainer"></div>

                    <div class="mb-4">
                        <label for="tipo_accidente" class="form-label">
                            Tipo de Accidente
                        </label>
                        <select class="form-control" id="tipo_accidente" name="tipo_accidente" required>
                            <option value="">Seleccione el tipo de accidente</option>
                            <option value="colision">Colisión</option>
                            <option value="atropello">Atropello</option>
                            <option value="volcamiento">Volcamiento</option>
                            <option value="choque">Choque</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="descripcion" class="form-label">
                            Descripción del Accidente
                        </label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4" placeholder="Describa los detalles del accidente" required></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="vehiculos_involucrados" class="form-label">
                            Vehículos Involucrados
                        </label>
                        <input type="number" class="form-control" id="vehiculos_involucrados" name="vehiculos_involucrados" min="1" placeholder="Número de vehículos involucrados" required>
                    </div>

                    <div class="mb-4">
                        <label for="heridos" class="form-label">
                            Personas Heridas
                        </label>
                        <input type="number" class="form-control" id="heridos" name="heridos" min="0" placeholder="Número de personas heridas" required>
                    </div>

                    <div class="mb-4">
                        <label for="fallecidos" class="form-label">
                            Personas Fallecidas
                        </label>
                        <input type="number" class="form-control" id="fallecidos" name="fallecidos" min="0" placeholder="Número de personas fallecidas" required>
                    </div>

                    <div class="mb-4">
                        <label for="condiciones_climaticas" class="form-label">
                            Condiciones Climáticas
                        </label>
                        <select class="form-control" id="condiciones_climaticas" name="condiciones_climaticas" required>
                            <option value="">Seleccione las condiciones climáticas</option>
                            <option value="soleado">Soleado</option>
                            <option value="lluvioso">Lluvioso</option>
                            <option value="nublado">Nublado</option>
                            <option value="neblina">Neblina</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-submit">
                            Guardar Registro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('formularioAccidentes').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('endpoints/registro_accidentes.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    this.reset();
                    document.getElementById('fecha').valueAsDate = new Date();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al enviar el formulario');
            });
        });

        // Establecer la fecha actual por defecto
        document.getElementById('fecha').valueAsDate = new Date();

        let parteCount = 0;
        document.getElementById('agregarParteBtn').addEventListener('click', function() {
            parteCount++;
            const parteDiv = document.createElement('div');
            parteDiv.className = 'form-section mb-4';
            parteDiv.innerHTML = `
                <h4 class="section-title" style="font-size:1.3rem;">Parte N°${parteCount}</h4>
                <div class="mb-4">
                    <label class="form-label">D.N.I. Nº</label>
                    <input type="text" class="form-control" name="dni[]" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Domicilio</label>
                    <input type="text" class="form-control" name="domicilio[]" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Vehículo</label>
                    <input type="text" class="form-control" name="vehiculo[]" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Tipo</label>
                    <input type="text" class="form-control" name="tipo[]" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Marca</label>
                    <input type="text" class="form-control" name="marca[]" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Modelo</label>
                    <input type="text" class="form-control" name="modelo[]" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Dominio</label>
                    <input type="text" class="form-control" name="dominio[]" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Color</label>
                    <input type="text" class="form-control" name="color[]" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Infracción N°</label>
                    <input type="text" class="form-control" name="infraccion[]" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Motivo</label>
                    <input type="text" class="form-control" name="motivo[]" required>
                </div>
            `;
            document.getElementById('partesContainer').appendChild(parteDiv);
        });

        const comisariasPorUrop = {
            urop1: [
                { value: 'comisaria_seccional_1', text: 'COMISARIA SECCIONAL 1° SAN LUIS' },
                { value: 'comisaria_seccional_2', text: 'COMISARIA SECCIONAL 2° SAN LUIS' },
                { value: 'comisaria_seccional_3', text: 'COMISARIA SECCIONAL 3° SAN LUIS' },
                { value: 'comisaria_seccional_4', text: 'COMISARIA SECCIONAL 4° SAN LUIS' },
                { value: 'comisaria_seccional_5', text: 'COMISARIA SECCIONAL 5° JUANA KOSLAY' },
                { value: 'comisaria_seccional_6', text: 'COMISARIA SECCIONAL 6° SAN LUIS' },
                { value: 'comisaria_seccional_7', text: 'COMISARIA SECCIONAL 7° SAN LUIS' },
                { value: 'comisaria_seccional_23', text: 'COMISARIA SECCIONAL 23° SAN LUIS' },
                { value: 'comisaria_seccional_28', text: 'COMISARIA SECCIONAL 28° LA PUNTA' },
                { value: 'comisaria_seccional_34', text: 'COMISARIA SECCIONAL 34° JUANA KOSLAY' },
                { value: 'comisaria_seccional_37', text: 'COMISARIA SECCIONAL 37° LA PUNTA' },
                { value: 'comisaria_seccional_38', text: 'COMISARIA SECCIONAL 38° JUANA KOSLAY' },
                { value: 'comisaria_seccional_39', text: 'COMISARIA SECCIONAL 39° SAN LUIS' },
                { value: 'comisaria_seccional_41', text: 'COMISARIA SECCIONAL 41° SAN LUIS' },
                { value: 'comisaria_dtto_1', text: 'COMISARIA DTTO. 1° DURAZNO' },
                { value: 'comisaria_dtto_16', text: 'COMISARIA DTTO. 16° LA TOMA' },
                { value: 'comisaria_dtto_17', text: 'COMISARIA DTTO. 17° EL TRAPICHE' },
                { value: 'comisaria_dtto_33', text: 'COMISARIA DTTO. 33° POTRERO DE LOS FUNES' },
                { value: 'comisaria_dtto_44', text: 'COMISARIA DTTO. 44° VILLA DE LA QUEBRADA' },
                { value: 'comisaria_dtto_46', text: 'COMISARIA DTTO. 46° BEAZLEY' },
                { value: 'comisaria_dtto_47', text: 'COMISARIA DTTO. 47° EL VOLCAN' },
                { value: 'comisaria_dtto_48', text: 'COMISARIA DTTO. 48° BALDE' },
                { value: 'sub_cria_1_ediro', text: 'SUB. CRIA. 1° EDIRO SAN LUIS' },
                { value: 'sub_cria_2_tibiletti', text: 'SUB. CRIA. 2° TIBILETTI SAN LUIS' },
                { value: 'sub_cria_20', text: 'SUB. CRIA. 20° SAN GERONIMO' },
                { value: 'dsto_5', text: 'DSTO. N° 5 ZANJITA' },
                { value: 'dsto_6', text: 'DSTO. N° 6 ALTO PELADO' },
                { value: 'dsto_8', text: 'DSTO. N° 8 ALTO PENOSO' },
                { value: 'dsto_18', text: 'DSTO. N° 18 LA CAROLINA' },
                { value: 'dsto_19', text: 'DSTO. N° 19 LA FLORIDA' },
                { value: 'dsto_20', text: 'DSTO. N° 20 SALADILLA' }
            ],
            urop2: [
                { value: 'comisaria_seccional_1_vm', text: 'COMISARIA SECCIONAL 1° VILLA MERCEDES' },
                { value: 'comisaria_seccional_2_vm', text: 'COMISARIA SECCIONAL 2° VILLA MERCEDES' },
                { value: 'comisaria_seccional_3_vm', text: 'COMISARIA SECCIONAL 3° VILLA MERCEDES' },
                { value: 'comisaria_seccional_4_vm', text: 'COMISARIA SECCIONAL 4° VILLA MERCEDES' },
                { value: 'comisaria_dtto_18', text: 'COMISARIA DTTO. 18° JUSTO DARACT' },
                { value: 'comisaria_dtto_49', text: 'COMISARIA DTTO. 49° FRAGA' },
                { value: 'comisaria_dtto_50', text: 'COMISARIA DTTO. 50° JUAN J. PASCUALE' },
                { value: 'sub_cria_24', text: 'SUB. CRIA. 24° VILLA MERCEDES' },
                { value: 'sub_cria_23', text: 'SUB. CRIA. 23° JUAN JORBA' },
                { value: 'dsto_25', text: 'DSTO. N° 25 LA ESQUINA DEL MORRO' }
            ],
            urop3: [
                { value: 'comisaria_dtto_22', text: 'COMISARIA DTTO. 22° CONCARAN' },
                { value: 'comisaria_dtto_23', text: 'COMISARIA DTTO. 23° TILISARAO' },
                { value: 'comisaria_dtto_24', text: 'COMISARIA DTTO. 24° NASCHEL' },
                { value: 'comisaria_dtto_25', text: 'COMISARIA DTTO. 25° SANTA ROSA' },
                { value: 'comisaria_dtto_26', text: 'COMISARIA DTTO. 26° SAN MARTIN' },
                { value: 'sub_cria_6', text: 'SUB. CRIA. 6° LAFINUR' },
                { value: 'sub_cria_7', text: 'SUB. CRIA. 7° PASO GRANDE' },
                { value: 'sub_cria_8', text: 'SUB. CRIA. 8° SAN FELIPE' },
                { value: 'dsto_9', text: 'DSTO. N° 9 RENCA' },
                { value: 'dsto_10', text: 'DSTO. N° 10 SAN MARTIN' },
                { value: 'dsto_11', text: 'DSTO. N° 11 LAS AGUADAS' },
                { value: 'dsto_21', text: 'DSTO. N° 21 LAS LAGUNAS' },
                { value: 'dsto_22', text: 'DSTO. N° 22 POTRERILLO' },
                { value: 'puesto_26', text: 'PUESTO N° 26 EL TALITA' },
                { value: 'puesto_28', text: 'PUESTO N° 28 LA VERTIENTE' },
                { value: 'puesto_31', text: 'PUESTO N° 31 LAS LAGUNAS' },
                { value: 'puesto_32', text: 'PUESTO N° 32 POTRERILLO' }
            ],
            urop4: [
                { value: 'comisaria_dtto_19', text: 'COMISARIA DTTO. 19° BUENA ESPERANZA' },
                { value: 'comisaria_dtto_20', text: 'COMISARIA DTTO. 20° NUEVA GALIA' },
                { value: 'comisaria_dtto_21', text: 'COMISARIA DTTO. 21° UNION' },
                { value: 'comisaria_dtto_27', text: 'COMISARIA DTTO. 27° ARIZONA' },
                { value: 'dsto_14', text: 'DSTO. N° 14 MARTIN DE LOYOLA' },
                { value: 'dsto_15', text: 'DSTO. N° 15 FORTIN EL PATRIA' },
                { value: 'dsto_16', text: 'DSTO. N° 16 FORTUNA' },
                { value: 'dsto_17', text: 'DSTO. N° 17 BAGUAL' },
                { value: 'dsto_18', text: 'DSTO. N° 18 ANCHORENA' },
                { value: 'dsto_19', text: 'DSTO. N° 19 NAVIA' },
                { value: 'dsto_20', text: 'DSTO. N° 20 NAHUEL MAPA' },
                { value: 'puesto_22', text: 'PUESTO N° 22 SANTA ROSA DEL CANTANTAL' },
                { value: 'puesto_24', text: 'PUESTO N° 24 ARIZONA' }
            ],
            urop5: [
                { value: 'comisaria_dtto_13', text: 'COMISARIA DTTO. 13° SAN FRANCISCO' },
                { value: 'comisaria_dtto_14', text: 'COMISARIA DTTO. 14° LOS MANANTIALES' },
                { value: 'comisaria_dtto_15', text: 'COMISARIA DTTO. 15° QUINES' },
                { value: 'comisaria_dtto_16', text: 'COMISARIA DTTO. 16° LA RIOJA' },
                { value: 'comisaria_dtto_17', text: 'COMISARIA DTTO. 17° LUJAN' },
                { value: 'comisaria_dtto_18', text: 'COMISARIA DTTO. 18° LA CAÑADA' },
                { value: 'sub_cria_11', text: 'SUB. CRIA. 11° LA FLORIDA' },
                { value: 'sub_cria_12', text: 'SUB. CRIA. 12° LA CAROLINA' },
                { value: 'dsto_12', text: 'DSTO. N° 12 POZO DEL TALA' },
                { value: 'dsto_13', text: 'DSTO. N° 13 REPRESA DEL CARMEN' },
                { value: 'dsto_14', text: 'DSTO. N° 14 PARAJE BALZORA' },
                { value: 'dsto_15', text: 'DSTO. N° 15 LA BOTIJA' },
                { value: 'puesto_6', text: 'PUESTO N° 6 ARBOL SOLO' },
                { value: 'puesto_9', text: 'PUESTO N° 9 SAN PEDRO' },
                { value: 'puesto_16', text: 'PUESTO N° 16 SANTA ROSA DE CANTANTAL' }
            ],
            urop6: [
                { value: 'comisaria_seccional_28', text: 'COMISARIA SECCIONAL 28° MERLO' },
                { value: 'comisaria_seccional_42', text: 'COMISARIA SECCIONAL 42° MERLO' },
                { value: 'comisaria_dtto_50', text: 'COMISARIA DTTO. 50° CORTADERAS' },
                { value: 'sub_cria_16', text: 'SUB. CRIA. 16° VILLA DEL CARMEN' },
                { value: 'sub_cria_17', text: 'SUB. CRIA. 17° VILLA LARCA' },
                { value: 'sub_cria_18', text: 'SUB. CRIA. 18° CARPINTERIA' },
                { value: 'dsto_15', text: 'DSTO. N° 15 PAPAGAYOS' },
                { value: 'dsto_27', text: 'DSTO. N° 27 LOS MOLLES' }
            ]
        };

        document.getElementById('urop').addEventListener('change', function() {
            const uropSeleccionada = this.value;
            const lugarSelect = document.getElementById('lugar');
            lugarSelect.innerHTML = '<option value="" disabled selected>Seleccione Comisaría</option>';
            if (comisariasPorUrop[uropSeleccionada]) {
                comisariasPorUrop[uropSeleccionada].forEach(opt => {
                    const option = document.createElement('option');
                    option.value = opt.value;
                    option.textContent = opt.text;
                    lugarSelect.appendChild(option);
                });
                lugarSelect.disabled = false;
            } else {
                lugarSelect.disabled = true;
            }
        });
    </script>
</body>
</html> 