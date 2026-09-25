<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #e74c3c;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
        }
        .header {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .main-content {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
        }
        .footer {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 0;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
        }
        .category-card, .item-card {
            cursor: pointer;
            transition: all 0.3s;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            border: none;
        }
        .category-card:hover, .item-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .category-img, .item-img {
            height: 100px;
            object-fit: cover;
        }
        .item-price {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        .badge-order {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--secondary-color);
        }
        .btn-action {
            border-radius: 50px;
            padding: 10px 15px;
            font-size: 0.9rem;
        }
        .nav-tabs .nav-link {
            color: white;
            border: none;
        }
        .nav-tabs .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            font-weight: bold;
        }
        #itemsPanel {
            display: none;
        }
    </style>
</head>
<body>
    <!-- Cabecera -->
    <header class="header text-center">
        <h4 class="mb-0">Merendero del norte</h4>
        <small class="d-block">Mesero: <strong>Juan Pérez</strong> | Mesa: <span id="currentTable">-</span></small>
    </header>

    <!-- Contenido Principal -->
    <main class="main-content">
        <!-- Panel de Categorías -->
        <div id="categoriesPanel">
            <h5 class="mb-3">Categorías</h5>
            <div class="row g-2" id="categoriesContainer">
                <!-- Categorías se cargarán aquí -->
            </div>
        </div>

        <!-- Panel de Platos -->
        <div id="itemsPanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button class="btn btn-sm btn-secondary" id="backToCategories">
                    <i class="fas fa-arrow-left"></i> Atrás
                </button>
                <h5 class="mb-0 text-center" id="categoryTitle"></h5>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-secondary" id="btnSearch">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="row g-2" id="itemsContainer">
                <!-- Platos se cargarán aquí -->
            </div>
        </div>
    </main>

    <!-- Pie de página -->
    <footer class="footer">
        <div class="container">
            <div class="d-flex justify-content-between">
                <button class="btn btn-action btn-light" id="btnCurrentOrder">
                    <i class="fas fa-clipboard-list"></i>
                    <span class="badge bg-danger badge-order" id="orderBadge">0</span>
                </button>
                <button class="btn btn-action btn-success" id="btnCheckout">
                    <i class="fas fa-cash-register"></i> Cobrar
                </button>
                <button class="btn btn-action btn-danger" id="btnClear">
                    <i class="fas fa-trash"></i> Limpiar
                </button>
                <button class="btn btn-action btn-info" id="btnTables">
                    <i class="fas fa-chair"></i> Mesas
                </button>
            </div>
        </div>
    </footer>

    <!-- Modal del Pedido Actual -->
    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Pedido Actual</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Mesa:</label>
                        <select class="form-select" id="tableSelect">
                            <option value="1">Mesa 1</option>
                            <option value="2">Mesa 2</option>
                            <option value="3">Mesa 3</option>
                            <option value="4">Mesa 4</option>
                            <option value="5">Mesa 5</option>
                        </select>
                    </div>
                    <div id="orderItems">
                        <p class="text-muted text-center">No hay productos en el pedido</p>
                    </div>
                    <div class="bg-light p-3 rounded mt-3">
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong id="modalTotal">$0.00</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnSendOrder">Enviar a Cocina</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery, Bootstrap JS y Popper -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script de la aplicación -->
    <script>
        $(document).ready(function() {
            // Datos de ejemplo (mismos que en la versión desktop)
            const categories = [
                { id: 1, name: "Entradas", img: "https://images.unsplash.com/photo-1544025162-d76694265947" },
                { id: 2, name: "Ensaladas", img: "https://images.unsplash.com/photo-1546793665-c74683f339c1" },
                { id: 3, name: "Sopas", img: "https://images.unsplash.com/photo-1476718406336-bb5a9690ee2a" },
                { id: 4, name: "Pasta", img: "https://images.unsplash.com/photo-1555949258-eb67b1ef0ceb" },
                { id: 5, name: "Carnes", img: "https://images.unsplash.com/photo-1558030006-450675393462" },
                { id: 6, name: "Pescados", img: "https://images.unsplash.com/photo-1561154464-82e9adf32764" },
                { id: 7, name: "Postres", img: "https://images.unsplash.com/photo-1563805042-7684c019e1cb" },
                { id: 8, name: "Bebidas", img: "https://images.unsplash.com/photo-1551024506-0bccd828d307" },
                { id: 9, name: "Especiales", img: "https://images.unsplash.com/photo-1565299624946-b28f40a0ae38" }
            ];

            const items = {
                1: [
                    { id: 101, name: "Nachos", price: 85, img: "https://images.unsplash.com/photo-1571407970349-bc81e7e96d47" },
                    { id: 102, name: "Alitas", price: 120, img: "https://images.unsplash.com/photo-1561758033-d89a9ad46330" },
                    { id: 103, name: "Quesadilla", price: 95, img: "https://images.unsplash.com/photo-1615870216519-2f9fa575fa5c" }
                ],
                2: [
                    { id: 201, name: "César", price: 90, img: "https://images.unsplash.com/photo-1546793665-c74683f339c1" },
                    { id: 202, name: "Griega", price: 110, img: "https://images.unsplash.com/photo-1546069901-ba9599a7e63c" },
                    { id: 203, name: "Caprese", price: 100, img: "https://images.unsplash.com/photo-1551248429-40975aa4de74" }
                ],
                // ... (agregar más items como en la versión desktop)
            };

            // Variables del pedido
            let currentOrder = [];
            let currentTable = null;

            // Cargar categorías
            function loadCategories() {
                const $container = $('#categoriesContainer');
                $container.empty();
                
                categories.forEach(category => {
                    $container.append(`
                        <div class="col-4">
                            <div class="category-card card" data-id="${category.id}">
                                <img src="${category.img}" class="category-img card-img-top">
                                <div class="card-body p-2 text-center">
                                    <h6 class="card-title mb-0">${category.name}</h6>
                                </div>
                            </div>
                        </div>
                    `);
                });
            }

            // Cargar items de categoría
            function loadItems(categoryId) {
                const category = categories.find(c => c.id == categoryId);
                $('#categoryTitle').text(category.name);
                
                const $container = $('#itemsContainer');
                $container.empty();
                
                if (items[categoryId]) {
                    items[categoryId].forEach(item => {
                        $container.append(`
                            <div class="col-4">
                                <div class="item-card card" data-id="${item.id}" data-price="${item.price}">
                                    <div class="position-relative">
                                        <img src="${item.img}" class="item-img card-img-top">
                                        <span class="item-price">$${item.price.toFixed(2)}</span>
                                    </div>
                                    <div class="card-body p-2 text-center">
                                        <h6 class="card-title mb-0">${item.name}</h6>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                }
                
                $('#categoriesPanel').hide();
                $('#itemsPanel').show();
            }

            // Actualizar badge del pedido
            function updateOrderBadge() {
                const totalItems = currentOrder.reduce((sum, item) => sum + item.quantity, 0);
                $('#orderBadge').text(totalItems);
                if (totalItems > 0) {
                    $('#orderBadge').show();
                } else {
                    $('#orderBadge').hide();
                }
            }

            // Mostrar modal de pedido
            function showOrderModal() {
                const $container = $('#orderItems');
                $container.empty();
                
                if (currentOrder.length === 0) {
                    $container.html('<p class="text-muted text-center">No hay productos en el pedido</p>');
                } else {
                    currentOrder.forEach((item, index) => {
                        $container.append(`
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong>${item.name}</strong>
                                    <div class="text-muted">$${item.price.toFixed(2)} x ${item.quantity}</div>
                                </div>
                                <div>
                                    <span class="fw-bold">$${(item.price * item.quantity).toFixed(2)}</span>
                                    <button class="btn btn-sm btn-outline-danger ms-2 remove-item" data-index="${index}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        `);
                    });
                }
                
                // Calcular total
                const total = currentOrder.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                $('#modalTotal').text('$' + total.toFixed(2));
                
                // Mostrar modal
                const modal = new bootstrap.Modal('#orderModal');
                modal.show();
            }

            // Event Listeners
            $(document).on('click', '.category-card', function() {
                const categoryId = $(this).data('id');
                loadItems(categoryId);
            });

            $(document).on('click', '.item-card', function() {
                const itemId = $(this).data('id');
                const itemPrice = $(this).data('price');
                const itemName = $(this).find('.card-title').text();
                
                // Verificar si ya existe en el pedido
                const existingItem = currentOrder.find(item => item.id === itemId);
                
                if (existingItem) {
                    existingItem.quantity++;
                } else {
                    currentOrder.push({
                        id: itemId,
                        name: itemName,
                        price: itemPrice,
                        quantity: 1
                    });
                }
                
                updateOrderBadge();
            });

            $('#backToCategories').click(function() {
                $('#itemsPanel').hide();
                $('#categoriesPanel').show();
            });

            $('#btnCurrentOrder').click(function() {
                showOrderModal();
            });

            $('#btnCheckout').click(function() {
                if (currentOrder.length === 0) {
                    alert('No hay productos en el pedido');
                    return;
                }
                
                if (!$('#tableSelect').val()) {
                    alert('Seleccione una mesa primero');
                    return;
                }
                
                // Aquí iría la lógica para enviar a cocina
                alert(`Pedido enviado a cocina para Mesa ${$('#tableSelect').val()}`);
                currentOrder = [];
                updateOrderBadge();
                $('#orderModal').modal('hide');
            });

            $('#btnClear').click(function() {
                currentOrder = [];
                updateOrderBadge();
            });

            $('#btnTables').click(function() {
                const tableNumber = prompt("Ingrese el número de mesa:");
                if (tableNumber) {
                    currentTable = tableNumber;
                    $('#currentTable').text(tableNumber);
                    $('#tableSelect').val(tableNumber);
                }
            });

            $(document).on('click', '.remove-item', function() {
                const index = $(this).data('index');
                currentOrder.splice(index, 1);
                showOrderModal();
                updateOrderBadge();
            });

            $('#tableSelect').change(function() {
                currentTable = $(this).val();
                $('#currentTable').text(currentTable);
            });

            // Inicializar
            loadCategories();
            updateOrderBadge();
        });
    </script>
</body>
</html>