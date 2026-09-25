 // ================= CONFIGURACIÓN DE GOOGLE DRIVE =================
 // ID de tu carpeta pública de Google Drive
const DRIVE_FOLDER_ID = '1yPi32yZrcgFeeuqcOQuXolRmkQo_YydA';
const DRIVE_FOLDER_URL = `https://drive.google.com/drive/folders/${DRIVE_FOLDER_ID}`;

// Enlaces a documentos específicos en Google Drive
// Reemplaza estos IDs con los IDs reales de tus archivos en Drive
 
        // ID de tu carpeta pública de Google Drive
        
        
        // Base URL para documentos (puedes cambiar esto por tu backend)
        const BASE_URL = 'https://cobaed.edu.mx/docs/';
        
        // Enlaces a documentos específicos en Google Drive
        // Reemplaza estos IDs con los IDs reales de tus archivos en Drive
        const DRIVE_DOCUMENTS = {
            'mision_vision': '1ABC123DEF456GHI', // ID del archivo en Drive
            'calendario': '1XYZ789UVW012345',
            'encuesta': '1JKL678MNO901234',
            'estadisticas': '1PQR345STU678901',
            'tutorias': '1VWX234YZA567890'
        };
        
        // Función para generar enlace a Google Drive
        function getDriveUrl(fileId, preview = true) {
            if (preview) {
                return `https://drive.google.com/file/d/${fileId}/preview`;
            } else {
                return `https://drive.google.com/uc?export=download&id=${fileId}`;
            }
        }
        
        // ================= DATOS DE LA APLICACIÓN =================
        const groups = [
            { id: 1, name: 'Grupo 301', code: 'MAT301' },
            { id: 2, name: 'Grupo 302', code: 'MAT302' },
            { id: 3, name: 'Grupo 303', code: 'MAT303' },
            { id: 4, name: 'Grupo 304', code: 'MAT304' }
        ];
        
        const adminReports = [
            {
                id: 1,
                title: "Misión, Visión y Política de Calidad",
                description: "Documentos oficiales de la institución. Disponibles en formato PDF desde Google Drive.",
                icon: "fas fa-bullseye",
                status: "Actualizado",
                type: "pdf",
                driveId: DRIVE_DOCUMENTS.mision_vision,
                localUrl: `${BASE_URL}mision-vision.pdf`
            },
            {
                id: 2,
                title: "Encuesta de Satisfacción",
                description: "Resultados de encuestas de satisfacción al cliente. Gráficos e informes detallados.",
                icon: "fas fa-chart-bar",
                status: "Nuevo",
                type: "image",
                driveId: DRIVE_DOCUMENTS.encuesta,
                localUrl: `${BASE_URL}encuesta.png`
            },
            {
                id: 3,
                title: "Reportes Estadísticos por Grupo",
                description: "Análisis estadístico detallado por grupo. Incluye promedios y tendencias.",
                icon: "fas fa-chart-pie",
                status: "Actualizado",
                type: "pdf",
                driveId: DRIVE_DOCUMENTS.estadisticas,
                localUrl: `${BASE_URL}estadisticas.pdf`
            },
            {
                id: 4,
                title: "Estilos de Aprendizaje",
                description: "Análisis de estilos de aprendizaje por grupo. Información para estrategias docentes.",
                icon: "fas fa-brain",
                status: "Pendiente",
                type: "image"
            },
            {
                id: 5,
                title: "Tutorías Académicas",
                description: "Reportes de seguimiento de tutorías académicas. Registro de sesiones y avances.",
                icon: "fas fa-hands-helping",
                status: "Actualizado",
                type: "pdf",
                driveId: DRIVE_DOCUMENTS.tutorias,
                localUrl: `${BASE_URL}tutorias.pdf`
            },
            {
                id: 6,
                title: "Alumnos en Riesgo Académico",
                description: "Listado de alumnos identificados con riesgo académico. Requiere atención inmediata.",
                icon: "fas fa-exclamation-triangle",
                status: "Urgente",
                type: "pdf"
            },
            {
                id: 7,
                title: "Calendarios de Actividades",
                description: "Calendario escolar y actividades programadas. Actualizado mensualmente.",
                icon: "fas fa-calendar-alt",
                status: "Actualizado",
                type: "pdf",
                driveId: DRIVE_DOCUMENTS.calendario,
                localUrl: `${BASE_URL}calendario.pdf`
            }
        ];
        
        const teacherComments = [
            {
                id: 1,
                title: "Recomendaciones Académicas",
                description: "Agregar recomendaciones por grupo en aspectos académicos y socioemocionales.",
                icon: "fas fa-comment-medical",
                status: "Agregar",
                type: "form"
            },
            {
                id: 2,
                title: "Reporte de Indisciplina",
                description: "Registrar situaciones de indisciplina grupal o individual.",
                icon: "fas fa-user-slash",
                status: "Reportar",
                type: "form"
            },
            {
                id: 3,
                title: "Tareas y Evaluaciones",
                description: "Subir tareas, trabajos y materiales de evaluación para los alumnos.",
                icon: "fas fa-tasks",
                status: "Subir",
                type: "upload"
            },
            {
                id: 4,
                title: "Registro de Incidencias",
                description: "Documentar incidencias ocurridas en el aula o durante clases.",
                icon: "fas fa-clipboard-list",
                status: "Registrar",
                type: "form"
            }
        ];
        
        // ================= FUNCIONES DE LA APLICACIÓN =================
        function renderGroups() {
            const container = document.getElementById('groupButtons');
            container.innerHTML = '';
            
            groups.forEach(group => {
                const button = document.createElement('div');
                button.className = 'group-btn' + (group.id === 1 ? ' active' : '');
                button.innerHTML = `
                    ${group.name}
                    <small style="display: block; font-size: 11px; opacity: 0.7;">${group.code}</small>
                `;
                button.onclick = () => selectGroup(group.id);
                container.appendChild(button);
            });
        }
        
        function selectGroup(groupId) {
            // Actualizar botones activos
            document.querySelectorAll('.group-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Actualizar información del docente
            const group = groups.find(g => g.id === groupId);
            document.querySelector('.teacher-info p').innerHTML = 
                `Docente responsable | Matemáticas | ${group.name} (${group.code})`;
            
            // Aquí podrías cargar datos específicos del grupo
            console.log(`Grupo seleccionado: ${group.name}`);
        }
        
        function renderCards() {
            renderAdminReports();
            renderTeacherComments();
        }
        
        function renderAdminReports() {
            const container = document.getElementById('adminReports');
            container.innerHTML = '';
            
            adminReports.forEach(report => {
                const card = document.createElement('div');
                card.className = 'card';
                card.innerHTML = `
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="${report.icon}"></i>
                        </div>
                        <div class="card-status" style="background: ${getStatusColor(report.status)}">
                            ${report.status}
                        </div>
                    </div>
                    <h3 class="card-title">${report.title}</h3>
                    <p class="card-desc">${report.description}</p>
                    <div class="card-actions">
                        ${report.driveId ? `
                            <button class="btn btn-primary" onclick="viewDriveDocument('${report.driveId}', '${report.title}')">
                                <i class="fab fa-google-drive"></i> Ver en Drive
                            </button>
                        ` : ''}
                        ${report.localUrl ? `
                            <button class="btn btn-secondary" onclick="viewLocalDocument('${report.localUrl}', '${report.title}')">
                                <i class="fas fa-file-pdf"></i> Ver PDF
                            </button>
                        ` : ''}
                        ${!report.driveId && !report.localUrl ? `
                            <button class="btn btn-secondary" onclick="simulateAction('${report.title}')">
                                <i class="fas fa-eye"></i> Ver Detalles
                            </button>
                        ` : ''}
                    </div>
                `;
                container.appendChild(card);
            });
        }
        
        function renderTeacherComments() {
            const container = document.getElementById('teacherComments');
            container.innerHTML = '';
            
            teacherComments.forEach(comment => {
                const card = document.createElement('div');
                card.className = 'card';
                card.innerHTML = `
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="${comment.icon}"></i>
                        </div>
                        <div class="card-status" style="background: ${getStatusColor(comment.status)}">
                            ${comment.status}
                        </div>
                    </div>
                    <h3 class="card-title">${comment.title}</h3>
                    <p class="card-desc">${comment.description}</p>
                    <div class="card-actions">
                        <button class="btn btn-primary" onclick="openCommentForm('${comment.type}', '${comment.title}')">
                            <i class="fas fa-edit"></i> ${comment.status}
                        </button>
                    </div>
                `;
                container.appendChild(card);
            });
        }
        
        function getStatusColor(status) {
            switch(status.toLowerCase()) {
                case 'actualizado': return 'rgba(13, 85, 50, 0.1)';
                case 'nuevo': return 'rgba(35, 104, 71, 0.1)';
                case 'urgente': return 'rgba(255, 87, 87, 0.1)';
                case 'pendiente': return 'rgba(221, 208, 169, 0.3)';
                default: return 'rgba(13, 85, 50, 0.1)';
            }
        }
        
        // ================= FUNCIONES DE DOCUMENTOS =================
        function viewDriveDocument(fileId, title) {
            const url = getDriveUrl(fileId, true);
            openPdfModal(url, title);
        }
        
        function viewLocalDocument(url, title) {
            openPdfModal(url, title);
        }
        
        function openPdfModal(url, title) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('pdfViewer').src = url;
            document.getElementById('pdfModal').style.display = 'flex';
            
            // Bloquear scroll del body
            document.body.style.overflow = 'hidden';
        }
        
        function closeModal() {
            document.getElementById('pdfModal').style.display = 'none';
            document.getElementById('pdfViewer').src = '';
            document.body.style.overflow = 'auto';
        }
        
        function simulateAction(title) {
            alert(`Acción simulada para: ${title}\n\nEn la versión final, esto abrirá el documento correspondiente.`);
        }
        
        function openCommentForm(type, title) {
            alert(`Abriendo formulario para: ${title}\n\nTipo: ${type}\n\nEn la versión con backend, esto mostrará un formulario real.`);
        }
        
        // ================= INICIALIZACIÓN =================
        document.addEventListener('DOMContentLoaded', () => {
            renderGroups();
            renderCards();
             
            // Cerrar modal al hacer clic fuera
            document.getElementById('pdfModal').addEventListener('click', (e) => {
                if (e.target.id === 'pdfModal') {
                    closeModal();
                }
            });
            
            // Demo: Simular carga de documentos de Drive
            setTimeout(() => {
                const status = document.querySelector('.drive-status');
                status.innerHTML = `
                    <i class="fab fa-google-drive" style="color: #34A853;"></i>
                    <span>Google Drive conectado | ${adminReports.filter(r => r.driveId).length} documentos cargados</span>
                `;
            }, 1500);
        });