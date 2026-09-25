<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>s
        
        
        
        
        
        
        
        COBAED - Portal Docente</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="this.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="logo-text">
                        <h1>COBAED PLANTEL 09 LOMAS</h1>
                        <p>Portal Docente Integrado con Google Drive</p>
                    </div>
                </div>
                <div class="user-profile">
                    <i class="fas fa-user-circle" style="font-size: 40px;"></i>
                </div>
            </div>
            
            <div class="drive-status">
                <i class="fab fa-google-drive"></i>
                <span>Google Drive conectado | Documentos públicos disponibles</span>
            </div>
        </div>
        
        <!-- Teacher Info -->
        <div class="teacher-card">
            <div class="teacher-avatar">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="teacher-info">
                <h2 style="color: var(--primary-green); margin-bottom: 5px;">Prof. Alejandro Méndez</h2>
                <p style="color: var(--secondary-green);">Docente responsable | Matemáticas | Grupo 301</p>
                <p style="color: #666; font-size: 14px; margin-top: 5px;">
                    <i class="fas fa-envelope"></i> profesor.mendez@cobaed.edu.mx
                </p>
            </div>
        </div>
        
        <!-- Group Selection -->
        <div class="groups-section">
            <h2 class="section-title">
                <i class="fas fa-users"></i> Grupos Asignados
            </h2>
            <div class="group-buttons" id="groupButtons">
                <!-- Groups loaded by JavaScript -->
            </div>
        </div>
        
        <!-- Reports from Administration -->
        <h2 class="section-title" style="color: var(--primary-green); margin: 30px 0 20px;">
            <i class="fas fa-file-contract"></i> Reportes de la Dirección
        </h2>
        <div class="cards-grid" id="adminReports">
            <!-- Cards loaded by JavaScript -->
        </div>
        
        <!-- Teacher Comments -->
        <h2 class="section-title" style="color: var(--primary-green); margin: 30px 0 20px;">
            <i class="fas fa-comment-dots"></i> Comentarios de Docentes
        </h2>
        <div class="cards-grid" id="teacherComments">
            <!-- Cards loaded by JavaScript -->
        </div>
    </div>
    
    <!-- PDF Modal -->
    <div class="modal" id="pdfModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Documento PDF</h3>
                <button onclick="closeModal()" style="background: none; border: none; color: white; font-size: 24px; cursor: pointer;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <iframe class="pdf-viewer" id="pdfViewer" frameborder="0"></iframe>
            </div>
        </div>
    </div>
    
    <!-- Navigation Footer -->
    <div class="nav-footer">
        <a href="#" class="nav-item active">
            <i class="fas fa-home"></i>
            <span>Inicio</span>
        </a>
        <a href="#" class="nav-item">
            <i class="fas fa-file-pdf"></i>
            <span>Documentos</span>
        </a>
        <a href="#" class="nav-item">
            <i class="fas fa-comments"></i>
            <span>Comentarios</span>
        </a>
        <a href="#" class="nav-item">
            <i class="fas fa-cog"></i>
            <span>Ajustes</span>
        </a>
    </div>

    <script src="this.js"></script>
</body>
</html>