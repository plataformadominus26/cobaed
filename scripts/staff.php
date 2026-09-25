<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Personal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a6fa5;
            --secondary-color: #6b8cae;
            --accent-color: #ff9f1c;
            --dark-color: #2d3e50;
            --light-color: #f8f9fa;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
            font-size: 14px;
        }
        
        .mobile-container {
            max-width: 100%;
            margin: 0 auto;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Header */
        .mobile-header {
            background-color: var(--dark-color);
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 100;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .mobile-header h1 {
            font-size: 1.2rem;
        }
        
        .menu-toggle {
            font-size: 1.5rem;
            background: none;
            border: none;
            color: white;
        }
        
        /* Main Content */
        .mobile-main {
            padding: 70px 15px 70px;
            min-height: 100vh;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            gap: 10px;
        }
        
        .action-btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        
        .add-btn {
            background-color: var(--success-color);
            color: white;
        }
        
        .edit-btn {
            background-color: var(--warning-color);
            color: #333;
        }
        
        .delete-btn {
            background-color: var(--danger-color);
            color: white;
        }
        
        /* Staff Cards */
        .staff-cards-container {
            margin-bottom: 20px;
        }
        
      .staff-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            border-left: 4px solid var(--primary-color);
            transition: all 0.3s ease;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            position: relative;
        }
        
        .staff-card.active {
            border-left-color: var(--accent-color);
            background-color: #f8fbff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .staff-card h3 {
            grid-column: 1 / -1;
            color: var(--dark-color);
            margin-bottom: 5px;
            font-size: 1.1rem;
            padding-bottom: 8px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .card-field {
            display: flex;
            flex-direction: column;
        }
        
        .card-label {
            font-size: 0.75rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        
        .card-value {
            font-weight: 500;
            color: #333;
            word-break: break-word;
        }
        
        .status-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: bold;
        }
        
        .status-active {
            background-color: #e6f7ee;
            color: var(--success-color);
        }
        
        .status-inactive {
            background-color: #ffebee;
            color: var(--danger-color);
        }
        
        .status-vacaciones {
            background-color: #fff8e6;
            color: var(--warning-color);
        }
        
        .status-licencia {
            background-color: #e6f3ff;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <!-- Header -->
        <header class="mobile-header">
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h1>Personal</h1>
            <div></div> <!-- Spacer -->
        </header>
        
        <!-- Main Content -->
        <main class="mobile-main">
            <!-- Action Buttons -->
            <div class="action-buttons">
                <button class="action-btn add-btn" id="addStaffBtn">
                    <i class="fas fa-plus"></i> Agregar
                </button>
                <button class="action-btn edit-btn" id="editStaffBtn" disabled>
                    <i class="fas fa-edit"></i> Modificar
                </button>
                <button class="action-btn delete-btn" id="deleteStaffBtn" disabled>
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
            
            <!-- Staff Cards -->
            <div class="staff-cards-container" id="staffCardsContainer">
                <!-- Cards will be dynamically inserted here -->
            </div>
            
            <!-- Pagination -->
            <div class="pagination" id="pagination">
                <!-- Pagination buttons will be dynamically inserted here -->
            </div>
        </main>
        
        <!-- Bottom Navigation -->
        <nav class="mobile-bottom-nav">
            <a href="#" class="nav-item">
                <i class="fas fa-home"></i>
                <span>Inicio</span>
            </a>
            <a href="#" class="nav-item active">
                <i class="fas fa-users"></i>
                <span>Personal</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Horarios</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Ajustes</span>
            </a>
        </nav>
        
        <!-- Add/Edit Staff Modal -->
        <div class="modal" id="staffModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 id="modalTitle">Agregar Personal</h2>
                    <button class="close-modal" id="closeModal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="staffForm">
                        <input type="hidden" id="staffId">
                        
                        <div class="form-group">
                            <label for="nombre">Nombre completo</label>
                            <input type="text" id="nombre" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="puesto">Puesto</label>
                            <select id="puesto" class="form-control" required>
                                <option value="">Seleccionar puesto</option>
                                <option value="Mesero">Mesero</option>
                                <option value="Cocinero">Cocinero</option>
                                <option value="Bartender">Bartender</option>
                                <option value="Gerente">Gerente</option>
                                <option value="Limpieza">Limpieza</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="departamento">Departamento</label>
                            <select id="departamento" class="form-control" required>
                                <option value="">Seleccionar departamento</option>
                                <option value="Servicio">Servicio</option>
                                <option value="Cocina">Cocina</option>
                                <option value="Bar">Bar</option>
                                <option value="Administración">Administración</option>
                                <option value="Limpieza">Limpieza</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="horario">Horario</label>
                            <select id="horario" class="form-control" required>
                                <option value="">Seleccionar horario</option>
                                <option value="Matutino (8am-4pm)">Matutino (8am-4pm)</option>
                                <option value="Vespertino (4pm-12am)">Vespertino (4pm-12am)</option>
                                <option value="Nocturno (12am-8am)">Nocturno (12am-8am)</option>
                                <option value="Medio tiempo">Medio tiempo</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="estatus">Estatus</label>
                            <select id="estatus" class="form-control" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                                <option value="Vacaciones">Vacaciones</option>
                                <option value="Licencia">Licencia</option>
                            </select>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" id="cancelBtn">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sample Data
        const staffData = [
            { id: 1, nombre: "Juan Pérez", puesto: "Mesero", departamento: "Servicio", horario: "Matutino (8am-4pm)", estatus: "Activo" },
            { id: 2, nombre: "María García", puesto: "Cocinero", departamento: "Cocina", horario: "Vespertino (4pm-12am)", estatus: "Activo" },
            { id: 3, nombre: "Carlos López", puesto: "Bartender", departamento: "Bar", horario: "Nocturno (12am-8am)", estatus: "Activo" },
            { id: 4, nombre: "Ana Martínez", puesto: "Gerente", departamento: "Administración", horario: "Matutino (8am-4pm)", estatus: "Activo" },
            { id: 5, nombre: "Luisa Rodríguez", puesto: "Limpieza", departamento: "Limpieza", horario: "Medio tiempo", estatus: "Activo" },
            { id: 6, nombre: "Pedro Sánchez", puesto: "Mesero", departamento: "Servicio", horario: "Vespertino (4pm-12am)", estatus: "Inactivo" },
            { id: 7, nombre: "Sofía Ramírez", puesto: "Cocinero", departamento: "Cocina", horario: "Matutino (8am-4pm)", estatus: "Vacaciones" },
            { id: 8, nombre: "Jorge Torres", puesto: "Bartender", departamento: "Bar", horario: "Nocturno (12am-8am)", estatus: "Activo" },
            { id: 9, nombre: "Elena Castro", puesto: "Mesero", departamento: "Servicio", horario: "Vespertino (4pm-12am)", estatus: "Licencia" },
            { id: 10, nombre: "Miguel Díaz", puesto: "Gerente", departamento: "Administración", horario: "Matutino (8am-4pm)", estatus: "Activo" },
            { id: 11, nombre: "Laura Morales", puesto: "Limpieza", departamento: "Limpieza", horario: "Medio tiempo", estatus: "Activo" },
            { id: 12, nombre: "Roberto Herrera", puesto: "Mesero", departamento: "Servicio", horario: "Vespertino (4pm-12am)", estatus: "Activo" }
        ];

        // DOM Elements
        const staffCardsContainer = document.getElementById('staffCardsContainer');
        const pagination = document.getElementById('pagination');
        const addStaffBtn = document.getElementById('addStaffBtn');
        const editStaffBtn = document.getElementById('editStaffBtn');
        const deleteStaffBtn = document.getElementById('deleteStaffBtn');
        const staffModal = document.getElementById('staffModal');
        const modalTitle = document.getElementById('modalTitle');
        const staffForm = document.getElementById('staffForm');
        const staffIdInput = document.getElementById('staffId');
        const closeModal = document.getElementById('closeModal');
        const cancelBtn = document.getElementById('cancelBtn');

        // Variables
        let currentPage = 1;
        const cardsPerPage = 5;
        let selectedStaffId = null;
        let isEditMode = false;

        // Initialize the app
        document.addEventListener('DOMContentLoaded', function() {
            renderStaffCards();
            renderPagination();
            
            // Set first card as active by default
            if (staffData.length > 0) {
                const firstCard = document.querySelector('.staff-card');
                if (firstCard) {
                    firstCard.classList.add('active');
                    selectedStaffId = parseInt(firstCard.dataset.id);
                    enableActionButtons();
                }
            }
        });

        // Render staff cards for current page
         function renderStaffCards() {
            staffCardsContainer.innerHTML = '';
            
            const startIndex = (currentPage - 1) * cardsPerPage;
            const endIndex = Math.min(startIndex + cardsPerPage, staffData.length);
            
            for (let i = startIndex; i < endIndex; i++) {
                const staff = staffData[i];
                const statusClass = `status-${staff.estatus.toLowerCase().replace(' ', '-')}`;
                
                const card = document.createElement('div');
                card.className = 'staff-card';
                card.dataset.id = staff.id;
                card.innerHTML = `
                    <h3>${staff.nombre}</h3>
                    <span class="status-badge ${statusClass}">${staff.estatus}</span>
                    
                    <div class="card-field">
                        <span class="card-label">Puesto</span>
                        <span class="card-value">${staff.puesto}</span>
                    </div>
                    
                    <div class="card-field">
                        <span class="card-label">Departamento</span>
                        <span class="card-value">${staff.departamento}</span>
                    </div>
                    
                    <div class="card-field">
                        <span class="card-label">Horario</span>
                        <span class="card-value">${staff.horario}</span>
                    </div>
                    
                    <div class="card-field">
                        <span class="card-label">ID</span>
                        <span class="card-value">${staff.id}</span>
                    </div>
                `;
                
                card.addEventListener('click', function() {
                    document.querySelectorAll('.staff-card').forEach(c => {
                        c.classList.remove('active');
                    });
                    this.classList.add('active');
                    selectedStaffId = parseInt(this.dataset.id);
                    enableActionButtons();
                });
                
                staffCardsContainer.appendChild(card);
            }
        }

        // Render pagination buttons
        function renderPagination() {
            pagination.innerHTML = '';
            
            const totalPages = Math.ceil(staffData.length / cardsPerPage);
            
            // Previous button
            const prevBtn = document.createElement('button');
            prevBtn.className = 'page-btn';
            prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
            prevBtn.disabled = currentPage === 1;
            prevBtn.addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    renderStaffCards();
                    renderPagination();
                }
            });
            pagination.appendChild(prevBtn);
            
            // Page buttons
            for (let i = 1; i <= totalPages; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.className = `page-btn ${i === currentPage ? 'active' : ''}`;
                pageBtn.textContent = i;
                pageBtn.addEventListener('click', function() {
                    currentPage = i;
                    renderStaffCards();
                    renderPagination();
                });
                pagination.appendChild(pageBtn);
            }
            
            // Next button
            const nextBtn = document.createElement('button');
            nextBtn.className = 'page-btn';
            nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.addEventListener('click', function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    renderStaffCards();
                    renderPagination();
                }
            });
            pagination.appendChild(nextBtn);
        }

        // Enable/disable action buttons based on selection
        function enableActionButtons() {
            editStaffBtn.disabled = !selectedStaffId;
            deleteStaffBtn.disabled = !selectedStaffId;
        }

        // Open modal for adding new staff
        addStaffBtn.addEventListener('click', function() {
            isEditMode = false;
            modalTitle.textContent = 'Agregar Personal';
            staffForm.reset();
            staffIdInput.value = '';
            staffModal.style.display = 'block';
        });

        // Open modal for editing staff
        editStaffBtn.addEventListener('click', function() {
            if (!selectedStaffId) return;
            
            isEditMode = true;
            modalTitle.textContent = 'Modificar Personal';
            
            const staff = staffData.find(s => s.id === selectedStaffId);
            if (staff) {
                document.getElementById('nombre').value = staff.nombre;
                document.getElementById('puesto').value = staff.puesto;
                document.getElementById('departamento').value = staff.departamento;
                document.getElementById('horario').value = staff.horario;
                document.getElementById('estatus').value = staff.estatus;
                staffIdInput.value = staff.id;
            }
            
            staffModal.style.display = 'block';
        });

        // Delete selected staff
        deleteStaffBtn.addEventListener('click', function() {
            if (!selectedStaffId) return;
            
            if (confirm('¿Estás seguro de que deseas eliminar este registro?')) {
                const index = staffData.findIndex(s => s.id === selectedStaffId);
                if (index !== -1) {
                    staffData.splice(index, 1);
                    renderStaffCards();
                    renderPagination();
                    selectedStaffId = null;
                    enableActionButtons();
                    
                    // If we deleted the last item on the page, go to previous page
                    if (staffData.length > 0 && (currentPage - 1) * cardsPerPage >= staffData.length) {
                        currentPage--;
                        renderStaffCards();
                        renderPagination();
                    }
                    
                    // Set first card as active if available
                    const firstCard = document.querySelector('.staff-card');
                    if (firstCard) {
                        firstCard.classList.add('active');
                        selectedStaffId = parseInt(firstCard.dataset.id);
                        enableActionButtons();
                    }
                }
            }
        });

        // Close modal
        function closeStaffModal() {
            staffModal.style.display = 'none';
        }

        closeModal.addEventListener('click', closeStaffModal);
        cancelBtn.addEventListener('click', closeStaffModal);

        // Handle form submission
        staffForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const staff = {
                id: isEditMode ? parseInt(staffIdInput.value) : generateId(),
                nombre: document.getElementById('nombre').value,
                puesto: document.getElementById('puesto').value,
                departamento: document.getElementById('departamento').value,
                horario: document.getElementById('horario').value,
                estatus: document.getElementById('estatus').value
            };
            
            if (isEditMode) {
                // Update existing staff
                const index = staffData.findIndex(s => s.id === staff.id);
                if (index !== -1) {
                    staffData[index] = staff;
                }
            } else {
                // Add new staff
                staffData.push(staff);
            }
            
            // Re-render and close modal
            renderStaffCards();
            renderPagination();
            closeStaffModal();
            
            // Select the newly added/modified staff
            selectedStaffId = staff.id;
            const staffCard = document.querySelector(`.staff-card[data-id="${staff.id}"]`);
            if (staffCard) {
                document.querySelectorAll('.staff-card').forEach(c => {
                    c.classList.remove('active');
                });
                staffCard.classList.add('active');
                enableActionButtons();
            }
        });

        // Generate unique ID for new staff
        function generateId() {
            return staffData.length > 0 ? Math.max(...staffData.map(s => s.id)) + 1 : 1;
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target === staffModal) {
                closeStaffModal();
            }
        });
    </script>
</body>
</html>