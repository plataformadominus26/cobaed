<!DOCTYPE html>
<html lang="es"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COBAED - Portal Docente</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-green: #0D5532;
            --secondary-green: #236847;
            --dark-green-accent: #09321D;
            --gold: #DDD0A9;
            --background-light: #F9FCF9;
            --text-dark: #222222;
            --text-light: #ffffff;
            --border-light: #e0e0e0;
            --shadow-light: rgba(13, 85, 50, 0.08);
            --shadow-medium: rgba(13, 85, 50, 0.15);
            --shadow-heavy: rgba(9, 50, 29, 0.2);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-light);
            color: var(--text-dark);
            line-height: 1.6;
            padding-bottom: 90px;
            overflow-x: hidden;
        }
        
        .container {
            max-width: 100%;
            padding: 0 16px;
        }
        
        /* Header con gradiente sofisticado */
        .header {
            background: linear-gradient(145deg, var(--dark-green-accent), var(--primary-green) 70%);
            color: var(--text-light);
            padding: 22px 0 18px;
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
            box-shadow: 0 6px 20px var(--shadow-heavy);
            position: sticky;
            top: 0;
            z-index: 100;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at top right, rgba(221, 208, 169, 0.15), transparent 70%);
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            position: relative;
            z-index: 2;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        
        .logo-icon {
            background: linear-gradient(135deg, var(--gold), #f0e6c5);
            color: var(--primary-green);
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .logo-text h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        
        .logo-text p {
            font-size: 12px;
            opacity: 0.9;
            font-weight: 300;
        }
        
        .user-profile {
            background-color: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            cursor: pointer;
            transition: var(--transition);
        }
        
        .user-profile:hover {
            background-color: rgba(255, 255, 255, 0.25);
            transform: scale(1.05);
        }
        
        /* Información del docente */
        .teacher-info {
            background: linear-gradient(to right, rgba(221, 208, 169, 0.12), rgba(35, 104, 71, 0.08));
            margin: 20px 16px;
            padding: 18px;
            border-radius: var(--radius-lg);
            border-left: 5px solid var(--gold);
            display: flex;
            align-items: center;
            box-shadow: 0 4px 12px var(--shadow-light);
            position: relative;
            overflow: hidden;
        }
        
        .teacher-info::after {
            content: '';
            position: absolute;
            top: -10px;
            right: -10px;
            width: 80px;
            height: 80px;
            background: rgba(13, 85, 50, 0.05);
            border-radius: 50%;
        }
        
        .teacher-avatar {
            background: linear-gradient(135deg, var(--gold), #e8dcb5);
            color: var(--primary-green);
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-right: 18px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
        }
        
        .teacher-details h3 {
            color: var(--primary-green);
            margin-bottom: 5px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }
        
        .teacher-details p {
            font-size: 14px;
            color: var(--secondary-green);
            font-weight: 500;
        }
        
        /* Selector de grupo */
        .group-selector {
            margin: 24px 16px;
            padding: 20px;
            background-color: white;
            border-radius: var(--radius-lg);
            box-shadow: 0 6px 18px var(--shadow-medium);
            border-top: 5px solid var(--secondary-green);
            position: relative;
            overflow: hidden;
        }
        
        .group-selector::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, var(--secondary-green), var(--primary-green));
        }
        
        .group-selector h3 {
            color: var(--primary-green);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }
        
        .group-selector h3 i {
            margin-right: 12px;
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green-accent));
            -webkit-background-clip: text;
            background-clip: initial;
            -webkit-text-fill-color: transparent;
        }
        
        .group-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 18px;
        }
        
        .group-btn {
            background-color: var(--background-light);
            border: 2px solid var(--secondary-green);
            color: var(--secondary-green);
            padding: 10px 18px;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            position: relative;
            overflow: hidden;
        }
        
        .group-btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(13, 85, 50, 0.2);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }
        
        .group-btn:focus:not(:active)::after {
            animation: ripple 1s ease-out;
        }
        
        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            100% {
                transform: scale(30, 30);
                opacity: 0;
            }
        }
        
        .group-btn.active {
            background: linear-gradient(135deg, var(--secondary-green), var(--primary-green));
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(35, 104, 71, 0.3);
        }
        
        .group-btn:hover:not(.active) {
            background-color: rgba(35, 104, 71, 0.1);
            transform: translateY(-2px);
        }
        
        /* Títulos de sección */ 
        .section-title {
            color: var(--primary-green);
            margin: 32px 16px 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--gold);
            display: flex;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 20px;
        }
        
        .section-title i {
            margin-right: 12px;
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green-accent));
            -webkit-background-clip: text;
            background-clip: initial;
            -webkit-text-fill-color: transparent;
        }
        
        /* Contenedor de tarjetas */
        .cards-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 18px;
            padding: 0 16px;
        }
        
        .card {
            background-color: white;
            border-radius: var(--radius-lg);
            padding: 22px;
            box-shadow: 0 5px 15px var(--shadow-light);
            transition: var(--transition);
            border-top: 5px solid var(--primary-green);
            position: relative;
            overflow: hidden;
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(to bottom, var(--primary-green), var(--secondary-green));
            opacity: 0;
            transition: var(--transition);
        }
        
        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 24px var(--shadow-medium);
        }
        
        .card:hover::before {
            opacity: 1;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }
        
        .card-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, rgba(13, 85, 50, 0.1), rgba(35, 104, 71, 0.1));
            color: var(--primary-green);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            transition: var(--transition);
        }
        
        .card:hover .card-icon {
            transform: scale(1.1);
            background: linear-gradient(135deg, rgba(13, 85, 50, 0.15), rgba(35, 104, 71, 0.15));
        }
        
        .card-status {
            background: linear-gradient(135deg, var(--gold), #e8dcb5);
            color: var(--dark-green-accent);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .card-title {
            color: var(--dark-green-accent);
            margin-bottom: 12px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 18px;
        }
        
        .card-content {
            font-size: 14px;
            color: #555;
            margin-bottom: 18px;
            line-height: 1.7;
        }
        
        .card-actions {
            display: flex;
            justify-content: flex-end;
        }
        
        .btn {
            background: linear-gradient(135deg, var(--secondary-green), var(--primary-green));
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: var(--transition);
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            box-shadow: 0 4px 10px rgba(13, 85, 50, 0.2);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(13, 85, 50, 0.3);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        /* Navegación inferior */
        .nav-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: white;
            display: flex;
            justify-content: space-around;
            padding: 18px 0;
            box-shadow: 0 -5px 20px var(--shadow-medium);
            z-index: 100;
            border-top-left-radius: 24px;
            border-top-right-radius: 24px;
        }
        
        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #888;
            text-decoration: none;
            font-size: 12px;
            transition: var(--transition);
            position: relative;
        }
        
        .nav-item i {
            font-size: 22px;
            margin-bottom: 6px;
            transition: var(--transition);
        }
        
        .nav-item.active {
            color: var(--primary-green);
            font-weight: 600;
        }
        
        .nav-item.active i {
            transform: translateY(-5px);
        }
        
        .nav-item.active::after {
            content: '';
            position: absolute;
            bottom: -18px;
            width: 40px;
            height: 4px;
            background: linear-gradient(to right, var(--primary-green), var(--secondary-green));
            border-radius: 2px;
        }
        
        /* Indicador de notificaciones */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: 5px;
            background-color: #ff4757;
            color: white;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        /* Efectos de carga */
        .loading-skeleton {
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        /* Responsive */
        @media (min-width: 768px) {
            .cards-container {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .container {
                max-width: 750px;
                margin: 0 auto;
            }
        }
        
        @media (min-width: 1024px) {
            .container {
                max-width: 980px;
            }
        }
        
        /* Scroll personalizado */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb { 
            background: linear-gradient(to bottom, var(--secondary-green), var(--primary-green));
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="logo-section">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="logo-text">
                        <h1>COBAED PLANTEL 09 LOMAS</h1>
                        <p>Portal Docente</p>
                    </div>
                </div>
                <div class="user-profile">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="teacher-info">
            <div class="teacher-avatar">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="teacher-details">
                <h3>Prof. Alejandro Méndez</h3>
                <p>Docente responsable de grupo | Matemáticas</p>
            </div>
        </div>
        
        <div class="group-selector">
            <h3><i class="fas fa-users"></i> Selecciona Tu Grupo</h3>
            <p>Elige un grupo para ver y gestionar reportes</p>
            <div class="group-buttons">
                <div class="group-btn active">Grupo 301</div>
                <div class="group-btn">Grupo 302</div>
                <div class="group-btn">Grupo 303</div>
                <div class="group-btn">Grupo 304</div>
                <div class="group-btn">+ Agregar Grupo</div>
            </div>
        </div>
        
        <h2 class="section-title"><i class="fas fa-file-alt"></i> Reportes de la Dirección</h2>
        <div class="cards-container" id="admin-reports">
            <!-- Tarjetas generadas por JavaScript -->
        </div>
        
        <h2 class="section-title"><i class="fas fa-comment-alt"></i> Comentarios de Docentes</h2>
        <div class="cards-container" id="teacher-comments">
            <!-- Tarjetas generadas por JavaScript -->
        </div>
    </div>
    
    <div class="nav-footer">
        <a href="#" class="nav-item active">
            <i class="fas fa-home"></i>
            <span>Inicio</span>
        </a>
        <a href="#" class="nav-item">
            <i class="fas fa-clipboard-list"></i>
            <span>Reportes</span>
            <div class="notification-badge">3</div>
        </a>
        <a href="#" class="nav-item">
            <i class="fas fa-tasks"></i>
            <span>Tareas</span>
        </a>
        <a href="#" class="nav-item">
            <i class="fas fa-cog"></i>
            <span>Ajustes</span>
        </a>
    </div>

    <script>
        // Base URL para los documentos (se puede cambiar más tarde)
        const baseUrl = "https://cobaed.edu.mx/docs/";
        
        // Datos para las tarjetas de "Reportes de la Dirección"
        const adminReports = [
            {
                id: 1,
                title: "Misión, Visión, Objetivos de Calidad",
                description: "Documentos oficiales de la institución en formato texto y PDF.",
                icon: "fas fa-bullseye",
                status: "Actualizado",
                buttonText: "Ver Documentos",
                type: "admin"
            },
            {
                id: 2,
                title: "Encuesta de Satisfacción",
                description: "Resultados de encuestas de satisfacción al cliente en formato imagen.",
                icon: "fas fa-chart-bar",
                status: "Nuevo",
                buttonText: "Ver Resultados",
                type: "admin"
            },
            {
                id: 3,
                title: "Reportes Estadísticos por Grupo",
                description: "Imágenes y reportes PDF con análisis estadísticos por grupo.",
                icon: "fas fa-chart-pie",
                status: "Actualizado",
                buttonText: "Ver Reportes",
                type: "admin"
            },
            {
                id: 4,
                title: "Estilos de Aprendizaje",
                description: "Imágenes con análisis de estilos de aprendizaje por grupo.",
                icon: "fas fa-brain",
                status: "Pendiente",
                buttonText: "Ver Análisis",
                type: "admin"
            },
            {
                id: 5,
                title: "Tutorías Académicas",
                description: "Reportes e imágenes de seguimiento de tutorías académicas.",
                icon: "fas fa-hands-helping",
                status: "Actualizado",
                buttonText: "Ver Reportes",
                type: "admin"
            },
            {
                id: 6,
                title: "Alumnos en Riesgo Académico",
                description: "Reporte PDF de alumnos identificados en riesgo académico.",
                icon: "fas fa-exclamation-triangle",
                status: "Urgente",
                buttonText: "Ver Reporte",
                type: "admin"
            },
            {
                id: 7,
                title: "Estatus Académico",
                description: "Reporte de baja, suspensión temporal o conducta de alumnos.",
                icon: "fas fa-user-graduate",
                status: "Actualizado",
                buttonText: "Consultar",
                type: "admin"
            },
            {
                id: 8,
                title: "Alumnos con Situación Especial",
                description: "Registro de alumnos con situaciones especiales (texto libre).",
                icon: "fas fa-heart",
                status: "Confidencial",
                buttonText: "Ver Registro",
                type: "admin"
            },
            {
                id: 9,
                title: "Calendarios de Actividades",
                description: "Imágenes y documentos con calendarios de actividades.",
                icon: "fas fa-calendar-alt",
                status: "Actualizado",
                buttonText: "Ver Calendarios",
                type: "admin"
            },
            {
                id: 10,
                title: "Convocatorias para Horas",
                description: "Documentos de convocatorias para adjudicación de horas.",
                icon: "fas fa-clock",
                status: "Nuevo",
                buttonText: "Ver Convocatorias",
                type: "admin"
            },
            {
                id: 11,
                title: "Horario de Grupo",
                description: "Horario asignado para cada grupo.",
                icon: "fas fa-calendar-day",
                status: "Actualizado",
                buttonText: "Ver Horarios",
                type: "admin"
            }
        ];
        
        // Datos para las tarjetas de "Comentarios de Docentes"
        const teacherComments = [
            {
                id: 1,
                title: "Recomendaciones por Grupo",
                description: "Recomendaciones académicas, socioemocionales y actitudinales por grupo.",
                icon: "fas fa-comments",
                status: "Agregar",
                buttonText: "Agregar Comentarios",
                type: "teacher"
            },
            {
                id: 2,
                title: "Recomendaciones Individuales",
                description: "Recomendaciones personalizadas por alumno (texto libre).",
                icon: "fas fa-user-edit",
                status: "Agregar",
                buttonText: "Agregar Recomendaciones",
                type: "teacher"
            },
            {
                id: 3,
                title: "Reporte de Indisciplina",
                description: "Registro de situaciones de indisciplina grupal (texto libre).",
                icon: "fas fa-exclamation-circle",
                status: "Agregar",
                buttonText: "Reportar Indisciplina",
                type: "teacher"
            },
            {
                id: 4,
                title: "Limpieza y Mantenimiento",
                description: "Reporte de estado de limpieza y mantenimiento del aula.",
                icon: "fas fa-broom",
                status: "Agregar",
                buttonText: "Reportar Situación",
                type: "teacher"
            },
            {
                id: 5,
                title: "Tareas y Evaluaciones",
                description: "Apartado para subir tareas, trabajos y evaluaciones.",
                icon: "fas fa-tasks",
                status: "Subir Archivos",
                buttonText: "Subir Archivos",
                type: "teacher"
            },
            {
                id: 6,
                title: "Reporte de Incidencias",
                description: "Registro de incidencias ocurridas en el aula.",
                icon: "fas fa-clipboard-check",
                status: "Agregar",
                buttonText: "Reportar Incidencia",
                type: "teacher"
            },
            {
                id: 7,
                title: "Pase de Salida",
                description: "Registro de pases de salida para alumnos.",
                icon: "fas fa-door-open",
                status: "Agregar",
                buttonText: "Registrar Pase",
                type: "teacher"
            }
        ];
        
        // Función para crear tarjetas
        function createCard(item) {
            return `
                <div class="card" data-id="${item.id}" data-type="${item.type}">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="${item.icon}"></i>
                        </div>
                        <div class="card-status">${item.status}</div>
                    </div>
                    <h3 class="card-title">${item.title}</h3>
                    <p class="card-content">${item.description}</p>
                    <div class="card-actions">
                        <button class="btn" onclick="handleCardClick(${item.id}, '${item.type}')">
                            <i class="fas fa-arrow-right"></i> ${item.buttonText}
                        </button>
                    </div>
                </div>
            `;
        }
        
        // Función para manejar clics en tarjetas
        function handleCardClick(id, type) {
            const items = type === 'admin' ? adminReports : teacherComments;
            const item = items.find(item => item.id === id);
            
            if (item) {
                alert(`Accediendo a: ${item.title}\n\nEsta funcionalidad se conectará al backend en la siguiente fase.`);
                
                // Simular carga
                const card = document.querySelector(`[data-id="${id}"][data-type="${type}"]`);
                card.classList.add('loading-skeleton');
                
                setTimeout(() => {
                    card.classList.remove('loading-skeleton');
                }, 800);
            }
        }
        
        // Función para inicializar la interfaz
        function initializeInterface() {
            const adminContainer = document.getElementById('admin-reports');
            const teacherContainer = document.getElementById('teacher-comments');
            
            // Generar tarjetas para reportes de administración
            adminReports.forEach(report => {
                adminContainer.innerHTML += createCard(report);
            });
            
            // Generar tarjetas para comentarios de docentes
            teacherComments.forEach(comment => {
                teacherContainer.innerHTML += createCard(comment);
            });
            
            // Agregar funcionalidad a los botones de grupo
            const groupButtons = document.querySelectorAll('.group-btn');
            groupButtons.forEach(button => {
                button.addEventListener('click', function() {
                    groupButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Simular cambio de grupo
                    const groupName = this.textContent;
                    document.querySelector('.teacher-details p').innerHTML = 
                        `Docente responsable de grupo | Matemáticas | ${groupName}`;
                });
            });
        }
        
        // Inicializar la interfaz cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', initializeInterface);
        
        // Simular notificaciones
        setTimeout(() => {
            const notificationBadge = document.querySelector('.notification-badge');
            notificationBadge.style.animation = 'none';
            setTimeout(() => {
                notificationBadge.style.animation = 'pulse 1.5s infinite';
            }, 10);
        }, 2000);
    </script>
</body>
</html> 